<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import FileUploader from '@/Components/Admin/FileUploader.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  event: { type: Object, required: true },
})

const form = useForm({
  title: props.event.title ?? '',
  description: props.event.description ?? '',
  start_datetime: props.event.start_datetime
    ? props.event.start_datetime.slice(0, 16)
    : '',
  end_datetime: props.event.end_datetime
    ? props.event.end_datetime.slice(0, 16)
    : '',
  location: props.event.location ?? '',
  is_published: props.event.is_published ?? true,
  is_featured: props.event.is_featured ?? false,
  cover_image: null,
})

function submit() {
  form.put(route('admin.aktiviti.update', props.event.id), { forceFormData: true })
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.aktiviti.index')"
        class="flex items-center justify-center h-9 w-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
      >
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </Link>
      <div>
        <h1 class="text-2xl font-black text-slate-900">Edit Aktiviti</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kemaskini maklumat aktiviti</p>
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

            <!-- Penerangan -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penerangan</label>
              <textarea
                v-model="form.description"
                rows="5"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none"
              />
              <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Tarikh -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarikh Mula <span class="text-red-500">*</span></label>
                <input
                  v-model="form.start_datetime"
                  type="datetime-local"
                  required
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                />
                <p v-if="form.errors.start_datetime" class="text-red-500 text-sm mt-1">{{ form.errors.start_datetime }}</p>
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tarikh Tamat (pilihan)</label>
                <input
                  v-model="form.end_datetime"
                  type="datetime-local"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                />
                <p v-if="form.errors.end_datetime" class="text-red-500 text-sm mt-1">{{ form.errors.end_datetime }}</p>
              </div>
            </div>

            <!-- Lokasi -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Lokasi</label>
              <input
                v-model="form.location"
                type="text"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              />
              <p v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</p>
            </div>

            <!-- Gambar Sedia Ada -->
            <div v-if="event.cover_image_path && !form.cover_image">
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gambar Sedia Ada</label>
              <img
                :src="`/storage/${event.cover_image_path}`"
                :alt="event.title"
                class="h-32 w-auto rounded-xl object-cover border border-slate-200"
              />
            </div>

            <!-- Gambar Penutup -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                {{ event.cover_image_path ? 'Ganti Gambar Penutup' : 'Gambar Penutup' }}
              </label>
              <FileUploader
                v-model="form.cover_image"
                accept="image/*"
                :max-size-mb="5"
              />
              <p v-if="form.errors.cover_image" class="text-red-500 text-sm mt-1">{{ form.errors.cover_image }}</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900">Tetapan</h3>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="form.is_published"
                type="checkbox"
                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-slate-700">Terbitkan sekarang</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="form.is_featured"
                type="checkbox"
                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-slate-700">Tampilkan (Featured)</span>
            </label>
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
              {{ form.processing ? 'Menyimpan...' : 'Kemaskini Aktiviti' }}
            </button>
            <Link
              :href="route('admin.aktiviti.index')"
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
