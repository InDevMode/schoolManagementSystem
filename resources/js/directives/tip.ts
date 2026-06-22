/**
 * v-tip — directive globale pour remplacer les title natifs
 * par le tooltip custom sidebar-style (bulle violette, position top).
 *
 * Usage:
 *   <button v-tip="'Supprimer'"> … </button>
 *   <button title="Supprimer"> …  </button>  ← intercepté automatiquement si useTitleInterception() est appelé
 */
import type { App, DirectiveBinding } from 'vue';

// ── Singleton tooltip DOM ────────────────────────────────────────────────────
let tipEl: HTMLElement | null = null;
let bubbleEl: HTMLElement | null = null;
let arrowEl: HTMLElement | null = null;
let currentAnchor: HTMLElement | null = null;

function ensureTipEl() {
    if (tipEl) return;

    tipEl = document.createElement('div');
    tipEl.style.cssText = `
        position: fixed;
        z-index: 99999;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        opacity: 0;
        transform: translateY(4px);
        transition: opacity 120ms ease, transform 120ms ease;
    `;

    bubbleEl = document.createElement('span');
    bubbleEl.style.cssText = `
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        white-space: nowrap;
        background: linear-gradient(135deg, #7B74F0 0%, #9189f5 100%);
        box-shadow: 0 4px 14px rgba(123,116,240,0.38), 0 2px 6px rgba(0,0,0,0.10);
    `;

    arrowEl = document.createElement('span');
    arrowEl.style.cssText = `
        width: 0; height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 7px solid #9189f5;
        flex-shrink: 0;
    `;

    tipEl.appendChild(bubbleEl);
    tipEl.appendChild(arrowEl);
    document.body.appendChild(tipEl);
}

export function showTip(anchor: HTMLElement, label: string, clientX: number, clientY: number) {
    ensureTipEl();
    if (!tipEl || !bubbleEl) return;

    currentAnchor = anchor;
    bubbleEl.textContent = label;

    // Positionner d'abord hors écran pour mesurer
    tipEl.style.opacity = '0';
    tipEl.style.left = '-9999px';
    tipEl.style.top = '-9999px';
    tipEl.style.display = 'flex';

    requestAnimationFrame(() => {
        if (!tipEl || !bubbleEl) return;
        const w = tipEl.offsetWidth;
        const GAP = 10; // espace entre curseur et tooltip

        let x = clientX - w / 2;
        let y = clientY - tipEl.offsetHeight - GAP;

        // Garde dans le viewport
        if (x < 6) x = 6;
        if (x + w > window.innerWidth - 6) x = window.innerWidth - w - 6;
        if (y < 6) y = clientY + GAP + 20; // sous le curseur si trop haut

        tipEl.style.left = x + 'px';
        tipEl.style.top  = y + 'px';
        tipEl.style.opacity = '1';
        tipEl.style.transform = 'translateY(0)';
    });
}

export function hideTip() {
    if (!tipEl) return;
    tipEl.style.opacity = '0';
    tipEl.style.transform = 'translateY(4px)';
    currentAnchor = null;
}

// ── Map anchor → handlers pour cleanup ──────────────────────────────────────
const handlerMap = new WeakMap<HTMLElement, {
    enter: (e: MouseEvent) => void;
    leave: () => void;
}>();

function bindTip(el: HTMLElement, label: string) {
    // Supprimer l'ancien binding si présent
    unbindTip(el);

    // Supprimer le title natif pour éviter la double bulle
    el.removeAttribute('title');

    const enter = (e: MouseEvent) => showTip(el, label, e.clientX, e.clientY);
    const leave = () => { if (currentAnchor === el) hideTip(); };

    el.addEventListener('mouseenter', enter);
    el.addEventListener('mouseleave', leave);
    handlerMap.set(el, { enter, leave });
}

function unbindTip(el: HTMLElement) {
    const handlers = handlerMap.get(el);
    if (handlers) {
        el.removeEventListener('mouseenter', handlers.enter);
        el.removeEventListener('mouseleave', handlers.leave);
        handlerMap.delete(el);
    }
}

// ── Directive v-tip ──────────────────────────────────────────────────────────
export const vTip = {
    mounted(el: HTMLElement, binding: DirectiveBinding<string>) {
        if (binding.value) bindTip(el, binding.value);
    },
    updated(el: HTMLElement, binding: DirectiveBinding<string>) {
        if (binding.value) bindTip(el, binding.value);
        else unbindTip(el);
    },
    unmounted(el: HTMLElement) {
        unbindTip(el);
        if (currentAnchor === el) hideTip();
    },
};

// ── Auto-interception des title natifs (MutationObserver) ───────────────────
// Intercepte tous les éléments qui ont un attribut `title` dans le DOM
// et les remplace automatiquement par le tooltip custom.
export function installTitleInterception() {
    const SKIP_TAGS = new Set(['INPUT', 'TEXTAREA', 'SELECT', 'IFRAME', 'ABBR']);

    function processEl(el: Element) {
        if (!(el instanceof HTMLElement)) return;
        if (SKIP_TAGS.has(el.tagName)) return;
        const label = el.getAttribute('title');
        if (!label) return;
        bindTip(el, label);
    }

    function scanTree(root: Element | Document) {
        const nodes = (root as Element).querySelectorAll ? (root as Element).querySelectorAll('[title]') : [];
        nodes.forEach(processEl);
    }

    // Scan initial
    scanTree(document);

    // Observer pour les noeuds ajoutés dynamiquement
    const observer = new MutationObserver((mutations) => {
        mutations.forEach(m => {
            m.addedNodes.forEach(node => {
                if (node instanceof Element) {
                    processEl(node);
                    scanTree(node);
                }
            });
            // Attribut title modifié
            if (m.type === 'attributes' && m.attributeName === 'title' && m.target instanceof HTMLElement) {
                processEl(m.target);
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['title'],
    });
}

// ── Plugin Vue ────────────────────────────────────────────────────────────────
export const TipPlugin = {
    install(app: App) {
        app.directive('tip', vTip);
        // Lancer l'interception après le premier mount
        if (typeof window !== 'undefined') {
            // On attend que le DOM soit prêt
            const init = () => installTitleInterception();
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init, { once: true });
            } else {
                init();
            }
        }
    },
};
