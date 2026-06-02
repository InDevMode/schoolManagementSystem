<script setup lang="ts">
/**
 * DataTable — Composant tableau professionnel universel
 * Fonctionnalités : tri, recherche, pagination, sélection (shift+clic),
 * édition inline, totaux dynamiques, export XLSX/CSV/JSON,
 * visibilité colonnes, densité, menu contextuel, dark mode, a11y.
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
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
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
  /** Affiche le bouton "Réinitialiser MDP" dans les actions groupées */
  showResetPassword?: boolean;
}>(), {
  loading: false, selectable: true, exportable: true,
  exportFilename: 'export', showTotals: true, density: 'normal',
  defaultPerPage: 10, perPageOptions: () => [10,25,50,100],
  emptyText: 'Aucune donnée disponible', showCount: true,
  striped: false, bordered: false, showResetPassword: false,
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

//  State 
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
const ctxMenu       = ref<{show:boolean;x:number;y:number;row:Record<string,unknown>|null}>({show:false,x:0,y:0,row:null});
const confirmDialog = ref<{show:boolean;title:string;message:string;confirmLabel:string;variant:'danger'|'warning'|'info';onConfirm:()=>void}>({
  show:false,title:'',message:'',confirmLabel:'Confirmer',variant:'danger',onConfirm:()=>{},
});

//  Computed 
const visibleColumns = computed(() => props.columns.filter(c => visibleKeys.value.includes(c.key)));

const filteredRows = computed(() => {
  let data = [...props.rows];
  if (search.value.trim()) {
    const q = search.value.toLowerCase();
    data = data.filter(row => {
      const cols = filterCol.value
        ? visibleColumns.value.filter(c => c.key === filterCol.value)
        : visibleColumns.value.filter(c => c.searchable !== false);
      return cols.some(col => {
        const v = row[col.key];
        if (v == null) return false;
        const str = col.format ? col.format(v, row) : String(v);
        return str.toLowerCase().includes(q);
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

const densityClass = computed(() => ({compact:'px-3 py-2 text-xs',normal:'px-4 py-3.5 text-sm',comfortable:'px-5 py-5 text-sm'}[densityMode.value]));
const headerDensityClass = computed(() => ({compact:'px-3 py-2.5 text-xs',normal:'px-4 py-3.5 text-xs',comfortable:'px-5 py-4 text-xs'}[densityMode.value]));
const totalCols = computed(() => visibleColumns.value.length + (props.selectable?1:0) + (props.actions?.length?1:0) + 1);

//  Watchers 
watch(search, () => { currentPage.value=1; emit('search-change', search.value); });
watch(perPage, () => { currentPage.value=1; });
watch(selected, () => emit('selection-change', selectedRows.value));

//  Helpers 
const getCellValue = (row: Record<string,unknown>, col: DtColumn): string => {
  const v = row[col.key];
  if (col.format) return col.format(v, row);
  if (v==null) return '';
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
    return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-500 dark:text-white shadow-sm';
  if (/attente|en cours|pending|partiel/.test(v))
    return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white dark:bg-amber-500 dark:text-white shadow-sm';
  if (/inactif|refus|rejet|annul|supprim|absent|chec|suspendu|cancel/.test(v))
    return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white dark:bg-red-500 dark:text-white shadow-sm';
  if (/info|nouveau|brouillon/.test(v))
    return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500 text-white dark:bg-blue-500 dark:text-white shadow-sm';
  return 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-400 text-white dark:bg-white/20 dark:text-white/80 shadow-sm';
};

const getActionClass = (variant: DtAction['variant']='ghost'): string => ({
  primary:'text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/20',
  success:'text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20',
  warning:'text-amber-600 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/20',
  danger:'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20',
  info:'text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20',
  ghost:'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700',
}[variant!]);

const getBulkActionClass = (variant: DtBulkAction['variant']='primary'): string => ({
  primary:'bg-primary-600 hover:bg-primary-700 text-white',
  success:'bg-emerald-600 hover:bg-emerald-700 text-white',
  warning:'bg-amber-500 hover:bg-amber-600 text-white',
  danger:'bg-red-600 hover:bg-red-700 text-white',
  info:'bg-blue-600 hover:bg-blue-700 text-white',
}[variant!]);

const formatTotal = (col: DtColumn, total: number): string =>
  col.totalFormat ? col.totalFormat(total) : new Intl.NumberFormat('fr-FR',{maximumFractionDigits:2}).format(total);

//  Sort 
const toggleSort = (key: string) => {
  if (sortKey.value===key) sortDir.value = sortDir.value==='asc'?'desc':'asc';
  else { sortKey.value=key; sortDir.value='asc'; }
  emit('sort-change', sortKey.value, sortDir.value);
};

//  Selection 
const toggleAll = () => {
  if (allSelected.value) { const ids=paginatedRows.value.map(rowId); selected.value=selected.value.filter(id=>!ids.includes(id)); }
  else { const ids=paginatedRows.value.map(rowId); selected.value=[...new Set([...selected.value,...ids])]; }
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

//  Inline edit 
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

const saveEdit = () => {
  if (!editingCell.value) return;
  const {rowIdx,key}=editingCell.value;
  const col=props.columns.find(c=>c.key===key)!;
  const result=validateEdit(col,editValue.value);
  if (!result.ok) { editError.value=result.msg??''; return; }
  const row=paginatedRows.value[rowIdx];
  emit('cell-updated',{row,key,newValue:result.value,oldValue:row[key]});
  editingCell.value=null; editValue.value=''; editError.value='';
};

const cancelEdit = () => { editingCell.value=null; editValue.value=''; editError.value=''; };
const onEditKey  = (e: KeyboardEvent) => { if(e.key==='Enter') saveEdit(); if(e.key==='Escape') cancelEdit(); };

//  Actions 
const handleAction = (action: DtAction, row: Record<string,unknown>) => {
  ctxMenu.value.show=false;
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

//  Confirm 
const openConfirm = (opts: Partial<typeof confirmDialog.value>) => Object.assign(confirmDialog.value,{show:true,...opts});

//  Context menu 
const CTX_MENU_W = 220;
const CTX_MENU_H = 60 + (props.actions?.length ?? 0) * 40; // estimation hauteur

const openCtxMenu = (e: MouseEvent, row: Record<string,unknown>) => {
  if (!props.actions?.length) return;
  e.preventDefault();
  const vw = window.innerWidth;
  const vh = window.innerHeight;
  // Repositionner si le menu déborde à droite ou en bas
  const x = e.clientX + CTX_MENU_W > vw ? e.clientX - CTX_MENU_W : e.clientX;
  const y = e.clientY + CTX_MENU_H > vh ? e.clientY - CTX_MENU_H : e.clientY;
  ctxMenu.value = { show: true, x: Math.max(4, x), y: Math.max(4, y), row };
};
const closeCtxMenu = () => { ctxMenu.value.show=false; };

//  Export 
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

//  Lifecycle 
const onDocClick = (e: MouseEvent) => {
  const t=e.target as HTMLElement;
  if (!t.closest('.dt-ctx-menu')) closeCtxMenu();
  if (!t.closest('.dt-export-menu')) showExport.value=false;
  if (!t.closest('.dt-col-picker')) showColPicker.value=false;
};
const onKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') { closeCtxMenu(); showExport.value=false; showColPicker.value=false; }
};
const onScroll = () => { closeCtxMenu(); };
onMounted(()=>{
  document.addEventListener('click', onDocClick);
  document.addEventListener('keydown', onKeyDown);
  window.addEventListener('scroll', onScroll, true);
});
onUnmounted(()=>{
  document.removeEventListener('click', onDocClick);
  document.removeEventListener('keydown', onKeyDown);
  window.removeEventListener('scroll', onScroll, true);
});

// ─── Méthode publique confirmDelete (compatibilité avec les pages existantes) ──
const confirmDelete = (id: string | number, label = 'cet élément') => {
  openConfirm({
    title: 'Supprimer',
    message: `Voulez-vous vraiment supprimer ${label} ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant: 'danger',
    onConfirm: () => emit('delete', [id]),
  });
};

// ─── Suppression groupée ──────────────────────────────────────────────────────
const handleBulkDelete = () => {
  const ids = selectedRows.value.map(r => r[props.rowKey ?? 'id'] as string | number);
  openConfirm({
    title: 'Supprimer la sélection',
    message: `Voulez-vous vraiment supprimer ${ids.length} élément(s) ? Cette action est irréversible.`,
    confirmLabel: 'Supprimer',
    variant: 'danger',
    onConfirm: () => { emit('delete', ids); clearSelection(); },
  });
};

// ─── Réinitialisation MDP groupée ────────────────────────────────────────────
const handleResetPasswordBulk = () => {
  const ids = selectedRows.value.map(r => r[props.rowKey ?? 'id'] as string | number);
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
  <div class="dt-root flex flex-col w-full font-sans select-none">

    <!-- ═══ TOOLBAR ═══ -->
    <div class="dt-toolbar flex flex-wrap items-center gap-2 px-3 py-2.5
                bg-white dark:bg-[#1c1c2e]
                border-b border-gray-200 dark:border-white/[0.06]
                rounded-t-2xl">

      <!-- Titre + compteur -->
      <div v-if="title || showCount" class="flex items-center gap-2.5 mr-auto min-w-0">
        <h3 v-if="title" class="text-sm font-bold text-gray-800 dark:text-white/90 truncate tracking-tight">{{ title }}</h3>
        <span v-if="showCount"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                     bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-300
                     border border-primary-200/60 dark:border-primary-500/30 tabular-nums">
          {{ filteredRows.length.toLocaleString('fr-FR') }} ligne{{ filteredRows.length > 1 ? 's' : '' }}
        </span>
      </div>

      <!-- Recherche -->
      <div class="relative flex items-center gap-1.5 flex-1 min-w-[180px] max-w-sm">
        <select v-if="columns.length > 1" v-model="filterCol"
                class="h-8 pl-2.5 pr-6 text-xs rounded-xl border border-gray-200 dark:border-white/10
                       bg-gray-50 dark:bg-white/[0.06] text-gray-600 dark:text-white/60
                       focus:outline-none focus:ring-2 focus:ring-primary-500/50 cursor-pointer appearance-none
                       transition-colors hover:border-gray-300 dark:hover:border-white/20">
          <option value="">Toutes</option>
          <option v-for="col in columns.filter(c => c.searchable !== false)" :key="col.key" :value="col.key">
            {{ col.label }}
          </option>
        </select>
        <div class="relative flex-1">
          <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 dark:text-white/30 pointer-events-none"
               fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" type="text" placeholder="Rechercher…"
                 class="w-full h-8 pl-8 pr-8 text-xs rounded-xl border border-gray-200 dark:border-white/10
                        bg-gray-50 dark:bg-white/[0.06] text-gray-900 dark:text-white/80
                        placeholder-gray-400 dark:placeholder-white/25
                        focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-400 dark:focus:border-primary-500/50
                        transition-all hover:border-gray-300 dark:hover:border-white/20"/>
          <button v-if="search" @click="search = ''"
                  class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 dark:text-white/30 hover:text-gray-600 dark:hover:text-white/60 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Actions groupées -->
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
                  enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
                  leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="selected.length > 0" class="flex items-center gap-1.5 flex-wrap">
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                       bg-primary-100 text-primary-700 dark:bg-primary-500/20 dark:text-primary-300
                       border border-primary-200/60 dark:border-primary-500/30 whitespace-nowrap">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            {{ selected.length }} sél.
          </span>
          <button v-for="action in bulkActions" :key="action.key"
                  :class="['inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-150 shadow-sm', getBulkActionClass(action.variant)]"
                  @click="handleBulkAction(action)">
            <span v-if="action.icon" v-html="action.icon"/>
            {{ action.label }}
          </button>

          <!-- Bouton réinitialiser MDP (si showResetPassword) -->
          <button v-if="showResetPassword"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                         bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white transition-all duration-150 shadow-sm"
                  @click="handleResetPasswordBulk">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Réinit. MDP
          </button>

          <!-- Bouton supprimer groupé -->
          <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold
                         bg-red-600 hover:bg-red-700 active:bg-red-800 text-white transition-all duration-150 shadow-sm"
                  @click="handleBulkDelete">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Supprimer
          </button>

          <button @click="clearSelection"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100
                         dark:hover:text-gray-200 dark:hover:bg-white/10 transition-all duration-150">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </Transition>

      <slot name="toolbar-actions"/>

      <!-- Lignes/page -->
      <select v-model="perPage"
              class="h-8 pl-2.5 pr-6 text-xs rounded-xl border border-gray-200 dark:border-white/10
                     bg-gray-50 dark:bg-white/[0.06] text-gray-700 dark:text-white/70
                     focus:outline-none focus:ring-2 focus:ring-primary-500/50 cursor-pointer appearance-none
                     transition-colors hover:border-gray-300 dark:hover:border-white/20">
        <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} / page</option>
      </select>

      <!-- Densité -->
      <div class="flex items-center rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden bg-gray-50 dark:bg-white/[0.04]">
        <button v-for="d in (['compact','normal','comfortable'] as const)" :key="d"
                :class="['px-2 py-1.5 transition-all duration-150',
                         densityMode === d
                           ? 'bg-primary-600 text-white shadow-sm'
                           : 'text-gray-500 dark:text-white/40 hover:bg-gray-100 dark:hover:bg-white/10']"
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

      <!-- Visibilité colonnes -->
      <div class="relative dt-col-picker">
        <button @click.stop="showColPicker = !showColPicker"
                class="h-8 px-2.5 flex items-center gap-1.5 text-xs rounded-xl border border-gray-200 dark:border-white/10
                       bg-gray-50 dark:bg-white/[0.06] text-gray-600 dark:text-white/60
                       hover:bg-gray-100 dark:hover:bg-white/10 hover:border-gray-300 dark:hover:border-white/20
                       transition-all duration-150">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
          </svg>
          Colonnes
        </button>
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
          <div v-if="showColPicker"
               class="absolute right-0 top-full mt-1.5 w-52
                      bg-white dark:bg-[#252535]
                      rounded-2xl border border-gray-200/80 dark:border-white/[0.08]
                      shadow-xl dark:shadow-black/40 py-2 z-50">
            <div class="px-3 pb-2 mb-1 border-b border-gray-100 dark:border-white/[0.06] flex items-center justify-between">
              <span class="text-xs font-semibold text-gray-500 dark:text-white/40 uppercase tracking-wide">Colonnes</span>
              <div class="flex gap-2">
                <button class="text-xs text-primary-600 dark:text-primary-400 hover:underline"
                        @click="visibleKeys = columns.map(c => c.key)">Tout</button>
                <button class="text-xs text-gray-400 dark:text-white/30 hover:underline" @click="visibleKeys = []">Aucun</button>
              </div>
            </div>
            <label v-for="col in columns" :key="col.key"
                   class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition-colors">
              <input type="checkbox" :checked="visibleKeys.includes(col.key)"
                     class="w-3.5 h-3.5 rounded border-gray-300 dark:border-white/20 cursor-pointer"
                     style="accent-color:#7c3aed"
                     @change="visibleKeys.includes(col.key) ? visibleKeys = visibleKeys.filter(k => k !== col.key) : visibleKeys.push(col.key)"/>
              <span class="text-xs text-gray-700 dark:text-white/70 truncate">{{ col.label }}</span>
            </label>
          </div>
        </Transition>
      </div>

      <!-- Export -->
      <div v-if="exportable" class="relative dt-export-menu">
        <button @click.stop="showExport = !showExport"
                class="h-8 px-2.5 flex items-center gap-1.5 text-xs rounded-xl border border-gray-200 dark:border-white/10
                       bg-gray-50 dark:bg-white/[0.06] text-gray-600 dark:text-white/60
                       hover:bg-gray-100 dark:hover:bg-white/10 hover:border-gray-300 dark:hover:border-white/20
                       transition-all duration-150">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Export
          <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in" leave-to-class="opacity-0">
          <div v-if="showExport"
               class="absolute right-0 top-full mt-1.5 w-44
                      bg-white dark:bg-[#252535]
                      rounded-2xl border border-gray-200/80 dark:border-white/[0.08]
                      shadow-xl dark:shadow-black/40 py-1.5 z-50">
            <div class="px-3 py-1 mb-1 border-b border-gray-100 dark:border-white/[0.06]">
              <span class="text-xs text-gray-400 dark:text-white/30">
                {{ selected.length > 0 ? `${selected.length} sélectionné(s)` : `${filteredRows.length} ligne(s)` }}
              </span>
            </div>
            <button @click="exportData('xlsx')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-gray-700 dark:text-white/70 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
              <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
              </svg>
              Excel (.xlsx)
            </button>
            <button @click="exportData('csv')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-gray-700 dark:text-white/70 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
              <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
              </svg>
              CSV (.csv)
            </button>
            <button @click="exportData('json')"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-gray-700 dark:text-white/70 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
              <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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


    <!-- ═══ TABLE ═══ -->
    <div class="overflow-x-auto bg-white dark:bg-[#1c1c2e] border border-gray-200 dark:border-white/[0.06] border-t-0 rounded-b-2xl"
         :style="maxHeight ? `max-height:${maxHeight};overflow-y:auto` : ''">
      <table class="min-w-full" role="grid" aria-label="Tableau de données">

        <!-- THEAD -->
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-50 dark:bg-[#1c1c2e] border-b border-gray-200 dark:border-white/[0.06]">

            <th v-if="selectable" :class="[headerDensityClass, 'w-10 sticky left-0 bg-gray-50 dark:bg-[#1c1c2e] z-20']">
              <input type="checkbox" :checked="allSelected" :indeterminate="someSelected"
                     class="w-4 h-4 rounded border-gray-300 dark:border-white/20 cursor-pointer"
                     style="accent-color:#7c3aed"
                     @change="toggleAll" aria-label="Sélectionner tout"/>
            </th>

            <th :class="[headerDensityClass, 'w-10 text-left text-[11px] font-semibold text-gray-400 dark:text-white/30 uppercase tracking-widest']">
              #
            </th>

            <th v-for="col in visibleColumns" :key="col.key"
                :class="[
                  headerDensityClass,
                  'text-[11px] font-semibold text-gray-500 dark:text-white/40 uppercase tracking-widest whitespace-nowrap',
                  col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-left',
                  col.sortable !== false ? 'cursor-pointer select-none hover:text-gray-700 dark:hover:text-white/70 transition-colors' : '',
                  col.sticky ? 'sticky left-0 z-10 bg-gray-50 dark:bg-[#1c1c2e]' : '',
                ]"
                :style="col.width ? `width:${col.width}` : col.minWidth ? `min-width:${col.minWidth}` : ''"
                @click="col.sortable !== false && toggleSort(col.key)"
                :aria-sort="sortKey === col.key ? (sortDir === 'asc' ? 'ascending' : 'descending') : 'none'">
              <div class="flex items-center gap-1" :class="col.align === 'center' ? 'justify-center' : col.align === 'right' ? 'justify-end' : ''">
                {{ col.label }}
                <span v-if="col.sortable !== false" class="flex flex-col ml-0.5 gap-px">
                  <svg :class="['w-2.5 h-2.5', sortKey === col.key && sortDir === 'asc' ? 'text-primary-400 opacity-100' : 'opacity-20']"
                       fill="currentColor" viewBox="0 0 24 24"><path d="M7 14l5-5 5 5z"/></svg>
                  <svg :class="['w-2.5 h-2.5 -mt-1', sortKey === col.key && sortDir === 'desc' ? 'text-primary-400 opacity-100' : 'opacity-20']"
                       fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                </span>
              </div>
            </th>

            <th v-if="actions?.length" :class="[headerDensityClass, 'text-right text-[11px] font-semibold text-gray-500 dark:text-white/40 uppercase tracking-widest']">
              Actions
            </th>
          </tr>
        </thead>

        <!-- TBODY -->
        <tbody class="bg-white dark:bg-[#1c1c2e] divide-y divide-gray-100 dark:divide-white/[0.04]">

          <!-- Skeleton loading -->
          <template v-if="loading">
            <tr v-for="i in perPage" :key="`sk-${i}`"
                class="animate-pulse">
              <td v-if="selectable" :class="[densityClass, 'w-10']">
                <div class="h-4 w-4 bg-gray-200 dark:bg-white/10 rounded"/>
              </td>
              <td :class="[densityClass, 'w-10']">
                <div class="h-3 w-6 bg-gray-200 dark:bg-white/10 rounded"/>
              </td>
              <td v-for="col in visibleColumns" :key="col.key" :class="densityClass">
                <div class="h-3 bg-gray-200 dark:bg-white/10 rounded"
                     :style="`width:${Math.floor(Math.random()*40)+40}%`"/>
              </td>
              <td v-if="actions?.length" :class="densityClass">
                <div class="h-6 w-16 bg-gray-200 dark:bg-white/10 rounded ml-auto"/>
              </td>
            </tr>
          </template>

          <!-- État vide -->
          <template v-else-if="!paginatedRows.length">
            <tr>
              <td :colspan="totalCols" class="px-4 py-16 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                  <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                      {{ search ? `Aucun résultat pour "${search}"` : emptyText }}
                    </p>
                    <p v-if="search" class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                      Essayez d'autres termes ou effacez la recherche.
                    </p>
                  </div>
                  <button v-if="search" @click="search = ''"
                          class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                    Effacer la recherche
                  </button>
                </div>
              </td>
            </tr>
          </template>

          <!-- Données -->
          <template v-else>
            <tr v-for="(row, idx) in paginatedRows"
                :key="rowKey ? String(row[rowKey]) : idx"
                :class="[
                  'group transition-colors duration-100 cursor-default',
                  selected.includes(rowId(row))
                    ? 'bg-primary-50 dark:bg-[#2a2a42]'
                    : idx % 2 === 0
                      ? 'bg-white dark:bg-[#1c1c2e] hover:bg-gray-50 dark:hover:bg-[#22223a]'
                      : 'bg-gray-50/40 dark:bg-[#1e1e32] hover:bg-gray-100/50 dark:hover:bg-[#22223a]',
                ]"
                @contextmenu="openCtxMenu($event, row)">

              <!-- Checkbox ligne -->
              <td v-if="selectable" :class="[densityClass, 'w-10']">
                <input type="checkbox"
                       :checked="selected.includes(rowId(row))"
                       class="w-4 h-4 rounded border-gray-300 dark:border-white/20 cursor-pointer"
                       style="accent-color:#7c3aed"
                       @change="toggleRow(row, idx, $event as MouseEvent)"
                       :aria-label="`Sélectionner la ligne ${idx + 1}`"/>
              </td>

              <!-- Numéro -->
              <td :class="[densityClass, 'w-10 text-xs text-gray-400 dark:text-white/25 font-mono tabular-nums']">
                {{ (currentPage - 1) * perPage + idx + 1 }}
              </td>

              <!-- Cellules -->
              <td v-for="col in visibleColumns" :key="col.key"
                  :class="[getCellClass(row, col), col.sticky ? 'sticky left-0 bg-inherit z-10' : '', bordered ? 'border-r border-gray-100 dark:border-white/[0.04]' : '']"
                  @dblclick="startEdit(idx, col, row)">

                <!-- Mode édition -->
                <div v-if="editingCell && editingCell.rowIdx === idx && editingCell.key === col.key"
                     class="flex flex-col gap-1">
                  <input v-model="editValue"
                         :type="col.dataType === 'number' ? 'number' : col.dataType === 'email' ? 'email' : col.dataType === 'date' ? 'date' : col.dataType === 'datetime' ? 'datetime-local' : 'text'"
                         class="dt-edit-input w-full px-2 py-1 text-xs rounded-lg border-2 bg-white dark:bg-[#252540] text-gray-900 dark:text-white focus:outline-none"
                         :class="editError ? 'border-red-500' : 'border-primary-500'"
                         @keydown="onEditKey"
                         @blur="saveEdit"/>
                  <span v-if="editError" class="text-xs text-red-500">{{ editError }}</span>
                  <div class="flex gap-1">
                    <button @click="saveEdit"
                            class="px-2 py-0.5 text-xs bg-primary-600 text-white rounded hover:bg-primary-700">✓</button>
                    <button @click="cancelEdit"
                            class="px-2 py-0.5 text-xs bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-white/70 rounded">✕</button>
                  </div>
                </div>

                <!-- Affichage normal -->
                <template v-else>
                  <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]" :col="col">
                    <span v-if="col.badge" :class="getBadgeClass(row[col.key], col)">
                      {{ getCellValue(row, col) }}
                    </span>
                    <span v-else :class="[
                            'text-gray-800 dark:text-white/80',
                            col.editable ? 'cursor-text hover:text-primary-600 dark:hover:text-primary-400 transition-colors' : ''
                          ]"
                          :title="col.editable ? 'Double-clic pour modifier' : undefined">
                      {{ getCellValue(row, col) }}
                    </span>
                  </slot>
                  <span v-if="col.editable && !(editingCell && editingCell.rowIdx === idx && editingCell.key === col.key)"
                        class="ml-1 opacity-0 group-hover:opacity-30 transition-opacity">
                    <svg class="inline w-3 h-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </span>
                </template>
              </td>

              <!-- Actions par ligne -->
              <td v-if="actions?.length" :class="[densityClass, 'text-right whitespace-nowrap']">
                <slot name="actions" :row="row" :index="idx">
                  <div class="flex items-center justify-end gap-1">
                    <template v-for="action in actions" :key="action.key">
                      <button v-if="!action.condition || action.condition(row)"
                              :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium transition-all duration-150',
                                       getActionClass(action.variant)]"
                              :title="action.label"
                              @click="handleAction(action, row)">
                        <span v-if="action.icon" v-html="action.icon" class="w-3.5 h-3.5 flex-shrink-0"/>
                        <span>{{ action.label }}</span>
                      </button>
                    </template>
                  </div>
                </slot>
              </td>
            </tr>
          </template>
        </tbody>

        <!-- TFOOT -->
        <tfoot v-if="hasTotals && !loading && paginatedRows.length > 0">
          <tr class="bg-gray-50 dark:bg-[#16162a] border-t-2 border-gray-200 dark:border-white/[0.08]">
            <td v-if="selectable" :class="densityClass"/>
            <td :class="[densityClass, 'text-xs font-bold text-gray-500 dark:text-white/40 uppercase tracking-wide']">Total</td>
            <td v-for="col in visibleColumns" :key="col.key"
                :class="[densityClass, col.align === 'center' ? 'text-center' : col.align === 'right' ? 'text-right' : 'text-right']">
              <span v-if="columnTotals[col.key] !== undefined"
                    class="text-xs font-bold text-gray-800 dark:text-white/80 tabular-nums">
                {{ formatTotal(col, columnTotals[col.key]) }}
              </span>
            </td>
            <td v-if="actions?.length" :class="densityClass"/>
          </tr>
        </tfoot>
      </table>
    </div>
    <!-- /TABLE -->


    <!-- ═══ PAGINATION ═══ -->
    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3
                bg-white dark:bg-[#1c1c2e]
                border border-gray-200 dark:border-white/[0.06]
                border-t-0 rounded-b-2xl">

      <!-- Info résultats -->
      <p class="text-xs text-gray-500 dark:text-white/30 tabular-nums whitespace-nowrap">
        <template v-if="filteredRows.length > 0">
          Affichage
          <span class="font-semibold text-gray-700 dark:text-white/60">{{ rangeFrom }}–{{ rangeTo }}</span>
          sur
          <span class="font-semibold text-gray-700 dark:text-white/60">{{ filteredRows.length.toLocaleString('fr-FR') }}</span>
          <template v-if="filteredRows.length !== rows.length">
            <span class="text-gray-400 dark:text-white/20"> (filtré sur {{ rows.length.toLocaleString('fr-FR') }})</span>
          </template>
        </template>
        <template v-else>Aucun résultat</template>
      </p>

      <!-- Boutons de pagination -->
      <div class="flex items-center gap-1 flex-wrap">
        <!-- Première page -->
        <button :disabled="currentPage <= 1"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-all duration-150
                       disabled:opacity-25 disabled:cursor-not-allowed
                       hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-white/40"
                @click="currentPage = 1" title="Première page">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
          </svg>
        </button>

        <!-- Page précédente -->
        <button :disabled="currentPage <= 1"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-all duration-150
                       disabled:opacity-25 disabled:cursor-not-allowed
                       hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-white/40"
                @click="currentPage--" title="Page précédente">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>

        <!-- Pages numérotées -->
        <template v-for="p in visiblePages" :key="p">
          <span v-if="p === '...'" class="w-7 h-7 flex items-center justify-center text-xs text-gray-400 dark:text-white/20">…</span>
          <button v-else
                  :class="['w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-all duration-150',
                           p === currentPage
                             ? 'bg-primary-600 text-white shadow-sm scale-105'
                             : 'hover:bg-gray-100 dark:hover:bg-white/10 text-gray-600 dark:text-white/50']"
                  @click="currentPage = p as number">
            {{ p }}
          </button>
        </template>

        <!-- Page suivante -->
        <button :disabled="currentPage >= totalPages"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-all duration-150
                       disabled:opacity-25 disabled:cursor-not-allowed
                       hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-white/40"
                @click="currentPage++" title="Page suivante">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>

        <!-- Dernière page -->
        <button :disabled="currentPage >= totalPages"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-xs font-medium transition-all duration-150
                       disabled:opacity-25 disabled:cursor-not-allowed
                       hover:bg-gray-100 dark:hover:bg-white/10 text-gray-500 dark:text-white/40"
                @click="currentPage = totalPages" title="Dernière page">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>
    <!-- /PAGINATION -->

    <!-- ═══ MENU CONTEXTUEL ═══ -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 scale-95 translate-y-1"
                  enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="transition duration-100 ease-in"
                  leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="ctxMenu.show && ctxMenu.row"
             class="dt-ctx-menu fixed z-[9999] min-w-[200px]
                    bg-white dark:bg-[#252535]
                    rounded-2xl border border-gray-200/80 dark:border-white/[0.08]
                    shadow-2xl dark:shadow-black/50
                    py-1.5 overflow-hidden"
             :style="`top:${ctxMenu.y}px;left:${ctxMenu.x}px`">

          <!-- En-tête du menu -->
          <div class="px-3.5 py-2 mb-1 border-b border-gray-100 dark:border-white/[0.06] flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-primary-500"/>
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</span>
          </div>

          <template v-for="(action, actionIdx) in actions" :key="action.key">
            <!-- Séparateur avant les actions danger -->
            <div v-if="actionIdx > 0 && action.variant === 'danger' && actions[actionIdx - 1]?.variant !== 'danger'"
                 class="my-1 border-t border-gray-100 dark:border-white/[0.06]"/>

            <button v-if="!action.condition || action.condition(ctxMenu.row)"
                    :class="[
                      'w-full flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium transition-all duration-100',
                      action.variant === 'danger'
                        ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10'
                        : action.variant === 'warning'
                          ? 'text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10'
                          : action.variant === 'success'
                            ? 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10'
                            : action.variant === 'info'
                              ? 'text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10'
                              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5'
                    ]"
                    @click="handleAction(action, ctxMenu.row!)">
              <!-- Icône de l'action ou icône par défaut selon variant -->
              <span v-if="action.icon" v-html="action.icon"
                    class="w-4 h-4 flex-shrink-0 opacity-80"/>
              <template v-else>
                <!-- Icônes par défaut selon variant -->
                <svg v-if="action.variant === 'danger'" class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <svg v-else-if="action.variant === 'warning'" class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <svg v-else-if="action.variant === 'success'" class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg v-else-if="action.variant === 'info'" class="w-4 h-4 flex-shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg v-else class="w-4 h-4 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </template>
              <span class="flex-1 text-left">{{ action.label }}</span>
              <!-- Raccourci visuel optionnel -->
              <kbd v-if="action.variant === 'danger'"
                   class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono
                          bg-red-100 dark:bg-red-500/10 text-red-500 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                Del
              </kbd>
            </button>
          </template>

          <!-- Pied du menu : fermer -->
          <div class="mt-1 pt-1 border-t border-gray-100 dark:border-white/[0.06]">
            <button @click="closeCtxMenu"
                    class="w-full flex items-center gap-3 px-3.5 py-2 text-xs text-gray-400 dark:text-gray-600
                           hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Fermer
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- ═══ CONFIRM DIALOG ═══ -->
    <Teleport to="body">
      <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
                  enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in"
                  leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="confirmDialog.show"
             class="fixed inset-0 z-[9998] flex items-center justify-center p-4"
             @click.self="confirmDialog.show = false">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm"/>

          <!-- Dialog -->
          <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95 translate-y-2"
                      enter-to-class="opacity-100 scale-100 translate-y-0">
            <div class="relative w-full max-w-md
                        bg-white dark:bg-[#1e1e2e]
                        rounded-2xl shadow-2xl dark:shadow-black/60
                        border border-gray-200/80 dark:border-white/[0.08]
                        overflow-hidden">
              <!-- Barre colorée en haut -->
              <div :class="['h-1 w-full',
                            confirmDialog.variant === 'danger' ? 'bg-gradient-to-r from-red-500 to-rose-500'
                            : confirmDialog.variant === 'warning' ? 'bg-gradient-to-r from-amber-400 to-orange-500'
                            : 'bg-gradient-to-r from-blue-500 to-indigo-500']"/>

              <div class="p-6">
                <!-- Icône + texte -->
                <div class="flex items-start gap-4">
                  <div :class="['w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0',
                                confirmDialog.variant === 'danger'
                                  ? 'bg-red-100 dark:bg-red-500/15'
                                  : confirmDialog.variant === 'warning'
                                    ? 'bg-amber-100 dark:bg-amber-500/15'
                                    : 'bg-blue-100 dark:bg-blue-500/15']">
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
                  <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white tracking-tight">
                      {{ confirmDialog.title }}
                    </h3>
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                      {{ confirmDialog.message }}
                    </p>
                  </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end gap-2.5 mt-6">
                  <button @click="confirmDialog.show = false"
                          class="px-4 py-2 text-sm font-medium rounded-xl
                                 border border-gray-200 dark:border-white/10
                                 text-gray-700 dark:text-gray-300
                                 bg-white dark:bg-white/5
                                 hover:bg-gray-50 dark:hover:bg-white/10
                                 transition-all duration-150">
                    Annuler
                  </button>
                  <button @click="confirmDialog.onConfirm(); confirmDialog.show = false"
                          :class="['px-4 py-2 text-sm font-semibold rounded-xl text-white transition-all duration-150 shadow-sm',
                                   confirmDialog.variant === 'danger'
                                     ? 'bg-red-600 hover:bg-red-700 active:bg-red-800 shadow-red-200 dark:shadow-red-900/30'
                                     : confirmDialog.variant === 'warning'
                                       ? 'bg-amber-500 hover:bg-amber-600 active:bg-amber-700 shadow-amber-200 dark:shadow-amber-900/30'
                                       : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 shadow-blue-200 dark:shadow-blue-900/30']">
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
