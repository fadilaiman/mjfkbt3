<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Shared/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  logs: { type: Object, required: true },
})

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

const actionColors = {
  created_announcement: 'bg-emerald-100 text-emerald-700',
  updated_announcement: 'bg-blue-100 text-blue-700',
  deleted_announcement: 'bg-red-100 text-red-700',
  created_event: 'bg-emerald-100 text-emerald-700',
  updated_event: 'bg-blue-100 text-blue-700',
  deleted_event: 'bg-red-100 text-red-700',
  updated_donation: 'bg-blue-100 text-blue-700',
  toggled_content: 'bg-amber-100 text-amber-700',
  created_tiktok: 'bg-emerald-100 text-emerald-700',
  deleted_tiktok: 'bg-red-100 text-red-700',
  uploaded_media: 'bg-emerald-100 text-emerald-700',
  deleted_media: 'bg-red-100 text-red-700',
  updated_whatsapp_contact: 'bg-blue-100 text-blue-700',
}

function getActionClass(action) {
  return actionColors[action] ?? 'bg-slate-100 text-slate-600'
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Log Aktiviti</h1>
      <p class="text-sm text-slate-500 mt-1">Rekod semua tindakan yang dilakukan oleh admin</p>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Tarikh / Masa</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Admin</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tindakan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Penerangan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">Alamat IP</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="logs.data.length === 0">
              <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                <span class="material-symbols-outlined text-4xl block mb-2">history</span>
                Tiada log aktiviti ditemui
              </td>
            </tr>
            <tr
              v-for="(log, index) in logs.data"
              :key="log.id"
              class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
              :class="{ 'bg-slate-50/50': index % 2 === 1 }"
            >
              <!-- Tarikh / Masa -->
              <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">
                {{ formatDate(log.created_at) }}
              </td>

              <!-- Admin -->
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-bold">
                    {{ log.admin_user?.name?.charAt(0)?.toUpperCase() ?? 'A' }}
                  </div>
                  <span class="text-slate-700 font-medium text-sm">{{ log.admin_user?.name ?? '-' }}</span>
                </div>
              </td>

              <!-- Tindakan -->
              <td class="px-4 py-3">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap"
                  :class="getActionClass(log.action)"
                >
                  {{ log.action }}
                </span>
              </td>

              <!-- Penerangan -->
              <td class="px-4 py-3 text-slate-600 max-w-sm">
                {{ log.description }}
              </td>

              <!-- IP -->
              <td class="px-4 py-3 text-slate-400 text-xs font-mono whitespace-nowrap">
                {{ log.ip_address ?? '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-4 border-t border-slate-50">
        <Pagination :links="logs.links" />
      </div>
    </div>
  </div>
</template>
