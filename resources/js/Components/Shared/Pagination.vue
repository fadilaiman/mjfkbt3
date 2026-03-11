<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex items-center justify-center gap-1 mt-8">
        <template v-for="(link, index) in links" :key="index">
            <Link
                v-if="link.url"
                :href="link.url"
                class="flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg text-sm font-medium transition-colors"
                :class="{
                    'bg-primary text-background-dark font-bold shadow-sm': link.active,
                    'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-primary/50 hover:text-primary': !link.active,
                }"
                v-html="link.label"
                preserve-scroll
            />
            <span
                v-else
                class="flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg text-sm font-medium text-slate-400 dark:text-slate-600 cursor-not-allowed"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
