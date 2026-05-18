import { ref, readonly } from 'vue';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

interface Toast {
    id: number;
    type: ToastType;
    message: string;
    duration: number;
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

export function useToast() {
    const add = (message: string, type: ToastType = 'info', duration = 4000) => {
        const id = ++nextId;
        toasts.value.push({ id, type, message, duration });
        setTimeout(() => remove(id), duration);
        return id;
    };

    const remove = (id: number) => {
        const idx = toasts.value.findIndex(t => t.id === id);
        if (idx !== -1) toasts.value.splice(idx, 1);
    };

    const success = (msg: string, duration?: number) => add(msg, 'success', duration);
    const error   = (msg: string, duration?: number) => add(msg, 'error',   duration);
    const warning = (msg: string, duration?: number) => add(msg, 'warning', duration);
    const info    = (msg: string, duration?: number) => add(msg, 'info',    duration);

    return { toasts: readonly(toasts), add, remove, success, error, warning, info };
}
