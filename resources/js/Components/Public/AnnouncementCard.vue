<template>
  <div class="rounded-xl bg-white border border-slate-100 shadow-sm p-5 space-y-3">
    <!-- Top row: category + pin + date -->
    <div class="flex items-center justify-between gap-2">
      <div class="flex items-center gap-2 flex-wrap">
        <!-- Category badge -->
        <span :class="['text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full', categoryClass]">
          {{ categoryLabel }}
        </span>
        <!-- Pin icon -->
        <span v-if="announcement.is_pinned" class="material-symbols-outlined text-primary text-sm" title="Disematkan">
          push_pin
        </span>
      </div>
      <!-- Date -->
      <span class="text-[10px] text-slate-400 shrink-0">
        {{ formatDate(announcement.published_at) }}
      </span>
    </div>

    <!-- Title -->
    <h3 class="font-bold text-slate-900" :class="compact ? 'text-sm' : 'text-base'">
      {{ announcement.title }}
    </h3>

    <!-- Body -->
    <p class="text-sm text-slate-600 line-clamp-3">{{ announcement.body }}</p>

    <!-- Attachment -->
    <div v-if="announcement.attachment_path">
      <!-- PDF attachment -->
      <a
        v-if="announcement.attachment_type === 'pdf' || isPdf"
        :href="announcement.attachment_path"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"
      >
        <span class="material-symbols-outlined text-sm">attach_file</span>
        Lihat Dokumen
      </a>
      <!-- Image attachment -->
      <div v-else class="mt-2 rounded-lg overflow-hidden border border-slate-100">
        <img
          :src="announcement.attachment_path"
          :alt="announcement.title"
          class="w-full h-32 object-cover"
          loading="lazy"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  announcement: {
    type: Object,
    required: true,
  },
  compact: {
    type: Boolean,
    default: false,
  },
})

const categoryConfig = {
  Aktiviti: 'bg-primary/10 text-primary',
  Pengumuman: 'bg-blue-50 text-blue-600',
  Kebajikan: 'bg-emerald-50 text-emerald-600',
  Am: 'bg-slate-100 text-slate-600',
}

const categoryClass = computed(() => {
  return categoryConfig[props.announcement.category] ?? 'bg-slate-100 text-slate-600'
})

const categoryLabel = computed(() => props.announcement.category ?? 'Am')

const isPdf = computed(() => {
  if (!props.announcement.attachment_path) return false
  return props.announcement.attachment_path.toLowerCase().endsWith('.pdf')
})

const MALAY_MONTHS = [
  'Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun',
  'Jul', 'Ogs', 'Sep', 'Okt', 'Nov', 'Dis'
]

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return `${date.getDate()} ${MALAY_MONTHS[date.getMonth()]} ${date.getFullYear()}`
}
</script>
