<script setup>
defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  icon: { type: String, required: true },
  color: { type: String, default: 'green' },
})
</script>

<template>
  <div class="rounded-2xl bg-white border border-slate-100 p-6 flex items-center gap-4 shadow-sm">
    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary/10">
      <span class="material-symbols-outlined text-2xl text-primary">{{ icon }}</span>
    </div>
    <div class="min-w-0">
      <p class="text-3xl font-black text-slate-900">{{ value }}</p>
      <p class="text-sm text-slate-500 mt-0.5 truncate">{{ title }}</p>
    </div>
  </div>
</template>
