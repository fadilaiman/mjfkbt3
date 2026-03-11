<template>
  <div id="hero" class="relative overflow-hidden rounded-2xl bg-black shadow-2xl">
    <!-- LIVE STATE -->
    <template v-if="liveStatus?.is_live">
      <div class="aspect-video w-full">
        <iframe
          :src="`https://www.youtube.com/embed/${liveStatus.video_id}?autoplay=0`"
          class="w-full h-full"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
          title="Siaran Langsung"
        ></iframe>
      </div>
      <!-- Live Badge -->
      <div class="absolute top-4 left-4 flex items-center gap-2 rounded-full bg-red-600 px-3 py-1.5 shadow-lg">
        <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
        <span class="text-[10px] font-bold text-white uppercase tracking-widest">SEDANG BERSIARAN</span>
        <span v-if="liveStatus.concurrent_viewers > 0" class="text-[10px] text-white/80">
          ({{ liveStatus.concurrent_viewers.toLocaleString('ms-MY') }} penonton)
        </span>
      </div>
    </template>

    <!-- LATEST VIDEO STATE -->
    <template v-else-if="latestVideo">
      <!-- Thumbnail -->
      <div class="aspect-video w-full relative group cursor-pointer" @click="handlePlay">
        <img
          v-if="!playing"
          :src="latestVideo.thumbnail_url"
          :alt="latestVideo.title"
          class="w-full h-full object-cover"
        />
        <!-- Gradient overlay -->
        <div v-if="!playing" class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

        <!-- Iframe when playing -->
        <iframe
          v-if="playing"
          :src="`https://www.youtube.com/embed/${latestVideo.video_id}?autoplay=1`"
          class="absolute inset-0 w-full h-full"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
          title="Video Terbaru"
        ></iframe>

        <!-- Video info (bottom) -->
        <div v-if="!playing" class="absolute bottom-0 left-0 right-0 p-6">
          <span class="text-[10px] font-black uppercase tracking-widest text-primary bg-primary/20 rounded-full px-3 py-1 mb-2 inline-block">
            Video Terbaru
          </span>
          <h2 class="text-xl md:text-2xl font-bold text-white line-clamp-2 mt-2">
            {{ latestVideo.title }}
          </h2>
          <p class="text-slate-300 text-sm mt-1">
            <span v-if="latestVideo.view_count">{{ formatViews(latestVideo.view_count) }} tontonan</span>
            <span v-if="latestVideo.view_count && latestVideo.published_at"> • </span>
            <span v-if="latestVideo.published_at">{{ formatDate(latestVideo.published_at) }}</span>
          </p>
        </div>

        <!-- Play button (centered) -->
        <div v-if="!playing" class="absolute inset-0 flex items-center justify-center">
          <button
            @click.stop="handlePlay"
            class="flex h-20 w-20 items-center justify-center rounded-full bg-primary text-background-dark shadow-xl hover:scale-110 transition-transform"
            aria-label="Main video"
          >
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1">play_arrow</span>
          </button>
        </div>
      </div>
    </template>

    <!-- SKELETON / EMPTY STATE -->
    <template v-else>
      <div class="aspect-video w-full bg-slate-800 flex items-center justify-center">
        <div class="text-center space-y-3">
          <div class="w-16 h-16 rounded-full bg-slate-700 flex items-center justify-center mx-auto animate-pulse">
            <span class="material-symbols-outlined text-slate-500 text-3xl">videocam_off</span>
          </div>
          <p class="text-slate-500 text-sm font-medium">Tiada siaran langsung atau video tersedia</p>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  liveStatus: {
    type: Object,
    default: null,
  },
  latestVideo: {
    type: Object,
    default: null,
  },
})

const playing = ref(false)

function handlePlay() {
  playing.value = true
}

function formatViews(count) {
  if (!count) return '0'
  if (count >= 1_000_000) return (count / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'J'
  if (count >= 1_000) return (count / 1_000).toFixed(1).replace(/\.0$/, '') + 'K'
  return count.toLocaleString('ms-MY')
}

function formatDate(dateStr) {
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
