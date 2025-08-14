<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next';

interface ToastProps {
    message: string;
    type: 'success' | 'error';
    duration?: number;
    onClose?: () => void;
}

const props = withDefaults(defineProps<ToastProps>(), {
    duration: 3000
});

const emit = defineEmits<{
    close: [];
}>();

const isVisible = ref(true);

const closeToast = () => {
    isVisible.value = false;
    setTimeout(() => {
        emit('close');
        props.onClose?.();
    }, 300); // Wait for fade out animation
};

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(closeToast, props.duration);
    }
});

const toastClasses = computed(() => {
    const baseClasses = 'fixed top-4 right-4 z-50 transform transition-all duration-300 ease-in-out';
    const visibilityClasses = isVisible.value ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0';
    
    return `${baseClasses} ${visibilityClasses}`;
});

const contentClasses = computed(() => {
    const baseClasses = 'px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 border';
    
    if (props.type === 'success') {
        return `${baseClasses} bg-green-900 border-green-700 text-green-300`;
    } else {
        return `${baseClasses} bg-red-900 border-red-700 text-red-300`;
    }
});

const iconClasses = computed(() => {
    if (props.type === 'success') {
        return 'w-5 h-5 text-green-400';
    } else {
        return 'w-5 h-5 text-red-400';
    }
});
</script>

<template>
    <div :class="toastClasses">
        <div :class="contentClasses">
            <CheckCircle v-if="type === 'success'" :class="iconClasses" />
            <AlertCircle v-else :class="iconClasses" />
            <span class="flex-1">{{ message }}</span>
            <button
                @click="closeToast"
                class="text-gray-400 hover:text-gray-300 transition-colors"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
