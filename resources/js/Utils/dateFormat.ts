/**
 * Utilitaire centralisé pour le formatage des dates.
 * Format cible : d-m-Y (ex: 01-06-2026)
 */

/**
 * Formate une date en d-m-Y (ex: "01-06-2026")
 */
export function fmtDate(value: string | Date | null | undefined): string {
    if (!value) return '—';
    try {
        const d = value instanceof Date ? value : new Date(value);
        if (isNaN(d.getTime())) return String(value);
        const day   = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year  = d.getFullYear();
        return `${day}-${month}-${year}`;
    } catch {
        return String(value);
    }
}

/**
 * Formate une date+heure en d-m-Y H:i (ex: "01-06-2026 14:30")
 */
export function fmtDateTime(value: string | Date | null | undefined): string {
    if (!value) return '—';
    try {
        const d = value instanceof Date ? value : new Date(value);
        if (isNaN(d.getTime())) return String(value);
        const day    = String(d.getDate()).padStart(2, '0');
        const month  = String(d.getMonth() + 1).padStart(2, '0');
        const year   = d.getFullYear();
        const hours  = String(d.getHours()).padStart(2, '0');
        const mins   = String(d.getMinutes()).padStart(2, '0');
        return `${day}-${month}-${year} ${hours}:${mins}`;
    } catch {
        return String(value);
    }
}

/**
 * Formate une date en affichage long localisé (ex: "lundi 1 juin 2026")
 * Utilisé dans les headers de dashboard.
 */
export function fmtDateLong(value: string | Date | null | undefined): string {
    if (!value) return '—';
    try {
        const d = value instanceof Date ? value : new Date(value);
        if (isNaN(d.getTime())) return String(value);
        return d.toLocaleDateString('fr-FR', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
        });
    } catch {
        return String(value);
    }
}

/**
 * Formate une date en affichage court localisé (ex: "1 juin 2026")
 */
export function fmtDateShort(value: string | Date | null | undefined): string {
    if (!value) return '—';
    try {
        const d = value instanceof Date ? value : new Date(value);
        if (isNaN(d.getTime())) return String(value);
        return d.toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'short', year: 'numeric',
        });
    } catch {
        return String(value);
    }
}

/**
 * Retourne un timestamp numérique depuis une valeur de date quelconque.
 * Utile pour le tri des colonnes de date dans DataTable.
 * Retourne -Infinity si la valeur n'est pas parsable.
 */
export function dateToTimestamp(value: unknown): number {
    if (!value) return -Infinity;
    // Déjà un nombre (timestamp ms)
    if (typeof value === 'number') return value;
    const str = String(value).trim();
    if (!str || str === '—') return -Infinity;
    // Format d-m-Y ou d/m/Y → convertir en ISO pour parsing fiable
    const dmY = str.match(/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/);
    if (dmY) {
        const [, d, m, y] = dmY;
        const ts = new Date(`${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`).getTime();
        if (!isNaN(ts)) return ts;
    }
    // Format ISO ou autre
    const ts = new Date(str).getTime();
    return isNaN(ts) ? -Infinity : ts;
}
