<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold flex items-center gap-2 text-slate-900">
        <span class="material-symbols-outlined text-primary">public</span>
        Info Terkini
      </h2>
      <a
        :href="pageUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="text-sm font-bold text-primary flex items-center gap-1 hover:underline"
      >
        Lihat Semua
        <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </a>
    </div>

    <div v-if="posts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 flex flex-col"
      >
        <div class="flex-1 p-1">
          <div v-html="post.embed_html" class="[&>iframe]:!w-full [&>iframe]:!min-h-[400px]" />
        </div>
        <div class="px-4 pb-4 pt-2 flex items-center justify-between border-t border-slate-100">
          <span class="text-xs text-slate-400">
            {{ formatDate(post.published_at) }}
          </span>
          <a
            :href="post.permalink_url"
            target="_blank"
            rel="noopener noreferrer"
            class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
          >
            Buka
            <span class="material-symbols-outlined" style="font-size:14px">open_in_new</span>
          </a>
        </div>
      </div>
    </div>

    <EmptyState
      v-else
      icon="public"
      title="Tiada info terkini buat masa ini"
      description="Sila semak semula kemudian untuk berita terkini."
    />
  </div>
</template>

<script setup>
import EmptyState from '@/Components/Shared/EmptyState.vue'

const props = defineProps({
  posts: {
    type: Array,
    default: () => [],
  },
  pageUrl: {
    type: String,
    default: 'https://www.facebook.com/mjfkbt3',
  },
})

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('ms-MY', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
