<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue';
import { CheckCircle, X } from 'lucide-vue-next';

interface Props {
    show: boolean;
    title?: string;
    message?: string;
    duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Success!',
    message: 'Your message has been sent successfully.',
    duration: 5000
});

const emit = defineEmits<{
    close: []
}>();

const visible = ref(false);
const animate = ref(false);

onMounted(() => {
    if (props.show) {
        showPopup();
    }
});

const showPopup = async () => {
    visible.value = true;
    await nextTick();
    animate.value = true;
    
    // Auto close after duration
    setTimeout(() => {
        closePopup();
    }, props.duration);
};

const closePopup = () => {
    animate.value = false;
    setTimeout(() => {
        visible.value = false;
        emit('close');
    }, 300);
};

// Watch for show prop changes
watch(() => props.show, (newVal) => {
    if (newVal) {
        showPopup();
    } else {
        closePopup();
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="visible"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="background-color: rgba(0, 0, 0, 0.5);"
                @click="closePopup"
            >
                <Transition
                    enter-active-class="transition-all duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-300"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div 
                        v-if="animate"
                        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
                        @click.stop
                    >
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <CheckCircle class="w-8 h-8" />
                                    <h3 class="text-xl font-bold">{{ title }}</h3>
                                </div>
                                <button 
                                    @click="closePopup"
                                    class="hover:bg-white/20 rounded-full p-1 transition-colors"
                                >
                                    <X class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ message }}
                            </p>
                            
                            <!-- Action Button -->
                            <div class="mt-6 flex justify-end">
                                <button 
                                    @click="closePopup"
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200"
                                >
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
