<script setup lang="ts">
/**
 * DataTable — Composant tableau professionnel universel
 * Style inspiré des captures : fond blanc, rows aérées, actions icônes, dropdown propre.
 */
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
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
  /** Pagination serveur (objet Laravel paginator) — si fourni, désactive la pagination client */
  pagination?: { total: number; from: number; to: number; last_page: number; prev_page_url: string|null; next_page_url: string|null; links: any[] } | null;
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
  defaultPerPage: 8, perPageOptions: () => [8,15,25,50],
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
    const terms = search.value
      .split(',')
      .map(t => t.trim().toLowerCase())
      .filter(t => t.length > 0);

    const normalize = (s: string) =>
      s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    data = data.filter(row => {
      const cols = filterCol.value
        ? visibleColumns.value.filter(c => c.key === filterCol.value)
        : visibleColumns.value.filter(c => c.searchable !== false);

      // Valeur composite nom complet (toutes combinaisons possibles)
      const firstName  = normalize(String(row['name']      ?? row['first_name']  ?? ''));
      const lastName   = normalize(String(row['last_name'] ?? row['surname']     ?? ''));
      const fullName1  = `${firstName} ${lastName}`.trim();
      const fullName2  = `${lastName} ${firstName}`.trim();
      // Pareil pour le champ "apprenant" qui peut être préformaté
      const studentField = normalize(String(row['student_name'] ?? row['full_name'] ?? ''));

      return terms.every(term => {
        const t = normalize(term);
        // 1. Chercher dans la valeur composite nom complet
        if (fullName1.includes(t) || fullName2.includes(t)) return true;
        if (studentField && studentField.includes(t)) return true;
        // 2. Chercher dans chaque colonne visible
        return cols.some(col => {
          const v = row[col.key];
          if (v == null) return false;
          const str = col.format ? normalize(col.format(v, row)) : normalize(String(v));
          return str.includes(t);
        });
      });
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
    message: `Voulez-vous vraiment supprimer ${label} ? L'élément sera masqué mais conservé dans l'historique.`,
    confirmLabel: 'Supprimer',
    variant: 'danger',
    onConfirm: () => emit('delete', [id]),
  });
};

const handleBulkDelete = () => {
  const ids = selectedRows.value.map(r => r[props.rowKey ?? 'id'] as string|number);
  openConfirm({
    title: 'Supprimer la sélection',
    message: `Voulez-vous vraiment supprimer ${ids.length} élément(s) ? Ils seront masqués mais conservés dans l'historique.`,
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


//  Copie presse-papier (cellules) ─────────────────────────
const dtCopiedKey = ref<string | null>(null);
let dtCopiedTimeout: ReturnType<typeof setTimeout> | null = null;
const dtCopy = (text: string, key: string) => {
  navigator.clipboard.writeText(text).then(() => {
    dtCopiedKey.value = key;
    if (dtCopiedTimeout) clearTimeout(dtCopiedTimeout);
    dtCopiedTimeout = setTimeout(() => { dtCopiedKey.value = null; }, 1500);
  }).catch(() => {});
};

// Copie ligne entière ─────────────────────────────────────
const dtCopiedRowKey = ref<string | number | null>(null);
let dtCopiedRowTimeout: ReturnType<typeof setTimeout> | null = null;
const dtCopyRow = (row: Record<string, unknown>) => {
  const text = visibleColumns.value
    .map(col => {
      const v = row[col.key];
      const str = col.format ? col.format(v, row) : (v == null ? '' : String(v));
      return `${col.label}: ${str}`;
    })
    .join(' | ');
  navigator.clipboard.writeText(text).then(() => {
    dtCopiedRowKey.value = rowId(row);
    if (dtCopiedRowTimeout) clearTimeout(dtCopiedRowTimeout);
    dtCopiedRowTimeout = setTimeout(() => { dtCopiedRowKey.value = null; }, 1500);
  }).catch(() => {});
};
defineExpose({ clearSelection, selected, filteredRows, confirmDelete });
</script>

<template>
  <!-- --dt-border adapts to dark mode via CSS -->
  <div class="dt-root w-full font-sans">

    <!-- ══════════════════════════════════════════════════════════
         TOOLBAR
    ═══════════════════════════════════════════════════════════ -->
    <div class="flex flex-wrap items-center gap-2 px-4 py-3
                bg-white dark:bg-gray-800/80
                border-b border-gray-200 dark:border-gray-700/60">

      <!-- Recherche -->
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input v-model="search" type="text"
               placeholder="Rechercher… "
               title="Séparez plusieurs termes par des virgules pour une recherche combinée"
               class="w-full h-9 pl-9 pr-8 text-sm rounded-lg
                      border border-gray-200 dark:border-gray-600/60
                      bg-gray-50 dark:bg-gray-700/60
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
      <p v-if="search.includes(',')" class="text-[11px] text-primary-500 dark:text-primary-400 whitespace-nowrap">
        {{ search.split(',').filter(t => t.trim()).length }} termes actifs
      </p>

      <!-- Filtre colonne -->
      <select v-if="columns.length > 1" v-model="filterCol"
              class="h-9 pl-3 pr-8 text-sm rounded-lg
                     border border-gray-200 dark:border-gray-600/60
                     bg-gray-50 dark:bg-gray-700/60
                     text-gray-700 dark:text-gray-300
                     focus:outline-none focus:ring-2 focus:ring-violet-500/40
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
                     border border-gray-200 dark:border-gray-600/60
                     bg-gray-50 dark:bg-gray-700/60
                     text-gray-700 dark:text-gray-300
                     focus:outline-none focus:ring-2 focus:ring-violet-500/40
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
                       border border-gray-200 dark:border-gray-600/60
                       bg-gray-50 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
                      rounded-xl border border-gray-200 dark:border-gray-600/60
                      shadow-lg shadow-gray-200/60 dark:shadow-black/40 py-2">
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
                     style="accent-color:#7B74F0"
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
                       border border-gray-200 dark:border-gray-600/60
                       bg-gray-50 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300
                       hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
                      rounded-xl border border-gray-200 dark:border-gray-600/60
                      shadow-lg shadow-gray-200/60 dark:shadow-black/40 py-1.5">
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
    <div class="dt-table-wrap overflow-x-auto bg-white dark:bg-gray-800"
         :style="maxHeight ? `max-height:${maxHeight};overflow-y:auto` : ''">
      <table class="min-w-full" role="grid" aria-label="Tableau de données"
             style="border-collapse:separate; border-spacing:0;">


        <!-- THEAD — sobre, gris clair, sans couleur de fond violette -->
        <thead class="sticky top-0 z-10">
          <tr class="dt-thead-row bg-gray-50 dark:bg-gray-900/80 border-b-2 border-gray-300 dark:border-gray-600">

            <th v-if="selectable" :class="[headerDensityClass, 'w-10 bg-gray-50 dark:bg-gray-900/80']">
              <input type="checkbox" :checked="allSelected" :indeterminate="someSelected"
                     class="w-4 h-4 rounded cursor-pointer"
                     style="accent-color:#7c3aed;"
                     @change="toggleAll" aria-label="Sélectionner tout"/>
            </th>

            <th v-for="col in visibleColumns" :key="col.key"
                :class="[
                  headerDensityClass,
                  'bg-gray-50 dark:bg-gray-900/80',
                  'text-[12px] font-bold uppercase tracking-wider whitespace-nowrap select-none',
                  'text-gray-600 dark:text-gray-300',
                  col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                  col.sortable !== false ? 'cursor-pointer hover:text-gray-900 dark:hover:text-white transition-colors' : '',
                ]"
                :style="col.width ? `width:${col.width};` : col.minWidth ? `min-width:${col.minWidth};` : ''"
                @click="col.sortable !== false && toggleSort(col.key)"
                :aria-sort="sortKey === col.key ? (sortDir === 'asc' ? 'ascending' : 'descending') : 'none'">
              <div class="flex items-center gap-1.5 group/hdr"
                   :class="col.align === 'center' ? 'justify-center' : col.align === 'right' ? 'justify-end' : ''">
                <span :class="sortKey === col.key ? 'text-violet-600 dark:text-violet-400' : ''">
                  {{ col.label }}
                </span>
                <!-- Flèches de tri — chevrons simples nets -->
                <span v-if="col.sortable !== false"
                      class="flex flex-col gap-[1px] ml-0.5 transition-opacity duration-150"
                      :class="sortKey === col.key ? 'opacity-100' : 'opacity-30 group-hover/hdr:opacity-60'">
                  <!-- Chevron haut -->
                  <svg class="w-3 h-3"
                       :class="sortKey === col.key && sortDir === 'asc' ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400'"
                       fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                  </svg>
                  <!-- Chevron bas -->
                  <svg class="w-3 h-3"
                       :class="sortKey === col.key && sortDir === 'desc' ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400'"
                       fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </span>
              </div>
            </th>

            <th v-if="actions?.length || $slots['actions']"
                :class="[headerDensityClass, 'bg-gray-50 dark:bg-gray-900/80 text-[12px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 text-right']">
              Actions
            </th>
          </tr>
        </thead>


        <!-- TBODY -->
        <tbody>

          <!-- Skeleton -->
          <template v-if="loading">
            <tr v-for="i in perPage" :key="`sk-${i}`"
                class="dt-data-row animate-pulse"
                :class="i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900/50' : 'bg-white dark:bg-gray-800'">
              <td v-if="selectable" :class="[densityClass, 'w-10 border-b border-gray-100 dark:border-gray-700/60']">
                <div class="h-4 w-4 bg-gray-200 dark:bg-gray-700 rounded"/>
              </td>
              <td v-for="col in visibleColumns" :key="col.key" :class="[densityClass, 'border-b border-gray-100 dark:border-gray-700/60']">
                <div class="h-3.5 bg-gray-200 dark:bg-gray-700 rounded" :style="`width:${40+(i*13%40)}%`"/>
              </td>
              <td v-if="actions?.length || $slots['actions']" :class="[densityClass, 'border-b border-gray-100 dark:border-gray-700/60']">
                <div class="h-7 w-24 bg-gray-200 dark:bg-gray-700 rounded ml-auto"/>
              </td>
            </tr>
          </template>

          <!-- Vide -->
          <template v-else-if="!paginatedRows.length">
            <tr class="bg-white dark:bg-gray-800">
              <td :colspan="totalCols" class="px-4 py-16 text-center bg-white dark:bg-gray-800">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                      {{ search ? `Aucun r�sultat pour  ${search} ` : emptyText }}
                    </p>
                    <p v-if="search && search.includes(',')" class="text-xs text-gray-400 mt-0.5">Tous les termes doivent correspondre simultan�ment</p>
                    <button v-if="search" @click="search = ''" class="mt-2 text-xs font-medium text-violet-600 dark:text-violet-400 hover:underline">
                      Effacer la recherche
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </template>
          <!-- Lignes de donn�es -->
          <template v-else>
            <tr v-for="(row, idx) in paginatedRows"
                :key="rowKey ? String(row[rowKey]) : idx"
                class="dt-data-row"
                :class="selected.includes(rowId(row))
                  ? '!bg-violet-50 dark:!bg-violet-900/20'
                  : idx % 2 === 0
                    ? 'bg-white dark:bg-gray-800'
                    : 'bg-gray-50/80 dark:bg-gray-900/50'"
                @contextmenu="contextMenu && openContextMenu($event, row)">

              <!-- Checkbox -->
              <td v-if="selectable" :class="[densityClass, 'w-10 border-b border-gray-100 dark:border-gray-700/60']">
                <input type="checkbox"
                       :checked="selected.includes(rowId(row))"
                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 cursor-pointer"
                       style="accent-color:#7c3aed"
                       @change="toggleRow(row, idx, $event as MouseEvent)"
                       :aria-label="`S�lectionner la ligne ${idx + 1}`"/>
              </td>

              <!-- Cellules -->
              <td v-for="col in visibleColumns" :key="col.key"
                  :class="[getCellClass(row, col), 'border-b border-gray-100 dark:border-gray-700/60']"
                  :style="col.editable && inlineEdit ? 'cursor:text;' : ''"
                  @dblclick="inlineEdit && col.editable && startEdit(idx, col, row)"
                  :title="col.editable && inlineEdit ? 'Double-clic pour modifier' : ''">

                <!-- Spinner sauvegarde -->
                <div v-if="editSaving && editingCell?.rowIdx === idx && editingCell?.key === col.key"
                     class="flex items-center gap-1.5 text-violet-500">
                  <svg class="w-3.5 h-3.5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                  </svg>
                  <span class="text-xs">Enregistrement</span>
                </div>

                <!-- Input �dition inline -->
                <div v-else-if="editingCell && editingCell.rowIdx === idx && editingCell.key === col.key"
                     class="flex flex-col gap-1 min-w-[120px]">
                  <input v-model="editValue"
                         :type="col.dataType === 'number' ? 'number' : col.dataType === 'email' ? 'email' : col.dataType === 'date' ? 'date' : 'text'"
                         class="dt-edit-input w-full px-2.5 py-1.5 text-sm rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none shadow-sm"
                         :class="editError ? 'border-2 border-red-500' : 'border-2 border-violet-500 focus:ring-2 focus:ring-violet-300/30'"
                         @keydown="onEditKey" @blur="saveEdit"/>
                  <span v-if="editError" class="text-xs text-red-500 font-medium">{{ editError }}</span>
                  <span class="text-[10px] text-gray-400"> valider  �chap annuler</span>
                </div>

                <!-- Affichage normal -->
                <template v-else>
                  <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]" :col="col">
                    <span v-if="col.badge" :class="getBadgeClass(row[col.key], col)">{{ getCellValue(row, col) }}</span>
                    <span v-else class="flex items-center gap-1 text-gray-900 dark:text-gray-100 font-medium group/cell">
                      {{ getCellValue(row, col) }}
                      <svg v-if="col.editable && inlineEdit"
                           class="w-3 h-3 text-violet-400 opacity-0 group-hover/cell:opacity-100 flex-shrink-0 transition-opacity duration-150"
                           fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      <button v-if="row[col.key] != null && String(row[col.key]).trim() !== ''"
                              type="button"
                              class="transition-all duration-150 p-0.5 rounded flex-shrink-0 cursor-pointer
                                     text-gray-300 dark:text-gray-600
                                     hover:text-violet-600 dark:hover:text-violet-400
                                     hover:bg-violet-50 dark:hover:bg-violet-900/20"
                              :class="dtCopiedKey === rowId(row) + '-' + col.key
                                ? 'opacity-100 text-emerald-500 dark:text-emerald-400'
                                : 'opacity-40 group-hover/cell:opacity-100'"
                              :title="dtCopiedKey === rowId(row) + '-' + col.key ? 'Copié !' : 'Copier cette cellule'"
                              @click.stop="dtCopy(String(row[col.key]), rowId(row) + '-' + col.key)">
                        <svg v-if="dtCopiedKey !== rowId(row) + '-' + col.key" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <svg v-else class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                      </button>
                    </span>
                  </slot>
                </template>
              </td>
              <!-- Actions dropdown -->
              <td v-if="actions?.length || $slots['actions']"
                  :class="[densityClass, 'text-right border-b border-gray-100 dark:border-gray-700/60']">
                <slot name="actions" :row="row" :index="idx">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Bouton copier la ligne -->
                    <button
                        :title="dtCopiedRowKey === rowId(row) ? 'Ligne copiée !' : 'Copier la ligne'"
                        class="w-8 h-8 inline-flex items-center justify-center rounded-xl transition-all duration-150 flex-shrink-0"
                        :class="dtCopiedRowKey === rowId(row)
                          ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'
                          : 'bg-gray-100 text-gray-400 hover:bg-violet-100 hover:text-violet-600 dark:bg-gray-700/50 dark:text-gray-500 dark:hover:bg-violet-900/30 dark:hover:text-violet-400'"
                        @click.stop="dtCopyRow(row)">
                      <svg v-if="dtCopiedRowKey !== rowId(row)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </button>
                    <template v-for="action in actions" :key="action.key">
                      <button v-if="!action.condition || action.condition(row)"
                              :title="action.label"
                              class="w-8 h-8 inline-flex items-center justify-center rounded-xl transition-all duration-150 flex-shrink-0"
                              :class="{
                                'bg-violet-100 text-violet-600 hover:bg-violet-600 hover:text-white dark:bg-violet-900/30 dark:text-violet-400 dark:hover:bg-violet-600 dark:hover:text-white': !action.variant || action.variant === 'primary',
                                'bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white dark:bg-amber-900/30 dark:text-amber-400 dark:hover:bg-amber-500 dark:hover:text-white': action.variant === 'warning',
                                'bg-red-100 text-red-600 hover:bg-red-500 hover:text-white dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white': action.variant === 'danger',
                                'bg-emerald-100 text-emerald-600 hover:bg-emerald-500 hover:text-white dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-500 dark:hover:text-white': action.variant === 'success',
                                'bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white': action.variant === 'info',
                              }"
                              @click="handleAction(action, row)">
                        <!-- Icône selon le key -->
                        <svg v-if="action.key === 'view' || action.key === 'show'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg v-else-if="action.key === 'edit' || action.key === 'update'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <svg v-else-if="action.key === 'assign' || action.key === 'permissions'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <svg v-else-if="action.key === 'delete' || action.key === 'remove'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <svg v-else-if="action.key === 'restore'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <!-- Icône générique si key inconnu -->
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                      </button>
                    </template>
                  </div>
                </slot>
              </td>
            </tr>
          </template>

          <!-- Totaux -->
          <tr v-if="hasTotals && !loading && paginatedRows.length > 0"
              class="bg-gray-50 dark:bg-gray-900/60 font-semibold border-t-2 border-gray-200 dark:border-gray-700/60">
            <td v-if="selectable" :class="densityClass"/>
            <td v-for="col in visibleColumns" :key="col.key"
                :class="[densityClass, col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : '']">
              <span v-if="columnTotals[col.key] !== undefined" class="text-sm font-bold text-gray-700 dark:text-gray-200 tabular-nums">
                {{ formatTotal(col, columnTotals[col.key]) }}
              </span>
            </td>
            <td v-if="actions?.length || $slots['actions']" :class="densityClass"/>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- /TABLE -->    <!-- /TABLE -->

    <!-- Context menu (clic droit) -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 scale-95"
                  enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
        <div v-if="ctxMenu.show" class="dt-ctx-menu fixed z-[9999] w-52
                    bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                    rounded-xl shadow-2xl shadow-gray-300/60 dark:shadow-black/50 py-1.5 overflow-hidden"
             :style="`left:${ctxMenu.x}px; top:${ctxMenu.y}px;`">
          <div class="px-3 py-1.5 mb-1 border-b border-gray-100 dark:border-gray-700">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Actions rapides</span>
          </div>
          <slot name="context-menu" :row="ctxMenu.row">
            <template v-for="action in actions" :key="action.key">
              <button v-if="!action.condition || (ctxMenu.row && action.condition(ctxMenu.row))"
                      class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium transition-colors text-left"
                      :class="{
                        'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20': action.variant === 'danger',
                        'text-gray-700 hover:bg-gray-50 hover:text-violet-700 dark:text-gray-300 dark:hover:bg-gray-700/60': !action.variant || action.variant !== 'danger',
                      }"
                      @click="ctxAction(action.key)">
                <span v-if="action.icon" v-html="action.icon" class="w-4 h-4 flex-shrink-0"/>
                {{ action.label }}
              </button>
            </template>
          </slot>
        </div>
      </Transition>
    </Teleport>
    <!-- PAGINATION -->
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3
                bg-white dark:bg-gray-800/80
                border-t border-gray-200 dark:border-gray-700/60">

      <!-- ── Pagination SERVEUR (Laravel paginator) ── -->
      <template v-if="pagination">
        <p class="text-sm text-gray-500 dark:text-gray-400 tabular-nums">
          <template v-if="pagination.total > 0">
            {{ pagination.from }}–{{ pagination.to }} sur
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ pagination.total.toLocaleString('fr-FR') }}</span>
          </template>
          <template v-else>Aucun résultat</template>
        </p>
        <div class="flex items-center gap-1">
          <!-- Précédent -->
          <button :disabled="!pagination.prev_page_url"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="pagination.prev_page_url && router.visit(pagination.prev_page_url, { preserveState: true })">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <!-- Numéros -->
          <template v-for="link in pagination.links.slice(1, -1)" :key="link.label">
            <button
              @click="link.url && router.visit(link.url, { preserveState: true })"
              :class="['w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                       link.active
                         ? 'bg-violet-600 text-white shadow-sm'
                         : link.url
                           ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
                           : 'text-gray-300 dark:text-gray-600 cursor-not-allowed']">
              {{ link.label }}
            </button>
          </template>
          <!-- Suivant -->
          <button :disabled="!pagination.next_page_url"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="pagination.next_page_url && router.visit(pagination.next_page_url, { preserveState: true })">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </template>

      <!-- ── Pagination CLIENT (locale) ── -->
      <template v-else>
        <p class="text-sm text-gray-500 dark:text-gray-400 tabular-nums">
          <template v-if="filteredRows.length > 0">
            {{ rangeFrom }} a {{ rangeTo }} sur
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ filteredRows.length.toLocaleString('fr-FR') }}</span>
            <template v-if="filteredRows.length !== rows.length">
              &nbsp;<span class="text-gray-400">(filtre sur {{ rows.length.toLocaleString('fr-FR') }})</span>
            </template>
          </template>
          <template v-else>Aucun resultat</template>
        </p>
        <div class="flex items-center gap-1">
          <button :disabled="currentPage <= 1"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="currentPage = 1" title="Premiere page">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
          </button>
          <button :disabled="currentPage <= 1"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="currentPage--">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <template v-for="p in visiblePages" :key="p">
            <span v-if="p === '...'" class="w-8 h-8 flex items-center justify-center text-sm text-gray-400">...</span>
            <button v-else
                    :class="['w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors',
                             p === currentPage ? 'bg-violet-600 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700']"
                    @click="currentPage = p as number">{{ p }}</button>
          </template>
          <button :disabled="currentPage >= totalPages"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="currentPage++">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
          <button :disabled="currentPage >= totalPages"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors
                         disabled:opacity-30 disabled:cursor-not-allowed text-gray-500 dark:text-gray-400
                         hover:bg-gray-100 dark:hover:bg-gray-700"
                  @click="currentPage = totalPages">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </template>
    </div>
    <!-- Dialog de confirmation -->
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
              <div :class="['h-1 w-full',
                            confirmDialog.variant === 'danger'  ? 'bg-red-500' :
                            confirmDialog.variant === 'warning' ? 'bg-amber-400' : 'bg-blue-500']"/>
              <div class="p-6">
                <div class="flex items-start gap-4">
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
                          class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-600
                                 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800
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

<style scoped>
/* ═══════════════════════════════════════════════════════════════
   DataTable — Styles hover, transitions, coins arrondis
   Le fond et les borders sont gérés par les classes Tailwind dark:
   dans le template directement.
   ═══════════════════════════════════════════════════════════════ */

/* ── Wrapper global — coins arrondis + bordure ────────────────── */
.dt-root {
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
}

/* ── Lignes — transitions ─────────────────────────────────────── */
.dt-data-row {
  position: relative;
  transition: background-color 0.12s ease, box-shadow 0.15s ease, transform 0.12s ease;
}

/* Hover : surbrillance indigo subtile + micro-élévation */
.dt-data-row:hover {
  background-color: #eef2ff !important;   /* indigo-50 */
  box-shadow: 0 2px 8px rgba(99,102,241,0.10), 0 1px 2px rgba(0,0,0,0.04);
  transform: translateY(-0.5px);
  z-index: 2;
}

/* Édition inline */
.dt-edit-input {
  animation: dtEditIn 0.12s ease;
}
@keyframes dtEditIn {
  from { opacity: 0; transform: scaleY(0.88); }
  to   { opacity: 1; transform: scaleY(1); }
}

/* ── Scrollbar fine ──────────────────────────────────────────── */
.dt-table-wrap::-webkit-scrollbar { height: 4px; }
.dt-table-wrap::-webkit-scrollbar-track { background: transparent; }
.dt-table-wrap::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
</style>

<!-- Styles globaux pour le dark mode (ne peuvent pas être scopés car .dark est sur <html>) -->
<style>
.dark .dt-root {
  border-color: #374151 !important;
  box-shadow: 0 1px 3px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.3) !important;
}
.dark .dt-data-row:hover {
  background-color: #1e3a5f !important;
  box-shadow: 0 2px 8px rgba(99,102,241,0.20), 0 1px 2px rgba(0,0,0,0.2) !important;
}
.dark .dt-table-wrap::-webkit-scrollbar-thumb {
  background: #374151;
}
</style>
