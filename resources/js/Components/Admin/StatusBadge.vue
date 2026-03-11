<script setup>
defineProps({
  active: { type: Boolean, required: true },
  trueText: { type: String, default: 'Aktif' },
  falseText: { type: String, default: 'Tidak Aktif' },
})
</script>

<template>
  <span
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
    :class="active
      ? 'bg-emerald-100 text-emerald-700'
      : 'bg-slate-100 text-slate-500'"
  >
    {{ active ? trueText : falseText }}
  </span>
</template>
