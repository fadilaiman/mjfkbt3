<template>
  <div class="rounded-xl bg-white border border-slate-100 shadow-sm overflow-hidden">
    <!-- Cover image -->
    <div v-if="event.cover_image_path" class="aspect-video overflow-hidden">
      <img
        :src="event.cover_image_path"
        :alt="event.title"
        class="w-full h-full object-cover"
        loading="lazy"
      />
    </div>

    <!-- Content -->
    <div class="p-5 space-y-3">
      <!-- Source badge -->
      <span
        :class="[
          'text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full',
          event.source === 'facebook'
            ? 'bg-blue-50 text-blue-600'
            : 'bg-slate-100 text-slate-600'
        ]"
      >
        {{ event.source === 'facebook' ? 'Facebook' : 'Manual' }}
      </span>

      <!-- Title -->
      <h3 class="font-bold text-slate-900" :class="compact ? 'text-sm' : 'text-base'">
        {{ event.title }}
      </h3>

      <!-- Date -->
      <div class="flex items-center gap-1.5 text-xs text-slate-500">
        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">calendar_month</span>
        <span>{{ formatDate(event.start_datetime) }}</span>
        <span v-if="event.end_datetime && !isSameDay(event.start_datetime, event.end_datetime)">
          — {{ formatDate(event.end_datetime) }}
        </span>
      </div>

      <!-- Location -->
      <div v-if="event.location" class="flex items-start gap-1.5 text-xs text-slate-500">
        <span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">location_on</span>
        <span>{{ event.location }}</span>
      </div>

      <!-- Description -->
      <p v-if="event.description" class="text-sm text-slate-500 line-clamp-2">
        {{ event.description }}
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
  compact: {
    type: Boolean,
    default: false,
  },
})

const MALAY_DAYS = ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu']
const MALAY_MONTHS = [
  'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun',
  'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'
]

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const day = MALAY_DAYS[date.getDay()]
  const dayNum = date.getDate()
  const month = MALAY_MONTHS[date.getMonth()]
  const year = date.getFullYear()
  const hours = date.getHours().toString().padStart(2, '0')
  const minutes = date.getMinutes().toString().padStart(2, '0')

  if (hours === '00' && minutes === '00') {
    return `${day}, ${dayNum} ${month} ${year}`
  }
  return `${day}, ${dayNum} ${month} ${year}, ${hours}:${minutes}`
}

function isSameDay(dateStr1, dateStr2) {
  if (!dateStr1 || !dateStr2) return true
  const d1 = new Date(dateStr1)
  const d2 = new Date(dateStr2)
  return d1.getFullYear() === d2.getFullYear() &&
    d1.getMonth() === d2.getMonth() &&
    d1.getDate() === d2.getDate()
}
</script>
