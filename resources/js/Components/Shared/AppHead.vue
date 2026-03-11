<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Head :title="title" />
</template>
