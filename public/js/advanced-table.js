/**
 * AdvancedTable — Tableau avancé réutilisable
 * Fonctionnalités :
 *  - Recherche instantanée multi-colonnes (séparées par virgules)
 *  - Tri cliquable sur chaque colonne
 *  - Hover smooth
 *  - Lignes horizontales nettes
 *  - Colonne "En ligne" avec indicateur visuel
 *  - Clic droit → menu contextuel (Modifier, Voir détails)
 *  - Double-clic sur cellule → édition inline (super admin uniquement)
 *  - Export CSV/Excel respectant les filtres actifs
 *  - Sélection par checkbox
 *  - Permissions : super_admin peut éditer les cellules
 */

(function () {
    'use strict';

    // ─── Initialisation principale ───────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const tables = document.querySelectorAll('[data-advanced-table]');
        tables.forEach(initAdvancedTable);
    });

    /**
     * Initialise toutes les fonctionnalités sur une table donnée.
     * @param {HTMLElement} wrapper - L'élément contenant [data-advanced-table]
     */
    function initAdvancedTable(wrapper) {
        const table = wrapper.querySelector('table');
        if (!table) return;

        const isSuperAdmin = wrapper.dataset.userType === 'super_admin';
        const canEdit = isSuperAdmin;

        // Récupère les paramètres de configuration
        const config = {
            editEndpoint: wrapper.dataset.editEndpoint || null,
            detailUrl: wrapper.dataset.detailUrl || null,
            editUrl: wrapper.dataset.editUrl || null,
            userType: wrapper.dataset.userType || 'admin',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        };

        // ── 1. Recherche instantanée ─────────────────────────────────────────
        initLiveSearch(wrapper, table);

        // ── 2. Tri des colonnes ──────────────────────────────────────────────
        initColumnSort(table);

        // ── 3. Sélection par checkbox ────────────────────────────────────────
        initCheckboxSelection(table);

        // ── 4. Clic droit (menu contextuel) ─────────────────────────────────
        initContextMenu(table, config);

        // ── 5. Double-clic → édition inline (super admin uniquement) ────────
        if (canEdit) {
            initInlineEdit(table, config);
        }

        // ── 6. Compteur de lignes filtrées ───────────────────────────────────
        initRowCounter(wrapper, table);

        // ── 7. Items par page ────────────────────────────────────────────────
        initItemsPerPage(wrapper);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 1. RECHERCHE INSTANTANÉE (multi-colonnes avec virgules)
    // ═══════════════════════════════════════════════════════════════════════════
    function initLiveSearch(wrapper, table) {
        const searchInput = wrapper.querySelector('[data-search-input]');
        if (!searchInput) return;

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-row]'));
        const noResultRow = tbody.querySelector('[data-no-result]');

        function normalizeText(str) {
            return str
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function applySearch() {
            const rawValue = searchInput.value.trim();
            // Termes séparés par virgules
            const terms = rawValue
                .split(',')
                .map(t => normalizeText(t.trim()))
                .filter(t => t.length > 0);

            let visibleCount = 0;

            rows.forEach(row => {
                const cellTexts = Array.from(row.querySelectorAll('td[data-col]'))
                    .map(td => normalizeText(td.dataset.searchValue || td.textContent || ''));

                const matches = terms.length === 0 || terms.every(term =>
                    cellTexts.some(text => text.includes(term))
                );

                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;

                // Mise en surbrillance des termes trouvés
                if (terms.length > 0 && matches) {
                    highlightTerms(row, terms);
                } else {
                    clearHighlights(row);
                }
            });

            // Ligne "aucun résultat"
            if (noResultRow) {
                noResultRow.style.display = visibleCount === 0 ? '' : 'none';
            }

            // Mettre à jour le compteur
            updateRowCounter(wrapper, visibleCount, rows.length);
        }

        // Délai pour éviter trop d'appels
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applySearch, 120);
        });

        // Initialiser l'état
        applySearch();
    }

    function highlightTerms(row, terms) {
        row.querySelectorAll('td[data-col] [data-highlight]').forEach(el => {
            let original = el.dataset.original || el.textContent;
            el.dataset.original = original;

            let html = escapeHtml(original);
            terms.forEach(term => {
                if (!term) return;
                const regex = new RegExp(
                    '(' + escapeRegex(term) + ')',
                    'gi'
                );
                html = html.replace(regex, '<mark class="adv-highlight">$1</mark>');
            });
            el.innerHTML = html;
        });
    }

    function clearHighlights(row) {
        row.querySelectorAll('[data-highlight]').forEach(el => {
            if (el.dataset.original) {
                el.textContent = el.dataset.original;
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 2. TRI DES COLONNES
    // ═══════════════════════════════════════════════════════════════════════════
    function initColumnSort(table) {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;

        const headers = Array.from(thead.querySelectorAll('th[data-sortable]'));
        let currentSort = { col: -1, dir: 'asc' };

        headers.forEach((th, idx) => {
            // Ajoute le curseur et l'icône
            th.style.cursor = 'pointer';
            th.style.userSelect = 'none';

            const iconEl = document.createElement('span');
            iconEl.className = 'sort-icon ml-1 inline-block transition-transform duration-200';
            iconEl.innerHTML = '<svg width="10" height="14" viewBox="0 0 10 14" fill="currentColor" style="opacity:0.4"><path d="M5 0L0 5h10L5 0zm0 14l5-5H0l5 5z"/></svg>';
            th.appendChild(iconEl);

            th.addEventListener('click', function () {
                const colIndex = parseInt(th.dataset.sortable);
                let dir = 'asc';
                if (currentSort.col === colIndex) {
                    dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
                }
                currentSort = { col: colIndex, dir };

                // Reset all icons
                headers.forEach(h => {
                    const ic = h.querySelector('.sort-icon');
                    if (ic) ic.style.opacity = '0.4';
                    h.dataset.sortDir = '';
                    h.classList.remove('adv-th-sorted');
                });

                // Active icon
                iconEl.style.opacity = '1';
                iconEl.style.transform = dir === 'desc' ? 'rotate(180deg)' : 'rotate(0deg)';
                th.dataset.sortDir = dir;
                th.classList.add('adv-th-sorted');

                sortTable(tbody, colIndex, dir);
            });
        });
    }

    function sortTable(tbody, colIndex, dir) {
        const rows = Array.from(tbody.querySelectorAll('tr[data-row]'));
        const noResultRow = tbody.querySelector('[data-no-result]');

        rows.sort((a, b) => {
            const aCell = a.querySelector(`td[data-col="${colIndex}"]`);
            const bCell = b.querySelector(`td[data-col="${colIndex}"]`);

            const aVal = (aCell?.dataset.sortValue || aCell?.textContent || '').trim();
            const bVal = (bCell?.dataset.sortValue || bCell?.textContent || '').trim();

            // Essaie tri numérique
            const aNum = parseFloat(aVal.replace(/[^0-9.-]/g, ''));
            const bNum = parseFloat(bVal.replace(/[^0-9.-]/g, ''));

            let cmp;
            if (!isNaN(aNum) && !isNaN(bNum)) {
                cmp = aNum - bNum;
            } else {
                // Tri date
                const aDate = new Date(aVal);
                const bDate = new Date(bVal);
                if (!isNaN(aDate) && !isNaN(bDate)) {
                    cmp = aDate - bDate;
                } else {
                    cmp = aVal.localeCompare(bVal, 'fr', { sensitivity: 'base' });
                }
            }

            return dir === 'asc' ? cmp : -cmp;
        });

        // Réinsertion
        rows.forEach(row => tbody.appendChild(row));
        if (noResultRow) tbody.appendChild(noResultRow);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 3. SÉLECTION PAR CHECKBOX
    // ═══════════════════════════════════════════════════════════════════════════
    function initCheckboxSelection(table) {
        const masterCheckbox = table.querySelector('[data-check-all]');
        const rowCheckboxes = () => table.querySelectorAll('[data-check-row]');

        if (!masterCheckbox) return;

        masterCheckbox.addEventListener('change', function () {
            rowCheckboxes().forEach(cb => {
                const row = cb.closest('tr');
                if (row && row.style.display !== 'none') {
                    cb.checked = masterCheckbox.checked;
                    toggleRowSelection(row, cb.checked);
                }
            });
        });

        table.addEventListener('change', function (e) {
            if (e.target.matches('[data-check-row]')) {
                const row = e.target.closest('tr');
                toggleRowSelection(row, e.target.checked);

                // Met à jour le master checkbox
                const allCbs = rowCheckboxes();
                const allChecked = Array.from(allCbs).every(cb => cb.checked);
                const someChecked = Array.from(allCbs).some(cb => cb.checked);
                masterCheckbox.checked = allChecked;
                masterCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    }

    function toggleRowSelection(row, selected) {
        if (!row) return;
        if (selected) {
            row.classList.add('adv-row-selected');
        } else {
            row.classList.remove('adv-row-selected');
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 4. MENU CONTEXTUEL (CLIC DROIT)
    // ═══════════════════════════════════════════════════════════════════════════
    function initContextMenu(table, config) {
        const menu = document.getElementById('adv-context-menu');
        if (!menu) return;

        let activeRow = null;

        table.addEventListener('contextmenu', function (e) {
            const row = e.target.closest('tr[data-row]');
            if (!row) return;

            e.preventDefault();
            activeRow = row;

            // Position
            const x = Math.min(e.clientX, window.innerWidth - 200);
            const y = Math.min(e.clientY, window.innerHeight - 160);

            menu.style.left = x + 'px';
            menu.style.top = y + 'px';
            menu.classList.remove('adv-cm-hidden');

            // Mise à jour des URLs
            const rowId = row.dataset.rowId;
            const editLink = menu.querySelector('[data-cm-edit]');
            const detailLink = menu.querySelector('[data-cm-detail]');

            if (editLink && row.dataset.editUrl) {
                editLink.href = row.dataset.editUrl;
            }
            if (detailLink && row.dataset.detailUrl) {
                detailLink.href = row.dataset.detailUrl;
            }
        });

        // Ferme en cliquant ailleurs
        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target)) {
                menu.classList.add('adv-cm-hidden');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') menu.classList.add('adv-cm-hidden');
        });
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 5. ÉDITION INLINE (double-clic — super admin uniquement)
    // ═══════════════════════════════════════════════════════════════════════════
    function initInlineEdit(table, config) {
        table.addEventListener('dblclick', function (e) {
            const cell = e.target.closest('td[data-editable]');
            if (!cell) return;

            // Évite la double ouverture
            if (cell.querySelector('.adv-edit-input')) return;

            const originalValue = cell.dataset.editValue || cell.textContent.trim();
            const fieldType = cell.dataset.fieldType || 'text';
            const fieldName = cell.dataset.fieldName;
            const rowId = cell.closest('tr[data-row]')?.dataset.rowId;
            const endpoint = cell.dataset.endpoint || config.editEndpoint;

            if (!rowId || !endpoint || !fieldName) return;

            // Sauvegarde le contenu original
            const originalContent = cell.innerHTML;
            cell.innerHTML = '';

            // Crée l'input
            const input = createEditInput(fieldType, originalValue);
            input.className = 'adv-edit-input';
            cell.appendChild(input);
            input.focus();
            if (input.select) input.select();

            // Indicateur visuel
            cell.classList.add('adv-cell-editing');

            // Sauvegarde sur Enter ou blur
            async function saveEdit() {
                const newValue = input.value.trim();
                cell.classList.remove('adv-cell-editing');

                if (newValue === originalValue) {
                    // Pas de changement
                    cell.innerHTML = originalContent;
                    return;
                }

                // Indicateur de chargement
                cell.innerHTML = '<span class="adv-saving">⟳</span>';

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': config.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            id: rowId,
                            field: fieldName,
                            value: newValue,
                        }),
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Succès
                        cell.dataset.editValue = newValue;
                        restoreCellContent(cell, fieldType, fieldName, newValue);
                        showCellFeedback(cell, 'success');
                    } else {
                        // Erreur serveur
                        cell.innerHTML = originalContent;
                        showCellFeedback(cell, 'error', data.message || 'Erreur');
                    }
                } catch (err) {
                    cell.innerHTML = originalContent;
                    showCellFeedback(cell, 'error', 'Erreur réseau');
                }
            }

            function cancelEdit() {
                cell.classList.remove('adv-cell-editing');
                cell.innerHTML = originalContent;
            }

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); saveEdit(); }
                if (e.key === 'Escape') { e.preventDefault(); cancelEdit(); }
            });

            input.addEventListener('blur', saveEdit);
        });
    }

    function createEditInput(type, value) {
        if (type === 'select') {
            // Le select est créé dynamiquement selon les options disponibles
            const sel = document.createElement('select');
            [['1', 'Actif'], ['0', 'Inactif']].forEach(([v, l]) => {
                const opt = document.createElement('option');
                opt.value = v;
                opt.textContent = l;
                if (v === value) opt.selected = true;
                sel.appendChild(opt);
            });
            return sel;
        }
        const input = document.createElement('input');
        input.type = type === 'date' ? 'date' : 'text';
        input.value = value;
        return input;
    }

    function restoreCellContent(cell, fieldType, fieldName, value) {
        // Régénère le contenu selon le type de champ
        if (fieldName === 'status') {
            const isActive = value === '1' || value === 1;
            cell.innerHTML = `<span class="px-2 py-1 border w-24 inline-flex justify-center text-xs leading-5 font-semibold rounded-full ${isActive ? 'bg-green-100 border-green-800 text-green-800' : 'bg-red-100 border-red-800 text-red-800'}">${isActive ? 'Actif' : 'Inactif'}</span>`;
        } else {
            cell.innerHTML = `<span data-highlight data-original="${escapeHtml(value)}">${escapeHtml(value)}</span>`;
        }
    }

    function showCellFeedback(cell, type, msg) {
        const cls = type === 'success' ? 'adv-cell-saved' : 'adv-cell-error';
        cell.classList.add(cls);
        setTimeout(() => cell.classList.remove(cls), 1500);
        if (type === 'error' && msg) {
            const tip = document.createElement('div');
            tip.className = 'adv-cell-tip';
            tip.textContent = msg;
            cell.appendChild(tip);
            setTimeout(() => tip.remove(), 2500);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 6. COMPTEUR DE LIGNES
    // ═══════════════════════════════════════════════════════════════════════════
    function initRowCounter(wrapper, table) {
        const counter = wrapper.querySelector('[data-row-counter]');
        if (!counter) return;

        const tbody = table.querySelector('tbody');
        const total = tbody.querySelectorAll('tr[data-row]').length;
        counter.dataset.total = total;
        counter.textContent = `${total} résultat(s)`;
    }

    function updateRowCounter(wrapper, visible, total) {
        const counter = wrapper.querySelector('[data-row-counter]');
        if (!counter) return;
        if (visible === total) {
            counter.textContent = `${total} résultat(s)`;
        } else {
            counter.textContent = `${visible} / ${total} résultat(s)`;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 7. ITEMS PAR PAGE
    // ═══════════════════════════════════════════════════════════════════════════
    function initItemsPerPage(wrapper) {
        const select = wrapper.querySelector('[data-per-page]');
        if (!select) return;

        select.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });

        // Sélectionner la valeur actuelle
        const current = new URL(window.location.href).searchParams.get('per_page') || '15';
        select.value = current;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // UTILITAIRES
    // ═══════════════════════════════════════════════════════════════════════════
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // EXPORT CSV côté client (complément de l'export serveur)
    // ═══════════════════════════════════════════════════════════════════════════
    window.advTableExportCSV = function (wrapperId, filename) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const table = wrapper.querySelector('table');
        const headers = Array.from(table.querySelectorAll('thead th[data-sortable]'))
            .map(th => th.dataset.label || th.textContent.trim().replace(/\s+/g, ' '));

        const rows = Array.from(table.querySelectorAll('tbody tr[data-row]'))
            .filter(r => r.style.display !== 'none')
            .map(r =>
                Array.from(r.querySelectorAll('td[data-col]'))
                    .map(td => '"' + (td.dataset.searchValue || td.textContent.trim()).replace(/"/g, '""') + '"')
            );

        const csvContent = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = (filename || 'export') + '_' + new Date().toISOString().split('T')[0] + '.csv';
        link.click();
    };

})();
