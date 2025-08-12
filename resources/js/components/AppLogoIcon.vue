<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img 
        src="/output-onlinepngtools.png" 
        alt="FotoMimi Logo" 
        :class="className" 
        v-bind="$attrs"
    />
</template>
