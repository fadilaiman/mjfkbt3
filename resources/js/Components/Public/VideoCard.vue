<template>
  <div
    class="group cursor-pointer"
    :class="compact ? 'min-w-[220px]' : 'min-w-[280px] md:min-w-[320px]'"
    @click="openVideo"
  >
    <!-- Thumbnail -->
    <div class="relative aspect-video rounded-xl overflow-hidden mb-3">
      <img
        :src="video.thumbnail_url"
        :alt="video.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />

      <!-- Duration badge -->
      <div
        v-if="video.duration"
        class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-bold px-2 py-1 rounded"
      >
        {{ video.duration }}
      </div>

      <!-- Play overlay on hover -->
      <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
        <div class="opacity-0 group-hover:opacity-100 transition-opacity transform group-hover:scale-100 scale-75">
          <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary text-background-dark shadow-lg">
            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1">play_arrow</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Title -->
    <h3
      class="font-bold text-sm line-clamp-2 text-slate-900"
      :class="compact ? 'text-xs' : 'text-sm'"
    >
      {{ video.title }}
    </h3>

    <!-- Meta -->
    <p class="text-xs text-slate-500 mt-1">
      <span v-if="video.view_count">{{ formatViews(video.view_count) }} tontonan</span>
      <span v-if="video.view_count && video.published_at"> • </span>
      <span v-if="video.published_at">{{ formatRelativeDate(video.published_at) }}</span>
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  video: {
    type: Object,
    required: true,
  },
  compact: {
    type: Boolean,
    default: false,
  },
})

function openVideo() {
  window.open(`https://youtube.com/watch?v=${props.video.video_id}`, '_blank', 'noopener,noreferrer')
}

function formatViews(count) {
  if (!count) return '0'
  if (count >= 1_000_000) return (count / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'J'
  if (count >= 1_000) return (count / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return count.toLocaleString('ms-MY')
}

function formatRelativeDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffMs = now - date
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  if (diffDays === 0) return 'Hari ini'
  if (diffDays === 1) return 'Semalam'
  if (diffDays < 7) return `${diffDays} hari lalu`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} minggu lalu`
  if (diffDays < 365) return `${Math.floor(diffDays / 30)} bulan lalu`
  return `${Math.floor(diffDays / 365)} tahun lalu`
}
</script>
