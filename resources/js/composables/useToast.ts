import { ref, computed } from 'vue';

interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error';
    duration?: number;
}

const toasts = ref<Toast[]>([]);

export const useToast = () => {
    const addToast = (message: string, type: 'success' | 'error', duration = 3000) => {
        const id = Date.now().toString();
        const toast: Toast = { id, message, type, duration };
        toasts.value.push(toast);
        
        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                removeToast(id);
            }, duration);
        }
        
        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex(toast => toast.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message: string, duration?: number) => {
        return addToast(message, 'success', duration);
    };

    const error = (message: string, duration?: number) => {
        return addToast(message, 'error', duration);
    };

    const clearAll = () => {
        toasts.value = [];
    };

    return {
        toasts: computed(() => toasts.value),
        addToast,
        removeToast,
        success,
        error,
        clearAll
    };
};
