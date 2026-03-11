<template>
  <div class="flex items-center gap-3 flex-wrap">
    <a
      v-for="social in socials"
      :key="social.label"
      :href="social.url"
      target="_blank"
      rel="noopener noreferrer"
      :aria-label="social.label"
      :class="[
        'flex items-center justify-center rounded-full transition-all',
        sizeClasses,
        variantClasses,
      ]"
    >
      <span :class="['material-symbols-outlined', iconSizeClass]">{{ social.icon }}</span>
    </a>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (v) => ['sm', 'md', 'lg'].includes(v),
  },
  variant: {
    type: String,
    default: 'dark',
    validator: (v) => ['light', 'dark'].includes(v),
  },
})

const socials = [
  {
    label: 'YouTube',
    url: 'https://youtube.com/@masjidjamekfastabiqulkhairat',
    icon: 'youtube_activity',
  },
  {
    label: 'TikTok',
    url: 'https://www.tiktok.com/@mjfk.batu03',
    icon: 'video_library',
  },
  {
    label: 'Facebook',
    url: 'https://www.facebook.com/mjfkbt3/',
    icon: 'thumb_up',
  },
  {
    label: 'Instagram',
    url: 'https://www.instagram.com/mjfk.bt3',
    icon: 'photo_camera',
  },
  {
    label: 'Linktree',
    url: 'https://linktr.ee/mjfkbt3',
    icon: 'link',
  },
]

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'h-8 w-8'
    case 'lg': return 'h-12 w-12'
    default: return 'h-10 w-10'
  }
})

const iconSizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'text-base'
    case 'lg': return 'text-2xl'
    default: return 'text-xl'
  }
})

const variantClasses = computed(() => {
  return props.variant === 'light'
    ? 'text-white/70 hover:text-white hover:bg-white/10'
    : 'text-slate-400 hover:text-primary hover:bg-primary/10'
})
</script>
