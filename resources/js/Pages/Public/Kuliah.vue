<template>
  <div>
    <AppHead title="Rakaman Kuliah" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Page Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
          <span class="material-symbols-outlined text-2xl text-primary">history</span>
        </div>
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Rakaman Kuliah</h1>
          <p v-if="videos.meta?.total" class="text-sm text-slate-500 mt-0.5">
            <span class="inline-flex items-center gap-1 bg-primary/10 text-primary font-bold px-2 py-0.5 rounded-full text-xs">
              {{ videos.meta.total }} video
            </span>
          </p>
        </div>
      </div>

      <!-- Video Grid -->
      <div v-if="videos.data && videos.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <VideoCard
          v-for="video in videos.data"
          :key="video.video_id"
          :video="video"
        />
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else
        icon="video_library"
        title="Tiada rakaman kuliah buat masa ini"
        description="Sila semak semula kemudian untuk video kuliah terbaru."
      />

      <!-- Pagination -->
      <Pagination v-if="videos.links" :links="videos.links" />

    </div>
  </div>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import EmptyState from '@/Components/Shared/EmptyState.vue'
import Pagination from '@/Components/Shared/Pagination.vue'
import VideoCard from '@/Components/Public/VideoCard.vue'

defineOptions({ layout: PublicLayout })

defineProps({
  videos: {
    type: Object,
    required: true,
  },
})
</script>
