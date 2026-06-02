<script setup lang="ts">
/**
 * DataTable — Composant tableau professionnel universel
 * Style inspiré des captures : fond blanc, rows aérées, actions icônes, dropdown propre.
 */
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import * as XLSX from 'xlsx';

export interface DtColumn {
  key: string; label: string;
  sortable?: boolean; searchable?: boolean; visible?: boolean;
  editable?: boolean;
  dataType?: 'text'|'number'|'email'|'tel'|'date'|'datetime';
  align?: 'left'|'center'|'right';
  width?: string; minWidth?: string;
  cellClass?: string | ((value: unknown, row: Record<string,unknown>) => string);
  format?: (value: unknown, row: Record<string,unknown>) => string;
  exportFormat?: (value: unknown, row: Record<string,unknown>) => string;
  badge?: boolean | Record<string,string>;
  showTotal?: boolean;
  totalFormat?: (total: number) => string;
  min?: number; max?: number; maxLength?: number;
  sticky?: boolean;
  sortFn?: (a: unknown, b: unknown) => number;
}
export interface DtAction {
  key: string; label: string; icon?: string;
  variant?: 'primary'|'success'|'warning'|'danger'|'info'|'ghost';
  condition?: (row: Record<string,unknown>) => boolean;
  confirm?: string | ((row: Record<string,unknown>) => string);
}
export interface DtBulkAction {
  key: string; label: string; icon?: string;
  variant?: 'primary'|'success'|'warning'|'danger'|'info';
  confirm?: string | ((count: number) => string);
}

const props = withDefaults(defineProps<{
  rows: any[];
  columns: DtColumn[];
  rowKey?: string;
  loading?: boolean;
  emptyText?: string;
  selectable?: boolean;
  exportable?: boolean;
  exportFilename?: string;
  actions?: DtAction[];
  bulkActions?: DtBulkAction[];
  showTotals?: boolean;
  density?: 'compact'|'normal'|'comfortable';
  defaultPerPage?: number;
  perPageOptions?: number[];
  title?: string;
  showCount?: boolean;
  maxHeight?: string;
  striped?: boolean;
  bordered?: boolean;
  showResetPassword?: boolean;
  /** Activer l'édition inline persistée côté serveur (super admin) */
  inlineEdit?: boolean;
  /** URL endpoint pour persister l'édition inline (POST JSON {id, field, value}) */
  inlineEditEndpoint?: string;
  /** Clé pour l'id dans l'édition inline (défaut: rowKey || 'id') */
  inlineEditIdKey?: string;
  /** Activer le menu contextuel clic droit */
  contextMenu?: boolean;
}>(), {
  loading: false, selectable: true, exportable: true,
  exportFilename: 'export', showTotals: true, density: 'normal',
  defaultPerPage: 10, perPageOptions: () => [10,25,50,100],
  emptyText: 'Aucune donnée disponible', showCount: true,
  striped: false, bordered: false, showResetPassword: false,
  inlineEdit: false, inlineEditEndpoint: '', contextMenu: false,
});

const emit = defineEmits<{
  'action': [key: string, row: Record<string,unknown>];
  'bulk-action': [key: string, rows: Record<string,unknown>[]];
  'cell-updated': [payload: {row: Record<string,unknown>; key: string; newValue: unknown; oldValue: unknown}];
  'selection-change': [rows: Record<string,unknown>[]];
  'sort-change': [key: string, dir: 'asc'|'desc'];
  'search-change': [term: string];
  'delete': [ids: (string|number)[]];
  'reset-password': [ids: (string|number)[]];
}>();

// ── State ────────────────────────────────────────────────────────────────────
const search        = ref('');
const filterCol     = ref('');
const sortKey       = ref('');
const sortDir       = ref<'asc'|'desc'>('asc');
const currentPage   = ref(1);
const perPage       = ref(props.defaultPerPage);
const selected      = ref<(string|number)[]>([]);
const showExport    = ref(false);
const showColPicker = ref(false);
const densityMode   = ref<'compact'|'normal'|'comfortable'>(props.density);
const lastShiftIdx  = ref<number|null>(null);
const visibleKeys   = ref<string[]>(props.columns.filter(c => c.visible !== false).map(c => c.key));
const editingCell   = ref<{rowIdx: number; key: string}|null>(null);
const editValue     = ref('');
const editError     = ref('');
const editSaving    = ref(false);
const openMenuRow   = ref<string|number|null>(null);

// ── Context menu (right-click) ────────────────────────────────────────────────
const ctxMenu = ref<{show:boolean; x:number; y:number; row:Record<string,unknown>|null}>({
  show: false, x: 0, y: 0, row: null,
});

const confirmDialog = ref<{show:boolean;title:string;message:string;confirmLabel:string;variant:'danger'|'warning'|'info';onConfirm:()=>void}>({
  show:false,title:'',message:'',confirmLabel:'Confirmer',variant:'danger',onConfirm:()=>{},
});

// ── Computed ─────────────────────────────────────────────────────────────────
const visibleColumns = computed(() => props.columns.filter(c => visibleKeys.value.includes(c.key)));

const filteredRows = computed(() => {
  let data = [...props.rows];
  if (search.value.trim()) {
    // ── Multi-termes séparés par virgules ─────────────────────────────────────
    // ex: "jean, actif" → chaque terme doit matcher au moins une colonne
    const terms = search.value
      .split(',')
      .map(t => t.trim().toLowerCase())
      .filter(t => t.length > 0);

    data = data.filter(row => {
      const cols = filterCol.value
        ? visibleColumns.value.filter(c => c.key === filterCol.value)
        : visibleColumns.value.filter(c => c.searchable !== false);

      // Tous les termes doivent être trouvés (AND logique entre termes)
      return terms.every(term =>
        cols.some(col => {
          const v = row[col.key];
          if (v == null) return false;
          const str = col.format ? col.format(v, row) : String(v);
          return str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').includes(
            term.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
          );
        })
      );
    });
  }
  if (sortKey.value) {
    const col = props.columns.find(c => c.key === sortKey.value);
    data.sort((a, b) => {
      const av = a[sortKey.value] ?? ''; const bv = b[sortKey.value] ?? '';
      let cmp: number;
      if (col?.sortFn) { cmp = col.sortFn(av, bv); }
      else if (typeof av === 'number' && typeof bv === 'number') { cmp = av - bv; }
      else { cmp = String(av).localeCompare(String(bv), 'fr', {numeric:true, sensitivity:'base'}); }
      return sortDir.value === 'asc' ? cmp : -cmp;
    });
  }
  return data;
});

const totalPages    = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / perPage.value)));
const paginatedRows = computed(() => { const s=(currentPage.value-1)*perPage.value; return filteredRows.value.slice(s,s+perPage.value); });
const rangeFrom     = computed(() => filteredRows.value.length===0 ? 0 : (currentPage.value-1)*perPage.value+1);
const rangeTo       = computed(() => Math.min(currentPage.value*perPage.value, filteredRows.value.length));

const visiblePages = computed(() => {
  const total=totalPages.value, cur=currentPage.value;
  if (total<=7) return Array.from({length:total},(_,i)=>i+1);
  const pages:(number|'...')[]=[1];
  if (cur>3) pages.push('...');
  for (let i=Math.max(2,cur-1);i<=Math.min(total-1,cur+1);i++) pages.push(i);
  if (cur<total-2) pages.push('...');
  pages.push(total);
  return pages;
});

const rowId = (row: Record<string,unknown>): string|number =>
  props.rowKey ? (row[props.rowKey] as string|number) : JSON.stringify(row);
const allSelected  = computed(() => paginatedRows.value.length>0 && paginatedRows.value.every(r=>selected.value.includes(rowId(r))));
const someSelected = computed(() => paginatedRows.value.some(r=>selected.value.includes(rowId(r))) && !allSelected.value);
const selectedRows = computed(() => props.rows.filter(r=>selected.value.includes(rowId(r))));

const columnTotals = computed(() => {
  const totals: Record<string,number> = {};
  if (!props.showTotals) return totals;
  visibleColumns.value.forEach(col => {
    if (col.showTotal || col.dataType==='number') {
      totals[col.key] = filteredRows.value.reduce((sum,row) => {
        const v = parseFloat(String(row[col.key]??0));
        return sum + (isNaN(v)?0:v);
      }, 0);
    }
  });
  return totals;
});
const hasTotals = computed(() => Object.keys(columnTotals.value).length>0);

const densityClass = computed(() => ({
  compact:     'px-3 py-2 text-xs',
  normal:      'px-4 py-3.5 text-sm',
  comfortable: 'px-5 py-5 text-sm',
}[densityMode.value]));

const headerDensityClass = computed(() => ({
  compact:     'px-3 py-2.5',
  normal:      'px-4 py-3',
  comfortable: 'px-5 py-4',
}[densityMode.value]));

const totalCols = computed(() =>
  visibleColumns.value.length + (props.selectable?1:0) + (props.actions?.length?1:0) + 1
);

// ── Watchers ─────────────────────────────────────────────────────────────────
watch(search,  () => { currentPage.value=1; emit('search-change', search.value); });
watch(perPage, () => { currentPage.value=1; });
watch(selected, () => emit('selection-change', selectedRows.value));

// ── Helpers ───────────────────────────────────────────────────────────────────
const getCellValue = (row: Record<string,unknown>, col: DtColumn): string => {
  const v = row[col.key];
  if (col.format) return col.format(v, row);
  if (v==null) return '—';
  return String(v);
};

const getCellClass = (row: Record<string,unknown>, col: DtColumn): string => {
  const align = col.align==='center'?'text-center':col.align==='right'?'text-right':'text-left';
  let extra = '';
  if (typeof col.cellClass==='function') extra = col.cellClass(row[col.key], row);
  else if (col.cellClass) extra = col.cellClass;
  return `${densityClass.value} ${align} ${extra}`.trim();
};

const getBadgeClass = (value: unknown, col: DtColumn): string => {
  const v = String(value??'').toLowerCase().trim();
  if (typeof col.badge==='object') return (col.badge as Record<string,string>)[v] ?? (col.badge as Record<string,string>)['default'] ?? '';
  if (/actif|valid|pay|termin|trait|approuv|confirm|pr.sent|accept/.test(v))
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20';
  if (/attente|en cours|pending|partiel/.test(v))
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20';
  if (/inactif|refus|rejet|annul|supprim|absent|chec|suspendu|cancel/.test(v))
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-red-200 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20';
  if (/info|nouveau|brouillon/.test(v))
    return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20';
  return 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 ring-1 ring-gray-200 dark:bg-white/10 dark:text-white/60 dark:ring-white/10';
};

const getBulkActionClass = (variant: DtBulkAction['variant']='primary'): string => ({
  primary:'bg-primary-600 hover:bg-primary-700 text-white',
  success:'bg-emerald-600 hover:bg-emerald-700 text-white',
  warning:'bg-amber-500 hover:bg-amber-600 text-white',
  danger: 'bg-red-600 hover:bg-red-700 text-white',
  info:   'bg-blue-600 hover:bg-blue-700 text-white',
}[variant!]);

const formatTotal = (col: DtColumn, total: number): string =>
  col.totalFormat ? col.totalFormat(total) : new Intl.NumberFormat('fr-FR',{maximumFractionDigits:2}).format(total);

// ── Sort ──────────────────────────────────────────────────────────────────────
const toggleSort = (key: string) => {
  if (sortKey.value===key) sortDir.value = sortDir.value==='asc'?'desc':'asc';
  else { sortKey.value=key; sortDir.value='asc'; }
  emit('sort-change', sortKey.value, sortDir.value);
};

// ── Selection ─────────────────────────────────────────────────────────────────
const toggleAll = () => {
  if (allSelected.value) {
    const ids=paginatedRows.value.map(rowId);
    selected.value=selected.value.filter(id=>!ids.includes(id));
  } else {
    const ids=paginatedRows.value.map(rowId);
    selected.value=[...new Set([...selected.value,...ids])];
  }
  lastShiftIdx.value=null;
};

const toggleRow = (row: Record<string,unknown>, idx: number, event: MouseEvent) => {
  const id = rowId(row);
  if (event.shiftKey && lastShiftIdx.value!==null) {
    const [from,to]=[Math.min(lastShiftIdx.value,idx),Math.max(lastShiftIdx.value,idx)];
    const rangeIds = paginatedRows.value.slice(from,to+1).map(rowId);
    const adding = !selected.value.includes(id);
    if (adding) selected.value=[...new Set([...selected.value,...rangeIds])];
    else selected.value=selected.value.filter(i=>!rangeIds.includes(i));
  } else {
    const pos=selected.value.indexOf(id);
    if (pos===-1) selected.value.push(id); else selected.value.splice(pos,1);
    lastShiftIdx.value=idx;
  }
};

const clearSelection = () => { selected.value=[]; lastShiftIdx.value=null; };

// ── Inline edit ───────────────────────────────────────────────────────────────
const startEdit = (rowIdx: number, col: DtColumn, row: Record<string,unknown>) => {
  if (!col.editable) return;
  editingCell.value={rowIdx,key:col.key};
  editValue.value=String(row[col.key]??'');
  editError.value='';
  nextTick(()=>{ const el=document.querySelector<HTMLInputElement>('.dt-edit-input'); el?.focus(); el?.select(); });
};

const validateEdit = (col: DtColumn, raw: string): {ok:boolean;value?:unknown;msg?:string} => {
  if (!raw.trim() && col.dataType!=='number') return {ok:false,msg:'Ce champ ne peut pas être vide.'};
  if (col.dataType==='number') {
    const n=Number(raw);
    if (isNaN(n)) return {ok:false,msg:'Valeur numérique requise.'};
    if (col.min!==undefined && n<col.min) return {ok:false,msg:`Minimum : ${col.min}`};
    if (col.max!==undefined && n>col.max) return {ok:false,msg:`Maximum : ${col.max}`};
    return {ok:true,value:n};
  }
  if (col.dataType==='email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(raw)) return {ok:false,msg:'Email invalide.'};
  if (col.dataType==='tel' && !/^[0-9+\s\-]{8,15}$/.test(raw)) return {ok:false,msg:'Téléphone invalide.'};
  if (col.maxLength && raw.length>col.maxLength) return {ok:false,msg:`Maximum ${col.maxLength} caractères.`};
  return {ok:true,value:raw};
};

const saveEdit = async () => {
  if (!editingCell.value) return;
  const {rowIdx,key}=editingCell.value;
  const col=props.columns.find(c=>c.key===key)!;
  const result=validateEdit(col,editValue.value);
  if (!result.ok) { editError.value=result.msg??''; return; }
  const row=paginatedRows.value[rowIdx];
  const oldValue = row[key];
  const newValue = result.value;

  // ── Persistance côté serveur si inlineEdit activé ─────────────────────────
  if (props.inlineEdit && props.inlineEditEndpoint) {
    editSaving.value = true;
    const idKey = props.inlineEditIdKey || props.rowKey || 'id';
    const rowId_ = row[idKey];
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const csrf = csrfMeta?.content ?? '';
    try {
      const res = await fetch(props.inlineEditEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ id: rowId_, field: key, value: newValue }),
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        editError.value = data.message ?? 'Erreur serveur.';
        editSaving.value = false;
        return;
      }
      // Mettre à jour la ligne localement
      (row as any)[key] = newValue;
    } catch {
      editError.value = 'Erreur réseau.';
      editSaving.value = false;
      return;
    }
    editSaving.value = false;
  }

  emit('cell-updated',{row,key,newValue,oldValue});
  editingCell.value=null; editValue.value=''; editError.value='';
};

const cancelEdit = () => { editingCell.value=null; editValue.value=''; editError.value=''; };
const onEditKey  = (e: KeyboardEvent) => { if(e.key==='Enter') saveEdit(); if(e.key==='Escape') cancelEdit(); };

// ── Row action dropdown ───────────────────────────────────────────────────────
const toggleRowMenu = (id: string|number) => {
  openMenuRow.value = openMenuRow.value === id ? null : id;
};

// ── Context menu (right-click) ────────────────────────────────────────────────
const openContextMenu = (e: MouseEvent, row: Record<string,unknown>) => {
  if (!props.contextMenu) return;
  e.preventDefault();
  const x = Math.min(e.clientX, window.innerWidth - 210);
  const y = Math.min(e.clientY, window.innerHeight - 170);
  ctxMenu.value = { show: true, x, y, row };
};
const closeContextMenu = () => { ctxMenu.value.show = false; };
const ctxAction = (key: string) => {
  if (ctxMenu.value.row) emit('action', key, ctxMenu.value.row);
  closeContextMenu();
};

// ── Actions ───────────────────────────────────────────────────────────────────
const handleAction = (action: DtAction, row: Record<string,unknown>) => {
  openMenuRow.value = null;
  if (action.confirm) {
    const msg=typeof action.confirm==='function'?action.confirm(row):action.confirm;
    openConfirm({title:action.label,message:msg,variant:action.variant==='danger'?'danger':'warning',onConfirm:()=>emit('action',action.key,row)});
  } else { emit('action',action.key,row); }
};

const handleBulkAction = (action: DtBulkAction) => {
  const rows=selectedRows.value;
  if (action.confirm) {
    const msg=typeof action.confirm==='function'?action.confirm(rows.length):action.confirm;
    openConfirm({title:action.label,message:msg,variant:action.variant==='danger'?'danger':'warning',onConfirm:()=>{emit('bulk-action',action.key,rows);clearSelection();}});
  } else { emit('bulk-action',action.key,rows); clearSelection(); }
};

// ── Confirm ───────────────────────────────────────────────────────────────────
const openConfirm = (opts: Partial<typeof confirmDialog.value>) =>
  Object.assign(confirmDialog.value,{show:true,...opts});

// ── Export ────────────────────────────────────────────────────────────────────
const exportData = (format: 'xlsx'|'csv'|'json') => {
  showExport.value=false;
  const source=selected.value.length>0?selectedRows.value:filteredRows.value;
  const exportRows=source.map(row=>Object.fromEntries(visibleColumns.value.map(col=>{
    const v=row[col.key];
    const str=col.exportFormat?col.exportFormat(v,row):col.format?col.format(v,row):(v==null?'':String(v));
    return [col.label,str];
  })));
  const filename=`${props.exportFilename}_${new Date().toISOString().slice(0,10)}`;
  if (format==='json') {
    const blob=new Blob([JSON.stringify(exportRows,null,2)],{type:'application/json'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=`${filename}.json`; a.click(); return;
  }
  const ws=XLSX.utils.json_to_sheet(exportRows);
  ws['!cols']=visibleColumns.value.map(col=>({wch:Math.max(col.label.length,...source.map(r=>String(r[col.key]??'').length),10)}));
  const wb=XLSX.utils.book_new(); XLSX.utils.book_append_sheet(wb,ws,'Données');
  XLSX.writeFile(wb,`${filename}.${format}`);
};

// ── Lifecycle ─────────────────────────────────────────────────────────────────
const onDocClick = (e: MouseEvent) => {
  const t=e.target as HTMLElement;
  if (!t.closest('.dt-row-menu'))  openMenuRow.value=null;
  if (!t.closest('.dt-export-menu')) showExport.value=false;
  if (!t.closest('.dt-col-picker'))  showColPicker.value=false;
  if (!t.closest('.dt-ctx-menu'))    ctxMenu.value.show=false;
};
const onKeyDown = (e: KeyboardEvent) => {
  if (e.key==='Escape') {
    openMenuRow.value=null;
    showExport.value=false;
    showColPicker.value=false;
    ctxMenu.value.show=false;
  }
};
onMounted(()=>{
  document.addEventListener('click', onDocClick);
  document.addEventListener('keydown', onKeyDown);
});
onUnmounted(()=>{
  document.removeEventListener('click', onDocClick);
  document.removeEventListener('keydown', onKeyDown);
});

// ── Public API ────────────────────────────────────────────────────────────────
const confirmDelete = (id: string|number, label = 'cet élément') => {
  openConfirm({
    title: 'Supprimer',
    message: `Voulez-vous vraiment supprimer ${label} ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant: 'danger',
    onConfirm: () => emit('delete', [id]),
  });
};

const handleBulkDelete = () => {
  const ids = selectedRows.value.map(r => r[props.rowKey ?? 'id'] as string|number);
  openConfirm({
    title: 'Supprimer la sélection',
    message: `Voulez-vous vraiment supprimer ${ids.length} élément(s) ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant: 'danger',
    onConfirm: () => { emit('delete', ids); clearSelection(); },
  });
};

const handleResetPasswordBulk = () => {
  const ids = selectedRows.value.map(r => r[props.rowKey ?? 'id'] as string|number);
  openConfirm({
    title: 'Réinitialiser les mots de passe',
    message: `Réinitialiser le mot de passe de ${ids.length} utilisateur(s) ?`,
    confirmLabel: 'Réinitialiser',
    variant: 'warning',
    onConfirm: () => { emit('reset-password', ids); clearSelection(); },
  });
};

defineExpose({ clearSelection, selected, filteredRows, confirmDelete });
</script>

<template>
  <div class="dt-root w-full font-sans">

    <!-- ══════════════════════════════════════════════════════════
         TOOLBAR
    ═══════════════════════════════════════════════════════════ -->
    <div class="flex flex-wrap items-center gap-2 px-4 py-3
                bg-white dark:bg-gray-900
                border border-gray-200 dark:border-gray-700
                rounded-t-xl">

      <!-- Recherche -->
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text"
               placeholder="Rechercher… (ex: Jean, actif)"
               title="Séparez plusieurs termes par des virgules pour une recherche combinée"
               class="w-full h-9 pl-9 pr-8 text-sm rounded-lg
                      border border-gray-200 dark:border-gray-600
                      bg-white dark:bg-gray-800
                      text-gray-900 dark:text-gray-100
                      placeholder-gray-400 dark:placeholder-gray-500
                      focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-400
                      transition-colors"/>
        <button v-if="search" @click="search = ''"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                title="Effacer">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <p v-if="search.includes(',')" class="text-[11px] text-violet-500 dark:text-violet-400 whitespace-nowrap">
        {{ search.split(',').filter(t => t.trim()).length }} termes actifs
      </p>

      <!-- Filtre colonne -->
      <select v-if="columns.length > 1" v-model="filterCol"
              class="h-9 pl-3 pr-8 text-sm rounded-lg
                     border border-gray-200 dark:border-gray-600
                     bg-white dark:bg-gray-800
                     text-gray-700 dark:text-gray-300
                     focus:outline-none focus:ring-2 focus:ring-primary-500/40
                     cursor-pointer appearance-none transition-colors">
        <option value="">Toutes les colonnes</option>
        <option v-for="col in columns.filter(c => c.searchable !== false)" :key="col.key" :value="col.key">
          {{ col.label }}
        </option>
      </select>

      <!-- Compteur -->
      <span v-if="showCount"
            class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap tabular-nums">
        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ filteredRows.length.toLocaleString('fr-FR') }}</span>
        résultat{{ filteredRows.length > 1 ? 's' : '' }}
      </span>

      <div class="flex-1"/>

      <!-- Actions groupées (sélection active) -->
      <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 scale-95"
                  enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-100 ease-in"
                  leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="selected.length > 0" class="flex items-center gap-1.5">
          <span class="text-xs font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/30
                       px-2.5 py-1 rounded-full border border-primary-200 dark:border-primary-700">
            {{ selected.length }} sélectionné{{ selected.length > 1 ? 's' : '' }}
          </span>
          <button v-for="action in bulkActions" :key="action.key"
                  :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors', getBulkActionClass(action.variant)]"
                  @click="handleBulkAction(action)">
            <span v-if="action.icon" v-html="action.icon"/>
            {{ action.label }}
          </button>
          <button v-if="showResetPassword"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                         bg-amber-500 hover:bg-amber-600 text-white transition-colors"
                  @click="handleResetPasswordBulk">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Réinit. MDP
          </button>
          <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                         bg-red-600 hover:bg-red-700 text-white transition-colors"
                  @click="handleBulkDelete">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Supprimer
          </button>
          <button @click="clearSelection"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100
                         dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </Transition>

      <slot name="toolbar-actions"/>

      <!-- Lignes/page -->
      <select v-model="perPage"
              class="h-9 pl-3 pr-7 text-sm rounded-lg
                     border border-gray-200 dark:border-gray-600
                     bg-white dark:bg-gray-800
                     text-gray-700 dark:text-gray-300
                     focus:outline-none focus:ring-2 focus:ring-primary-500/40
                     cursor-pointer appearance-none transition-colors">
        <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} / page</option>
      </select>

      <!-- Densité -->
      <div class="flex items-center rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
        <button v-for="d in (['compact','normal','comfortable'] as const)" :key="d" :title="d"
                :class="['px-2 py-1.5 transition-colors',
                         densityMode === d
                           ? 'bg-primary-600 text-white'
                           : 'text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700']"
                @click="densityMode = d">
          <svg v-if="d === 'compact'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
          </svg>
          <svg v-else-if="d === 'normal'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
          </svg>
        </button>
      </div>

      <!-- Colonnes visibles -->
      <div class="relative dt-col-picker">
        <button @click.stop="showColPicker = !showColPicker"
                class="h-9 px-3 flex items-center gap-1.5 text-sm rounded-lg
                       border border-gray-200 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300
                       hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
          </svg>
          <span class="hidden sm:inline">Colonnes</span>
        </button>
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
          <div v-if="showColPicker"
               class="absolute right-0 top-full mt-1.5 w-52 z-50
                      bg-white dark:bg-gray-800
                      rounded-xl border border-gray-200 dark:border-gray-600
                      shadow-lg shadow-gray-200/60 dark:shadow-black/30 py-2">
            <div class="px-3 pb-2 mb-1 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Colonnes</span>
              <div class="flex gap-2">
                <button class="text-xs text-primary-600 dark:text-primary-400 hover:underline"
                        @click="visibleKeys = columns.map(c => c.key)">Tout</button>
                <button class="text-xs text-gray-400 hover:underline" @click="visibleKeys = []">Aucun</button>
              </div>
            </div>
            <label v-for="col in columns" :key="col.key"
                   class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
              <input type="checkbox" :checked="visibleKeys.includes(col.key)"
                     class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                     style="accent-color:#7c3aed"
                     @change="visibleKeys.includes(col.key) ? visibleKeys = visibleKeys.filter(k => k !== col.key) : visibleKeys.push(col.key)"/>
              <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ col.label }}</span>
            </label>
          </div>
        </Transition>
      </div>

      <!-- Export -->
      <div v-if="exportable" class="relative dt-export-menu">
        <button @click.stop="showExport = !showExport"
                class="h-9 px-3 flex items-center gap-1.5 text-sm rounded-lg
                       border border-gray-200 dark:border-gray-600
                       bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300
                       hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          <span class="hidden sm:inline">Exporter</span>
          <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
          <div v-if="showExport"
               class="absolute right-0 top-full mt-1.5 w-44 z-50
                      bg-white dark:bg-gray-800
                      rounded-xl border border-gray-200 dark:border-gray-600
                      shadow-lg shadow-gray-200/60 dark:shadow-black/30 py-1.5">
            <div class="px-3 py-1.5 mb-1 border-b border-gray-100 dark:border-gray-700">
              <span class="text-xs text-gray-400 dark:text-gray-500">
                {{ selected.length > 0 ? `${selected.length} sélectionné(s)` : `${filteredRows.length} ligne(s)` }}
              </span>
            </div>
            <button @click="exportData('xlsx')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
              </svg>
              Excel (.xlsx)
            </button>
            <button @click="exportData('csv')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
              </svg>
              CSV (.csv)
            </button>
            <button @click="exportData('json')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-gray-700 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343"/>
              </svg>
              JSON (.json)
            </button>
          </div>
        </Transition>
      </div>
    </div>
    <!-- /TOOLBAR -->

    <!-- ══════════════════════════════════════════════════════════
         TABLE
    ═══════════════════════════════════════════════════════════ -->
    <div class="overflow-x-auto border border-t-0 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"
         :style="maxHeight ? `max-height:${maxHeight};overflow-y:auto` : ''">
      <table class="min-w-full border-collapse" role="grid" aria-label="Tableau de données">

        <!-- THEAD -->
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-700">

            <!-- Checkbox tout sélectionner -->
            <th v-if="selectable" :class="[headerDensityClass, 'w-10 sticky left-0 bg-gray-50 dark:bg-gray-800/80 z-20']">
              <input type="checkbox" :checked="allSelected" :indeterminate="someSelected"
                     class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                     style="accent-color:#7c3aed"
                     @change="toggleAll" aria-label="Sélectionner tout"/>
            </th>

            <!-- Colonnes -->
            <th v-for="col in visibleColumns" :key="col.key"
                :class="[
                  headerDensityClass,
                  'text-[11px] font-semibold uppercase tracking-wider whitespace-nowrap select-none',
                  'text-gray-500 dark:text-gray-400',
                  col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                  col.sortable !== false ? 'cursor-pointer hover:text-gray-700 dark:hover:text-gray-200 transition-colors' : '',
                  col.sticky ? 'sticky left-0 bg-gray-50 dark:bg-gray-800/80 z-10' : '',
                ]"
                :style="col.width ? `width:${col.width}` : col.minWidth ? `min-width:${col.minWidth}` : ''"
                @click="col.sortable !== false && toggleSort(col.key)"
                :aria-sort="sortKey === col.key ? (sortDir === 'asc' ? 'ascending' : 'descending') : 'none'">
              <div class="flex items-center gap-1.5"
                   :class="col.align === 'center' ? 'justify-center' : col.align === 'right' ? 'justify-end' : ''">
                {{ col.label }}
                <span v-if="col.sortable !== false" class="flex flex-col gap-px opacity-50"
                      :class="sortKey === col.key ? 'opacity-100 text-primary-500' : ''">
                  <svg class="w-2.5 h-2.5" :class="sortKey === col.key && sortDir === 'asc' ? 'text-primary-500' : 'text-gray-400'"
                       fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5z"/></svg>
                  <svg class="w-2.5 h-2.5 -mt-1" :class="sortKey === col.key && sortDir === 'desc' ? 'text-primary-500' : 'text-gray-400'"
                       fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                </span>
              </div>
            </th>

            <!-- Actions -->
            <th v-if="actions?.length"
                :class="[headerDensityClass, 'text-[11px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right']">
              Actions
            </th>
          </tr>
        </thead>

        <!-- TBODY -->
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">

          <!-- Skeleton -->
          <template v-if="loading">
            <tr v-for="i in perPage" :key="`sk-${i}`" class="animate-pulse">
              <td v-if="selectable" :class="[densityClass, 'w-10']">
                <div class="h-4 w-4 bg-gray-200 dark:bg-gray-700 rounded"/>
              </td>
              <td v-for="col in visibleColumns" :key="col.key" :class="densityClass">
                <div class="h-3.5 bg-gray-200 dark:bg-gray-700 rounded" :style="`width:${Math.floor(Math.random()*40)+40}%`"/>
              </td>
              <td v-if="actions?.length" :class="densityClass">
                <div class="h-6 w-20 bg-gray-200 dark:bg-gray-700 rounded ml-auto"/>
              </td>
            </tr>
          </template>

          <!-- Vide -->
          <template v-else-if="!paginatedRows.length">
            <tr>
              <td :colspan="totalCols" class="px-4 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                      {{ search ? `Aucun résultat pour « ${search} »` : emptyText }}
                    </p>
                    <button v-if="search" @click="search = ''"
                            class="mt-1.5 text-xs text-primary-600 dark:text-primary-400 hover:underline">
                      Effacer la recherche
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </template>

          <!-- Données -->
          <template v-else>
            <tr v-for="(row, idx) in paginatedRows"
                :key="rowKey ? String(row[rowKey]) : idx"
                :class="[
                  'group relative transition-colors duration-100',
                  selected.includes(rowId(row))
                    ? 'bg-primary-50/60 dark:bg-primary-900/10'
                    : 'bg-white dark:bg-gray-900 hover:bg-gray-50/70 dark:hover:bg-gray-800/40',
                ]">

              <!-- Checkbox ligne -->
              <td v-if="selectable" :class="[densityClass, 'w-10']">
                <input type="checkbox"
                       :checked="selected.includes(rowId(row))"
                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                       style="accent-color:#7c3aed"
                       @change="toggleRow(row, idx, $event as MouseEvent)"
                       :aria-label="`Sélectionner la ligne ${idx + 1}`"/>
              </td>

              <!-- Cellules -->
              <td v-for="col in visibleColumns" :key="col.key"
                  :class="[getCellClass(row, col), col.sticky ? 'sticky left-0 bg-inherit z-10' : '']"
                  @dblclick="startEdit(idx, col, row)">

                <!-- Édition inline -->
                <div v-if="editingCell && editingCell.rowIdx === idx && editingCell.key === col.key"
                     class="flex flex-col gap-1">
                  <input v-model="editValue"
                         :type="col.dataType === 'number' ? 'number' : col.dataType === 'email' ? 'email' : col.dataType === 'date' ? 'date' : 'text'"
                         class="dt-edit-input w-full px-2.5 py-1.5 text-sm rounded-lg border-2
                                bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                focus:outline-none transition-colors"
                         :class="editError ? 'border-red-500' : 'border-primary-500'"
                         @keydown="onEditKey" @blur="saveEdit"/>
                  <span v-if="editError" class="text-xs text-red-500">{{ editError }}</span>
                </div>

                <!-- Affichage normal -->
                <template v-else>
                  <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]" :col="col">
                    <span v-if="col.badge" :class="getBadgeClass(row[col.key], col)">
                      {{ getCellValue(row, col) }}
                    </span>
                    <span v-else
                          class="text-gray-800 dark:text-gray-200"
                          :class="col.editable ? 'cursor-text' : ''">
                      {{ getCellValue(row, col) }}
                    </span>
                  </slot>
                </template>
              </td>

              <!-- Actions par ligne — dropdown style capture -->
              <td v-if="actions?.length" :class="[densityClass, 'text-right']">
                <slot name="actions" :row="row" :index="idx">
                  <div class="relative dt-row-menu inline-block">
                    <button @click.stop="toggleRowMenu(rowId(row))"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg
                                   text-gray-400 hover:text-gray-600 hover:bg-gray-100
                                   dark:text-gray-500 dark:hover:text-gray-300 dark:hover:bg-gray-700
                                   transition-colors"
                            aria-label="Ouvrir les actions">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                      </svg>
                    </button>

                    <!-- Dropdown menu -->
                    <Transition enter-active-class="transition duration-150 ease-out"
                                enter-from-class="opacity-0 scale-95 translate-y-1"
                                enter-to-class="opacity-100 scale-100 translate-y-0"
                                leave-active-class="transition duration-100 ease-in"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95">
                      <div v-if="openMenuRow === rowId(row)"
                           class="absolute right-0 z-50 mt-1 w-48
                                  bg-white dark:bg-gray-800
                                  rounded-xl border border-gray-200 dark:border-gray-600
                                  shadow-lg shadow-gray-200/60 dark:shadow-black/40
                                  py-1 overflow-hidden"
                           style="top: calc(100% + 4px)">
                        <template v-for="(action, aIdx) in actions" :key="action.key">
                          <!-- Séparateur avant danger -->
                          <div v-if="aIdx > 0 && action.variant === 'danger'"
                               class="my-1 border-t border-gray-100 dark:border-gray-700"/>
                          <button v-if="!action.condition || action.condition(row)"
                                  :class="[
                                    'w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm transition-colors',
                                    action.variant === 'danger'
                                      ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20'
                                      : action.variant === 'warning'
                                        ? 'text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20'
                                        : action.variant === 'success'
                                          ? 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'
                                          : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50',
                                  ]"
                                  @click="handleAction(action, row)">
                            <span v-if="action.icon" v-html="action.icon" class="w-4 h-4 flex-shrink-0"/>
                            <template v-else>
                              <svg v-if="action.variant === 'danger'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                              </svg>
                              <svg v-else-if="action.variant === 'warning'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                              </svg>
                              <svg v-else class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                              </svg>
                            </template>
                            <span class="font-medium">{{ action.label }}</span>
                          </button>
                        </template>
                      </div>
                    </Transition>
                  </div>
                </slot>
              </td>
            </tr>
          </template>
        </tbody>

        <!-- TFOOT totaux -->
        <tfoot v-if="hasTotals && !loading && paginatedRows.length > 0">
          <tr class="bg-gray-50 dark:bg-gray-800/80 border-t-2 border-gray-200 dark:border-gray-700">
            <td v-if="selectable" :class="densityClass"/>
            <td v-for="col in visibleColumns" :key="col.key"
                :class="[densityClass, 'text-right']">
              <span v-if="columnTotals[col.key] !== undefined"
                    class="text-sm font-semibold text-gray-800 dark:text-gray-200 tabular-nums">
                {{ formatTotal(col, columnTotals[col.key]) }}
              </span>
            </td>
            <td v-if="actions?.length" :class="densityClass"/>
          </tr>
        </tfoot>
      </table>
    </div>
    <!-- /TABLE -->

    <!-- ══════════════════════════════════════════════════════════
         PAGINATION
    ═══════════════════════════════════════════════════════════ -->
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3
                bg-white dark:bg-gray-900
                border border-t-0 border-gray-200 dark:border-gray-700
                rounded-b-xl">

      <!-- "1 to 10 of 2000" -->
      <p class="text-sm text-gray-500 dark:text-gray-400 tabular-nums">
        <template v-if="filteredRows.length > 0">
          {{ rangeFrom }} à {{ rangeTo }} sur
          <span class="font-semibold text-gray-700 dark:text-gray-200">{{ filteredRows.length.toLocaleString('fr-FR') }}</span>
          <template v-if="filteredRows.length !== rows.length">
            &nbsp;<span class="text-gray-400">(filtré sur {{ rows.length.toLocaleString('fr-FR') }})</span>
          </template>
        </template>
        <template v-else>Aucun résultat</template>
      </p>

      <!-- Boutons de page -->
      <div class="flex items-center gap-1">
        <!-- Première -->
        <button :disabled="currentPage <= 1"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                       disabled:opacity-30 disabled:cursor-not-allowed
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="currentPage = 1" title="Première page">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
          </svg>
        </button>

        <!-- Précédente -->
        <button :disabled="currentPage <= 1"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                       disabled:opacity-30 disabled:cursor-not-allowed
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="currentPage--" title="Page précédente">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>

        <!-- Numéros -->
        <template v-for="p in visiblePages" :key="p">
          <span v-if="p === '...'"
                class="w-8 h-8 flex items-center justify-center text-sm text-gray-400 dark:text-gray-500">
            …
          </span>
          <button v-else
                  :class="[
                    'w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                    p === currentPage
                      ? 'bg-primary-600 text-white shadow-sm'
                      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700',
                  ]"
                  @click="currentPage = p as number">
            {{ p }}
          </button>
        </template>

        <!-- Suivante -->
        <button :disabled="currentPage >= totalPages"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                       disabled:opacity-30 disabled:cursor-not-allowed
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="currentPage++" title="Page suivante">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- Dernière -->
        <button :disabled="currentPage >= totalPages"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                       disabled:opacity-30 disabled:cursor-not-allowed
                       text-gray-500 dark:text-gray-400
                       hover:bg-gray-100 dark:hover:bg-gray-700"
                @click="currentPage = totalPages" title="Dernière page">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>
    <!-- /PAGINATION -->

    <!-- ══════════════════════════════════════════════════════════
         DIALOG DE CONFIRMATION
    ═══════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                  enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
                  leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="confirmDialog.show"
             class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
             @click.self="confirmDialog.show = false">
          <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"/>
          <Transition enter-active-class="transition duration-200 ease-out"
                      enter-from-class="opacity-0 scale-95 translate-y-2"
                      enter-to-class="opacity-100 scale-100 translate-y-0">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-900
                        rounded-2xl shadow-2xl dark:shadow-black/50
                        border border-gray-200 dark:border-gray-700 overflow-hidden">
              <!-- Barre colorée -->
              <div :class="['h-1 w-full',
                            confirmDialog.variant === 'danger'  ? 'bg-red-500' :
                            confirmDialog.variant === 'warning' ? 'bg-amber-400' : 'bg-blue-500']"/>
              <div class="p-6">
                <div class="flex items-start gap-4">
                  <!-- Icône -->
                  <div :class="['w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0',
                                confirmDialog.variant === 'danger'  ? 'bg-red-100 dark:bg-red-900/30' :
                                confirmDialog.variant === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30' :
                                                                       'bg-blue-100 dark:bg-blue-900/30']">
                    <svg v-if="confirmDialog.variant === 'danger'"
                         class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <svg v-else-if="confirmDialog.variant === 'warning'"
                         class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ confirmDialog.title }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ confirmDialog.message }}</p>
                  </div>
                </div>
                <div class="flex justify-end gap-2.5 mt-6">
                  <button @click="confirmDialog.show = false"
                          class="px-4 py-2 text-sm font-medium rounded-lg
                                 border border-gray-200 dark:border-gray-600
                                 text-gray-700 dark:text-gray-300
                                 bg-white dark:bg-gray-800
                                 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Annuler
                  </button>
                  <button @click="confirmDialog.onConfirm(); confirmDialog.show = false"
                          :class="['px-4 py-2 text-sm font-semibold rounded-lg text-white transition-colors',
                                   confirmDialog.variant === 'danger'  ? 'bg-red-600 hover:bg-red-700' :
                                   confirmDialog.variant === 'warning' ? 'bg-amber-500 hover:bg-amber-600' :
                                                                          'bg-blue-600 hover:bg-blue-700']">
                    {{ confirmDialog.confirmLabel }}
                  </button>
                </div>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>
