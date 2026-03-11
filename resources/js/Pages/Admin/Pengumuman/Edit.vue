<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import FileUploader from '@/Components/Admin/FileUploader.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  announcement: { type: Object, required: true },
})

const form = useForm({
  title: props.announcement.title ?? '',
  body: props.announcement.body ?? '',
  category: props.announcement.category ?? 'am',
  is_pinned: props.announcement.is_pinned ?? false,
  is_published: props.announcement.is_published ?? true,
  published_at: props.announcement.published_at
    ? props.announcement.published_at.slice(0, 16)
    : '',
  expires_at: props.announcement.expires_at
    ? props.announcement.expires_at.slice(0, 16)
    : '',
  attachment: null,
})

function submit() {
  form.put(route('admin.pengumuman.update', props.announcement.id), { forceFormData: true })
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.pengumuman.index')"
        class="flex items-center justify-center h-9 w-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
      >
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </Link>
      <div>
        <h1 class="text-2xl font-black text-slate-900">Edit Pengumuman</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kemaskini pengumuman yang sedia ada</p>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Fields -->
        <div class="lg:col-span-2 space-y-5">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
            <!-- Tajuk -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tajuk <span class="text-red-500">*</span></label>
              <input
                v-model="form.title"
                type="text"
                required
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              />
              <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
            </div>

            <!-- Kandungan -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kandungan <span class="text-red-500">*</span></label>
              <textarea
                v-model="form.body"
                rows="6"
                required
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none"
              />
              <p v-if="form.errors.body" class="text-red-500 text-sm mt-1">{{ form.errors.body }}</p>
            </div>

            <!-- Lampiran Sedia Ada -->
            <div v-if="announcement.attachment_path && !form.attachment">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lampiran Sedia Ada</label>
              <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="material-symbols-outlined text-2xl text-primary">insert_drive_file</span>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-slate-700 truncate">
                    {{ announcement.attachment_path.split('/').pop() }}
                  </p>
                  <p class="text-xs text-slate-400">{{ announcement.attachment_type }}</p>
                </div>
                <a
                  :href="`/storage/${announcement.attachment_path}`"
                  target="_blank"
                  class="text-xs font-medium text-primary hover:underline"
                >
                  Lihat
                </a>
              </div>
            </div>

            <!-- Lampiran Baru -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                {{ announcement.attachment_path ? 'Ganti Lampiran' : 'Lampiran' }}
              </label>
              <FileUploader
                v-model="form.attachment"
                accept="image/*,.pdf"
                :max-size-mb="10"
              />
              <p v-if="form.errors.attachment" class="text-red-500 text-sm mt-1">{{ form.errors.attachment }}</p>
            </div>
          </div>
        </div>

        <!-- Sidebar Settings -->
        <div class="space-y-5">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900">Tetapan</h3>

            <!-- Kategori -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
              <select
                v-model="form.category"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              >
                <option value="aktiviti">Aktiviti</option>
                <option value="pengumuman">Pengumuman</option>
                <option value="kebajikan">Kebajikan</option>
                <option value="am">Am</option>
              </select>
              <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</p>
            </div>

            <!-- Tandakan sebagai Utama -->
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="form.is_pinned"
                type="checkbox"
                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-slate-700">Tandakan sebagai Utama</span>
            </label>

            <!-- Terbitkan sekarang -->
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="form.is_published"
                type="checkbox"
                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-slate-700">Terbitkan sekarang</span>
            </label>

            <!-- Tarikh Terbit -->
            <div v-if="!form.is_published">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarikh Terbit</label>
              <input
                v-model="form.published_at"
                type="datetime-local"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              />
              <p v-if="form.errors.published_at" class="text-red-500 text-sm mt-1">{{ form.errors.published_at }}</p>
            </div>

            <!-- Tarikh Tamat -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarikh Tamat (pilihan)</label>
              <input
                v-model="form.expires_at"
                type="datetime-local"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              />
              <p v-if="form.errors.expires_at" class="text-red-500 text-sm mt-1">{{ form.errors.expires_at }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-primary text-background-dark font-bold px-4 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity disabled:opacity-60 flex items-center justify-center gap-2"
            >
              <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ form.processing ? 'Menyimpan...' : 'Kemaskini Pengumuman' }}
            </button>
            <Link
              :href="route('admin.pengumuman.index')"
              class="w-full text-center text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl transition-colors"
            >
              Batal
            </Link>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
