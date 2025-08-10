<script setup lang="ts">
import { ref, onMounted } from 'vue';

const isDark = ref(false);

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark';
    document.documentElement.classList.toggle('dark', isDark.value);
});

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}
</script>

<template>
    <button 
        @click="toggleTheme"
        class="relative inline-flex items-center w-14 h-8 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
        :class="isDark ? 'bg-gray-600' : 'bg-gray-200'"
    >
        <!-- Sun Icon (Left) -->
        <div class="absolute left-1 text-yellow-500 text-sm">
            ☀️
        </div>
        
        <!-- Moon Icon (Right) -->
        <div class="absolute right-1 text-blue-400 text-sm">
            🌙
        </div>
        
        <!-- Sliding Knob -->
        <div 
            class="absolute w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300 ease-in-out"
            :class="isDark ? 'translate-x-7' : 'translate-x-1'"
        ></div>
    </button>
</template> 