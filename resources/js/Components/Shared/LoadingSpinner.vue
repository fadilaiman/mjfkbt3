<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'h-5 w-5 border-2';
        case 'md':
            return 'h-8 w-8 border-[3px]';
        case 'lg':
            return 'h-12 w-12 border-4';
        default:
            return 'h-8 w-8 border-[3px]';
    }
});
</script>

<template>
    <div class="flex items-center justify-center">
        <div
            :class="[sizeClasses, 'animate-spin rounded-full border-primary border-t-transparent']"
        ></div>
    </div>
</template>
