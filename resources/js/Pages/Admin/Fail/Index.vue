<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import FileUploader from '@/Components/Admin/FileUploader.vue'
import ConfirmModal from '@/Components/Admin/ConfirmModal.vue'
import Pagination from '@/Components/Shared/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  mediaFiles: { type: Object, required: true },
})

const uploadForm = useForm({
  file: null,
})

function submitUpload() {
  if (!uploadForm.file) return
  uploadForm.post(route('admin.fail.store'), {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset()
    },
  })
}

const showDeleteModal = ref(false)
const deletingId = ref(null)
const deletingName = ref('')

function confirmDelete(file) {
  deletingId.value = file.id
  deletingName.value = file.original_name
  showDeleteModal.value = true
}

function handleDelete() {
  router.delete(route('admin.fail.destroy', deletingId.value), {
    onSuccess: () => {
      showDeleteModal.value = false
      deletingId.value = null
      deletingName.value = ''
    },
    preserveScroll: true,
  })
}

function cancelDelete() {
  showDeleteModal.value = false
  deletingId.value = null
  deletingName.value = ''
}

function formatSize(bytes) {
  if (!bytes) return '-'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

function getTypeBadge(type) {
  if (!type) return { label: 'Fail', class: 'bg-slate-100 text-slate-600' }
  if (type.includes('pdf')) return { label: 'PDF', class: 'bg-red-100 text-red-700' }
  if (type.includes('image') || type.startsWith('image/')) return { label: 'Imej', class: 'bg-blue-100 text-blue-700' }
  return { label: type.split('/').pop()?.toUpperCase() ?? 'Fail', class: 'bg-slate-100 text-slate-600' }
}

function isImage(type) {
  return type && (type.includes('image') || type.startsWith('image/'))
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Muat Naik Fail</h1>
      <p class="text-sm text-slate-500 mt-1">Urus semua fail yang telah dimuat naik</p>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
      <h2 class="text-sm font-bold text-slate-900 mb-4">Muat Naik Fail Baru</h2>
      <FileUploader
        v-model="uploadForm.file"
        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
        :max-size-mb="20"
      />
      <p v-if="uploadForm.errors.file" class="text-red-500 text-sm mt-2">{{ uploadForm.errors.file }}</p>

      <div v-if="uploadForm.file" class="mt-4">
        <button
          type="button"
          :disabled="uploadForm.processing"
          class="inline-flex items-center gap-2 bg-primary text-background-dark font-bold px-5 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity disabled:opacity-60"
          @click="submitUpload"
        >
          <svg v-if="uploadForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          <span class="material-symbols-outlined text-lg" v-if="!uploadForm.processing">upload</span>
          {{ uploadForm.processing ? 'Memuat naik...' : 'Muat Naik' }}
        </button>
      </div>
    </div>

    <!-- Files Grid -->
    <div v-if="mediaFiles.data.length === 0" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center">
      <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">folder_open</span>
      <p class="text-slate-500 font-medium">Tiada fail dimuat naik</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div
        v-for="file in mediaFiles.data"
        :key="file.id"
        class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden group"
      >
        <!-- Preview -->
        <div class="h-32 bg-slate-50 flex items-center justify-center relative">
          <img
            v-if="isImage(file.mime_type) && file.path"
            :src="`/storage/${file.path}`"
            :alt="file.original_name"
            class="h-full w-full object-cover"
          />
          <span v-else class="material-symbols-outlined text-4xl text-slate-300">insert_drive_file</span>

          <!-- Delete overlay -->
          <button
            type="button"
            class="absolute top-2 right-2 flex h-7 w-7 items-center justify-center rounded-lg bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
            @click="confirmDelete(file)"
          >
            <span class="material-symbols-outlined text-sm">delete</span>
          </button>
        </div>

        <!-- Info -->
        <div class="p-3">
          <p class="text-sm font-semibold text-slate-900 truncate" :title="file.original_name">{{ file.original_name }}</p>
          <div class="flex items-center gap-2 mt-2">
            <span
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
              :class="getTypeBadge(file.mime_type).class"
            >
              {{ getTypeBadge(file.mime_type).label }}
            </span>
            <span class="text-xs text-slate-400">{{ formatSize(file.size) }}</span>
          </div>
          <p class="text-xs text-slate-400 mt-1">{{ formatDate(file.created_at) }}</p>

          <!-- View link -->
          <a
            v-if="file.path"
            :href="`/storage/${file.path}`"
            target="_blank"
            class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-primary hover:underline"
          >
            <span class="material-symbols-outlined text-sm">open_in_new</span>
            Buka fail
          </a>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination v-if="mediaFiles.data.length > 0" :links="mediaFiles.links" />

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Padam Fail"
      :message="`Adakah anda pasti ingin memadam fail &quot;${deletingName}&quot;? Tindakan ini tidak boleh dibatalkan.`"
      confirm-text="Padam"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>
