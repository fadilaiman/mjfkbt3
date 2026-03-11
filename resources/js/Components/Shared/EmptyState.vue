<script setup>
defineProps({
    icon: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 mb-4">
            <span class="material-symbols-outlined text-4xl text-primary">{{ icon }}</span>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ title }}</h3>
        <p v-if="description" class="text-sm text-slate-500 max-w-sm">{{ description }}</p>
    </div>
</template>
