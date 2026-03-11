<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'

defineOptions({ layout: AdminLayout })

defineProps({
  stats: {
    type: Object,
    required: true,
  },
  recent_logs: {
    type: Array,
    required: true,
  },
})

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<template>
  <div>
    <!-- Page Heading -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Dashboard</h1>
      <p class="text-sm text-slate-500 mt-1">Selamat datang ke Panel Pentadbir MJFKBT3</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
      <StatsCard
        title="Pengumuman Aktif"
        :value="stats.announcement_count"
        icon="campaign"
      />
      <StatsCard
        title="Aktiviti Akan Datang"
        :value="stats.event_count"
        icon="event"
      />
      <StatsCard
        title="Tabung Derma Aktif"
        :value="stats.donation_active"
        icon="volunteer_activism"
      />
      <StatsCard
        title="Video YouTube"
        :value="stats.youtube_video_count"
        icon="smart_display"
      />
      <StatsCard
        title="Catatan Facebook"
        :value="stats.fb_post_count"
        icon="thumb_up"
      />
      <StatsCard
        title="Video TikTok"
        :value="stats.tiktok_video_count"
        icon="video_library"
      />
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-xl text-primary">history</span>
        Aktiviti Terkini
      </h2>

      <div class="overflow-x-auto">
        <table v-if="recent_logs.length > 0" class="table-auto w-full text-sm">
          <thead>
            <tr class="border-b border-slate-100">
              <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tarikh</th>
              <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Admin</th>
              <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tindakan</th>
              <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Penerangan</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(log, index) in recent_logs"
              :key="log.id"
              class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
              :class="{ 'bg-slate-50/50': index % 2 === 1 }"
            >
              <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">{{ formatDate(log.created_at) }}</td>
              <td class="px-3 py-2.5 text-slate-700 font-medium">{{ log.admin_user?.name ?? '-' }}</td>
              <td class="px-3 py-2.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                  {{ log.action }}
                </span>
              </td>
              <td class="px-3 py-2.5 text-slate-600 max-w-xs truncate">{{ log.description }}</td>
            </tr>
          </tbody>
        </table>

        <div v-else class="py-12 text-center text-slate-400">
          <span class="material-symbols-outlined text-4xl block mb-2">history</span>
          Tiada aktiviti terkini
        </div>
      </div>
    </div>
  </div>
</template>
