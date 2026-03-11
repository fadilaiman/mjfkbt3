<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmModal from '@/Components/Admin/ConfirmModal.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  posts: { type: Array, required: true },
  pageUrl: { type: String, default: 'https://www.facebook.com/mjfkbt3' },
})

const addForm = useForm({ facebook_url: '' })
const showDeleteModal = ref(false)
const deletingPost = ref(null)

function submitAdd() {
  addForm.post(route('admin.kandungan.facebook.store'), {
    preserveScroll: true,
    onSuccess: () => addForm.reset(),
  })
}

function confirmDelete(post) {
  deletingPost.value = post
  showDeleteModal.value = true
}

function handleDelete() {
  router.delete(route('admin.kandungan.facebook.destroy', deletingPost.value.id), {
    preserveScroll: true,
    onFinish: () => {
      showDeleteModal.value = false
      deletingPost.value = null
    },
  })
}

function toggleVisibility(post) {
  router.put(route('admin.kandungan.toggle', { type: 'fb_posts', id: post.id }), {}, {
    preserveScroll: true,
  })
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('ms-MY', {
    day: '2-digit', month: 'short', year: 'numeric',
  })
}

function truncateUrl(url, len = 60) {
  if (!url) return '-'
  return url.length > len ? url.slice(0, len) + '...' : url
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-black text-slate-900">Kandungan Facebook</h1>
        <p class="text-sm text-slate-500 mt-1">Tambah post Facebook menggunakan URL awam</p>
      </div>
      <a
        :href="pageUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#1877F2] text-white text-sm font-semibold hover:bg-[#166FE5] transition-colors"
      >
        <span class="material-symbols-outlined text-base">open_in_new</span>
        Buka Halaman Facebook
      </a>
    </div>

    <!-- How it works info -->
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-6 flex items-start gap-4">
      <span class="material-symbols-outlined text-2xl text-blue-500 shrink-0 mt-0.5">info</span>
      <div>
        <p class="text-sm font-semibold text-slate-900">Cara menambah post Facebook</p>
        <ol class="text-xs text-slate-600 mt-1.5 space-y-1 list-decimal ml-4">
          <li>Buka <a :href="pageUrl" target="_blank" class="text-blue-600 underline">halaman Facebook masjid</a></li>
          <li>Klik pada mana-mana post yang ingin dipaparkan</li>
          <li>Salin URL dari bar alamat (contoh: <code class="bg-slate-100 px-1 rounded text-xs">https://www.facebook.com/mjfkbt3/posts/...</code>)</li>
          <li>Tampal URL di bawah dan klik "Tambah"</li>
        </ol>
        <p class="text-xs text-slate-500 mt-2">Hanya post awam (public) yang boleh dipaparkan. Post mesti dari halaman yang sah.</p>
      </div>
    </div>

    <!-- Add URL Form -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
      <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-lg text-primary">add_link</span>
        Tambah Post Baharu
      </h2>
      <form @submit.prevent="submitAdd" class="flex gap-3">
        <div class="flex-1">
          <input
            v-model="addForm.facebook_url"
            type="url"
            placeholder="https://www.facebook.com/mjfkbt3/posts/..."
            class="w-full rounded-xl border-slate-200 text-sm focus:border-primary focus:ring-primary"
            :disabled="addForm.processing"
          />
          <p v-if="addForm.errors.facebook_url" class="text-red-500 text-xs mt-1">
            {{ addForm.errors.facebook_url }}
          </p>
        </div>
        <button
          type="submit"
          :disabled="addForm.processing || !addForm.facebook_url"
          class="px-5 py-2.5 bg-primary text-background-dark font-bold rounded-xl text-sm hover:brightness-95 transition disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
        >
          <span v-if="addForm.processing">Menambah...</span>
          <span v-else>Tambah Post</span>
        </button>
      </form>
    </div>

    <!-- Post List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-900">
          Post Tersimpan
          <span class="ml-2 bg-slate-100 text-slate-600 rounded-full px-2.5 py-0.5 text-xs font-semibold">
            {{ posts.length }}
          </span>
        </h2>
      </div>

      <!-- Empty state -->
      <div v-if="posts.length === 0" class="py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">thumb_up</span>
        <p class="text-slate-500 font-medium">Tiada post ditambah lagi</p>
        <p class="text-slate-400 text-sm mt-1">Tampal URL post Facebook di atas untuk mula</p>
      </div>

      <!-- Post rows -->
      <div v-else class="divide-y divide-slate-50">
        <div
          v-for="post in posts"
          :key="post.id"
          class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors"
          :class="{ 'opacity-50': post.is_hidden }"
        >
          <!-- Facebook icon -->
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#1877F2]/10">
            <span class="material-symbols-outlined text-[#1877F2]">thumb_up</span>
          </div>

          <!-- URL + meta -->
          <div class="flex-1 min-w-0">
            <a
              :href="post.permalink_url"
              target="_blank"
              rel="noopener noreferrer"
              class="text-sm font-medium text-slate-700 hover:text-primary truncate block"
            >
              {{ truncateUrl(post.permalink_url) }}
            </a>
            <div class="flex items-center gap-3 mt-1">
              <span class="text-xs text-slate-400">{{ formatDate(post.published_at) }}</span>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                :class="post.post_type === 'video' ? 'bg-red-100 text-red-700' : post.post_type === 'photo' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'"
              >
                {{ post.post_type }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 shrink-0">
            <!-- Toggle visibility -->
            <button
              type="button"
              class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
              :class="post.is_hidden
                ? 'bg-slate-100 text-slate-500 hover:bg-slate-200'
                : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
              @click="toggleVisibility(post)"
            >
              <span class="material-symbols-outlined text-sm">
                {{ post.is_hidden ? 'visibility_off' : 'visibility' }}
              </span>
              {{ post.is_hidden ? 'Tersembunyi' : 'Dipapar' }}
            </button>

            <!-- Delete -->
            <button
              type="button"
              class="p-1.5 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors"
              @click="confirmDelete(post)"
            >
              <span class="material-symbols-outlined text-base">delete</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Padam Post Facebook"
      :message="`Anda pasti ingin memadam post ini?\n\n${deletingPost?.permalink_url}`"
      confirm-text="Ya, Padam"
      @confirm="handleDelete"
      @cancel="showDeleteModal = false"
    />
  </div>
</template>
