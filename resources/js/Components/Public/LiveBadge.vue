<template>
  <div class="inline-flex items-center gap-2 rounded-full bg-red-600 px-3 py-1.5 shadow-lg">
    <!-- Animated dot -->
    <span class="h-2 w-2 rounded-full bg-white animate-pulse shrink-0"></span>
    <!-- Text -->
    <span class="text-[10px] font-bold text-white uppercase tracking-widest">SEDANG BERSIARAN</span>
    <!-- Viewer count -->
    <span v-if="viewerCount > 0" class="text-[10px] text-white/80">
      ({{ viewerCount.toLocaleString('ms-MY') }} penonton)
    </span>
  </div>
</template>

<script setup>
defineProps({
  viewerCount: {
    type: Number,
    default: 0,
  },
})
</script>
