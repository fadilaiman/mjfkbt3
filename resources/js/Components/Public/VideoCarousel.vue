<template>
  <section class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold flex items-center gap-2 text-slate-900">
        <span class="material-symbols-outlined text-primary">history</span>
        {{ title }}
      </h2>
      <Link
        href="/kuliah"
        class="text-sm font-bold text-primary flex items-center gap-1 hover:underline"
      >
        Lihat Semua
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </Link>
    </div>

    <!-- Videos horizontal scroll -->
    <div v-if="videos && videos.length > 0" class="flex gap-4 overflow-x-auto scrollbar-hide pb-4">
      <VideoCard
        v-for="video in videos"
        :key="video.video_id"
        :video="video"
      />
    </div>

    <!-- Empty state -->
    <div v-else class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-2xl border border-slate-100">
      <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">video_library</span>
      <p class="text-sm font-medium text-slate-500">Tiada rakaman kuliah tersedia</p>
      <p class="text-xs text-slate-400 mt-1">Sila semak semula kemudian</p>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import VideoCard from '@/Components/Public/VideoCard.vue'

defineProps({
  videos: {
    type: Array,
    default: () => [],
  },
  title: {
    type: String,
    default: 'Rakaman Kuliah',
  },
})
</script>

<style>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
