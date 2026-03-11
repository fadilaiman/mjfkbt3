<template>
  <div
    class="group cursor-pointer relative min-w-[180px] h-[320px] rounded-2xl overflow-hidden"
    @click="openVideo"
  >
    <!-- Background thumbnail -->
    <img
      v-if="video.thumbnail_url"
      :src="video.thumbnail_url"
      :alt="video.title"
      class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      loading="lazy"
    />
    <div v-else class="absolute inset-0 w-full h-full bg-slate-800"></div>

    <!-- Dark gradient overlay at bottom -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

    <!-- TikTok icon (top right) -->
    <div class="absolute top-3 right-3">
      <span class="material-symbols-outlined text-white text-xl opacity-80">play_circle</span>
    </div>

    <!-- Title (bottom) -->
    <div class="absolute bottom-4 left-3 right-3">
      <p class="text-white text-xs font-bold line-clamp-2 leading-snug">{{ video.title }}</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  video: {
    type: Object,
    required: true,
  },
})

function openVideo() {
  if (props.video.tiktok_url) {
    window.open(props.video.tiktok_url, '_blank', 'noopener,noreferrer')
  }
}
</script>
