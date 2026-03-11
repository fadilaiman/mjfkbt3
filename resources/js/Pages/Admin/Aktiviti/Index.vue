<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import DataTable from '@/Components/Admin/DataTable.vue'
import StatusBadge from '@/Components/Admin/StatusBadge.vue'
import ConfirmModal from '@/Components/Admin/ConfirmModal.vue'
import Pagination from '@/Components/Shared/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  events: { type: Object, required: true },
})

const showDeleteModal = ref(false)
const deletingId = ref(null)
const deletingTitle = ref('')

function confirmDelete(row) {
  deletingId.value = row.id
  deletingTitle.value = row.title
  showDeleteModal.value = true
}

function handleDelete() {
  router.delete(route('admin.aktiviti.destroy', deletingId.value), {
    onSuccess: () => {
      showDeleteModal.value = false
      deletingId.value = null
      deletingTitle.value = ''
    },
  })
}

function cancelDelete() {
  showDeleteModal.value = false
  deletingId.value = null
  deletingTitle.value = ''
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const columns = [
  { key: 'title', label: 'Tajuk' },
  { key: 'start_datetime', label: 'Tarikh Mula' },
  { key: 'location', label: 'Lokasi' },
  { key: 'source', label: 'Sumber' },
  { key: 'is_published', label: 'Status' },
]
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-black text-slate-900">Aktiviti</h1>
        <p class="text-sm text-slate-500 mt-1">Urus semua aktiviti masjid</p>
      </div>
      <Link
        :href="route('admin.aktiviti.create')"
        class="inline-flex items-center gap-2 bg-primary text-background-dark font-bold px-4 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity"
      >
        <span class="material-symbols-outlined text-lg">add</span>
        Tambah Baru
      </Link>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <DataTable :columns="columns" :rows="events.data" empty-message="Tiada aktiviti ditemui">
        <!-- Tajuk -->
        <template #cell-title="{ value }">
          <span class="font-medium text-slate-900 block max-w-xs truncate">{{ value }}</span>
        </template>

        <!-- Tarikh Mula -->
        <template #cell-start_datetime="{ value }">
          <span class="text-slate-500 text-xs whitespace-nowrap">{{ formatDate(value) }}</span>
        </template>

        <!-- Lokasi -->
        <template #cell-location="{ value }">
          <span class="text-slate-600 text-xs">{{ value ?? '-' }}</span>
        </template>

        <!-- Sumber -->
        <template #cell-source="{ value }">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
            :class="value === 'facebook' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'"
          >
            {{ value === 'facebook' ? 'Facebook' : 'Manual' }}
          </span>
        </template>

        <!-- Status -->
        <template #cell-is_published="{ value }">
          <StatusBadge :active="value" true-text="Diterbitkan" false-text="Draf" />
        </template>

        <!-- Tindakan -->
        <template #row-actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <Link
              :href="route('admin.aktiviti.edit', row.id)"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors"
            >
              <span class="material-symbols-outlined text-sm">edit</span>
              Edit
            </Link>
            <button
              type="button"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
              @click="confirmDelete(row)"
            >
              <span class="material-symbols-outlined text-sm">delete</span>
              Padam
            </button>
          </div>
        </template>
      </DataTable>

      <!-- Pagination -->
      <div class="px-4 py-4 border-t border-slate-50">
        <Pagination :links="events.links" />
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Padam Aktiviti"
      :message="`Adakah anda pasti ingin memadam aktiviti &quot;${deletingTitle}&quot;? Tindakan ini tidak boleh dibatalkan.`"
      confirm-text="Padam"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>
