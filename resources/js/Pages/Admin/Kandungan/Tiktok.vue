<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import ConfirmModal from '@/Components/Admin/ConfirmModal.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  videos: { type: Array, required: true },
})

const addForm = useForm({
  tiktok_url: '',
  title: '',
})

function submitAdd() {
  addForm.post(route('admin.kandungan.tiktok.store'), {
    onSuccess: () => {
      addForm.reset()
    },
  })
}

function toggleVisibility(video) {
  router.put(route('admin.kandungan.toggle', { type: 'tiktok_videos', id: video.id }), {}, {
    preserveScroll: true,
  })
}

const showDeleteModal = ref(false)
const deletingId = ref(null)

function confirmDelete(video) {
  deletingId.value = video.id
  showDeleteModal.value = true
}

function handleDelete() {
  router.delete(route('admin.kandungan.tiktok.destroy', deletingId.value), {
    onSuccess: () => {
      showDeleteModal.value = false
      deletingId.value = null
    },
    preserveScroll: true,
  })
}

function cancelDelete() {
  showDeleteModal.value = false
  deletingId.value = null
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Kandungan TikTok</h1>
      <p class="text-sm text-slate-500 mt-1">Urus video TikTok secara manual</p>
    </div>

    <!-- Add Form -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
      <h2 class="text-sm font-bold text-slate-900 mb-4">Tambah Video TikTok</h2>
      <form @submit.prevent="submitAdd" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">URL TikTok <span class="text-red-500">*</span></label>
            <input
              v-model="addForm.tiktok_url"
              type="url"
              required
              placeholder="https://www.tiktok.com/@user/video/..."
              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
            />
            <p v-if="addForm.errors.tiktok_url" class="text-red-500 text-sm mt-1">{{ addForm.errors.tiktok_url }}</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tajuk (pilihan)</label>
            <input
              v-model="addForm.title"
              type="text"
              placeholder="Tajuk video..."
              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
            />
            <p v-if="addForm.errors.title" class="text-red-500 text-sm mt-1">{{ addForm.errors.title }}</p>
          </div>
        </div>
        <div>
          <button
            type="submit"
            :disabled="addForm.processing"
            class="inline-flex items-center gap-2 bg-primary text-background-dark font-bold px-5 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity disabled:opacity-60"
          >
            <svg v-if="addForm.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <span class="material-symbols-outlined text-lg" v-if="!addForm.processing">add</span>
            {{ addForm.processing ? 'Menambah...' : 'Tambah' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Thumbnail</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tajuk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">URL</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="videos.length === 0">
              <td colspan="4" class="px-4 py-12 text-center text-slate-400">Tiada video TikTok ditambah</td>
            </tr>
            <tr
              v-for="(video, index) in videos"
              :key="video.id"
              class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
              :class="{ 'bg-slate-50/50': index % 2 === 1 }"
            >
              <!-- Thumbnail -->
              <td class="px-4 py-3">
                <img
                  v-if="video.thumbnail_url"
                  :src="video.thumbnail_url"
                  :alt="video.title"
                  class="h-[60px] w-[60px] object-cover rounded-lg"
                />
                <div
                  v-else
                  class="h-[60px] w-[60px] rounded-lg bg-slate-100 flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-slate-400">video_library</span>
                </div>
              </td>

              <!-- Tajuk -->
              <td class="px-4 py-3">
                <p class="font-medium text-slate-900 max-w-xs truncate">
                  {{ video.title ?? video.author_name ?? '-' }}
                </p>
                <p v-if="video.author_name && video.title" class="text-xs text-slate-400 mt-0.5">@{{ video.author_name }}</p>
              </td>

              <!-- URL -->
              <td class="px-4 py-3">
                <a
                  :href="video.tiktok_url"
                  target="_blank"
                  class="text-xs text-primary hover:underline max-w-[180px] truncate block"
                >
                  {{ video.tiktok_url?.slice(0, 40) }}{{ (video.tiktok_url?.length ?? 0) > 40 ? '...' : '' }}
                </a>
              </td>

              <!-- Tindakan -->
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    type="button"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                    :class="video.is_hidden
                      ? 'bg-slate-100 text-slate-500 hover:bg-slate-200'
                      : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
                    @click="toggleVisibility(video)"
                  >
                    <span class="material-symbols-outlined text-sm">
                      {{ video.is_hidden ? 'visibility_off' : 'visibility' }}
                    </span>
                    {{ video.is_hidden ? 'Tersembunyi' : 'Dipapar' }}
                  </button>
                  <button
                    type="button"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-colors"
                    @click="confirmDelete(video)"
                  >
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Padam
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Padam Video TikTok"
      message="Adakah anda pasti ingin memadam video TikTok ini? Tindakan ini tidak boleh dibatalkan."
      confirm-text="Padam"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />
  </div>
</template>
