<template>
  <div>
    <AppHead title="Pengumuman" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Page Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
          <span class="material-symbols-outlined text-2xl text-primary">campaign</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Pengumuman</h1>
      </div>

      <div v-if="announcements.data && announcements.data.length > 0">
        <!-- Pinned / Utama Section -->
        <div v-if="pinnedAnnouncements.length > 0" class="mb-8">
          <h2 class="text-sm font-black uppercase tracking-wider text-slate-500 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">push_pin</span>
            Utama
          </h2>
          <div class="space-y-4">
            <AnnouncementCard
              v-for="announcement in pinnedAnnouncements"
              :key="announcement.id"
              :announcement="announcement"
            />
          </div>
        </div>

        <!-- Rest of Announcements -->
        <div v-if="unpinnedAnnouncements.length > 0">
          <h2 v-if="pinnedAnnouncements.length > 0" class="text-sm font-black uppercase tracking-wider text-slate-500 mb-4">
            Semua Pengumuman
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <AnnouncementCard
              v-for="announcement in unpinnedAnnouncements"
              :key="announcement.id"
              :announcement="announcement"
            />
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else
        icon="campaign"
        title="Tiada pengumuman buat masa ini"
        description="Sila semak semula kemudian untuk pengumuman terkini."
      />

      <!-- Pagination -->
      <Pagination v-if="announcements.links" :links="announcements.links" />

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import EmptyState from '@/Components/Shared/EmptyState.vue'
import Pagination from '@/Components/Shared/Pagination.vue'
import AnnouncementCard from '@/Components/Public/AnnouncementCard.vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  announcements: {
    type: Object,
    required: true,
  },
})

const pinnedAnnouncements = computed(() => {
  return (props.announcements.data ?? []).filter(a => a.is_pinned)
})

const unpinnedAnnouncements = computed(() => {
  return (props.announcements.data ?? []).filter(a => !a.is_pinned)
})
</script>
