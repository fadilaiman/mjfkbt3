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
  announcements: { type: Object, required: true },
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
  router.delete(route('admin.pengumuman.destroy', deletingId.value), {
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

const categoryMap = {
  aktiviti: { label: 'Aktiviti', class: 'bg-blue-100 text-blue-700' },
  pengumuman: { label: 'Pengumuman', class: 'bg-amber-100 text-amber-700' },
  kebajikan: { label: 'Kebajikan', class: 'bg-emerald-100 text-emerald-700' },
  am: { label: 'Am', class: 'bg-slate-100 text-slate-600' },
}

function getCategoryInfo(cat) {
  return categoryMap[cat] ?? { label: cat, class: 'bg-slate-100 text-slate-600' }
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

const columns = [
  { key: 'title', label: 'Tajuk' },
  { key: 'category', label: 'Kategori' },
  { key: 'is_pinned', label: 'Ditandai' },
  { key: 'is_published', label: 'Status' },
  { key: 'published_at', label: 'Tarikh' },
]
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-black text-slate-900">Pengumuman</h1>
        <p class="text-sm text-slate-500 mt-1">Urus semua pengumuman masjid</p>
      </div>
      <Link
        :href="route('admin.pengumuman.create')"
        class="inline-flex items-center gap-2 bg-primary text-background-dark font-bold px-4 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity"
      >
        <span class="material-symbols-outlined text-lg">add</span>
        Tambah Baru
      </Link>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <DataTable :columns="columns" :rows="announcements.data" empty-message="Tiada pengumuman ditemui">
        <!-- Tajuk -->
        <template #cell-title="{ value }">
          <span class="font-medium text-slate-900 block max-w-xs truncate">{{ value }}</span>
        </template>

        <!-- Kategori -->
        <template #cell-category="{ value }">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
            :class="getCategoryInfo(value).class"
          >
            {{ getCategoryInfo(value).label }}
          </span>
        </template>

        <!-- Ditandai -->
        <template #cell-is_pinned="{ value }">
          <span
            class="material-symbols-outlined text-xl"
            :class="value ? 'text-amber-500' : 'text-slate-200'"
            :title="value ? 'Ditandai' : 'Tidak ditandai'"
          >push_pin</span>
        </template>

        <!-- Status -->
        <template #cell-is_published="{ value }">
          <StatusBadge :active="value" true-text="Diterbitkan" false-text="Draf" />
        </template>

        <!-- Tarikh -->
        <template #cell-published_at="{ value }">
          <span class="text-slate-500 text-xs">{{ formatDate(value) }}</span>
        </template>

        <!-- Tindakan -->
        <template #row-actions="{ row }">
          <div class="flex items-center justify-end gap-2">
            <Link
              :href="route('admin.pengumuman.edit', row.id)"
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
        <Pagination :links="announcements.links" />
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Padam Pengumuman"
      :message="`Adakah anda pasti ingin memadam pengumuman &quot;${deletingTitle}&quot;? Tindakan ini tidak boleh dibatalkan.`"
      confirm-text="Padam"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>
