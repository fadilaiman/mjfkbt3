<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import Pagination from '@/Components/Shared/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  videos: { type: Object, required: true },
})

function toggleVisibility(video) {
  router.put(route('admin.kandungan.toggle', { type: 'youtube_videos', id: video.id }), {}, {
    preserveScroll: true,
  })
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('ms-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  })
}

function formatViews(count) {
  if (!count && count !== 0) return '-'
  if (count >= 1000000) return (count / 1000000).toFixed(1) + 'J'
  if (count >= 1000) return (count / 1000).toFixed(1) + 'K'
  return count.toString()
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-black text-slate-900">Kandungan YouTube</h1>
        <p class="text-sm text-slate-500 mt-1">Urus kandungan video YouTube yang disegerakkan</p>
      </div>
    </div>

    <!-- Sync Info Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6 flex items-start gap-4">
      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100">
        <span class="material-symbols-outlined text-xl text-red-600">smart_display</span>
      </div>
      <div>
        <p class="text-sm font-semibold text-slate-900">Segerakkan dari YouTube</p>
        <p class="text-xs text-slate-500 mt-0.5">Video disegerakkan secara automatik melalui YouTube Data API. Togol butang untuk menyembunyikan atau memaparkan video di laman awam.</p>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="table-auto w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Thumbnail</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tajuk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tarikh Upload</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Tontonan</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="videos.data.length === 0">
              <td colspan="5" class="px-4 py-12 text-center text-slate-400">Tiada video ditemui</td>
            </tr>
            <tr
              v-for="(video, index) in videos.data"
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
                  class="h-[60px] w-[80px] object-cover rounded-lg"
                />
                <div
                  v-else
                  class="h-[60px] w-[80px] rounded-lg bg-slate-100 flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-slate-400">smart_display</span>
                </div>
              </td>

              <!-- Tajuk -->
              <td class="px-4 py-3">
                <a
                  v-if="video.youtube_url"
                  :href="video.youtube_url"
                  target="_blank"
                  class="font-medium text-slate-900 hover:text-primary transition-colors line-clamp-2 max-w-xs block"
                >
                  {{ video.title?.slice(0, 60) }}{{ (video.title?.length ?? 0) > 60 ? '...' : '' }}
                </a>
                <span v-else class="font-medium text-slate-900 line-clamp-2 max-w-xs block">
                  {{ video.title?.slice(0, 60) }}{{ (video.title?.length ?? 0) > 60 ? '...' : '' }}
                </span>
              </td>

              <!-- Tarikh -->
              <td class="px-4 py-3 text-slate-500 text-xs whitespace-nowrap">
                {{ formatDate(video.published_at) }}
              </td>

              <!-- Tontonan -->
              <td class="px-4 py-3 text-slate-600 text-sm">
                {{ formatViews(video.view_count) }}
              </td>

              <!-- Toggle -->
              <td class="px-4 py-3 text-right">
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
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
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-4 border-t border-slate-50">
        <Pagination :links="videos.links" />
      </div>
    </div>
  </div>
</template>
