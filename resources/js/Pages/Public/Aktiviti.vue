<template>
  <div>
    <AppHead title="Aktiviti & Acara" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Page Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
          <span class="material-symbols-outlined text-2xl text-primary">calendar_month</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Aktiviti &amp; Acara</h1>
      </div>

      <!-- Upcoming Events Label -->
      <h2 class="text-lg font-bold text-slate-700 mb-4">Acara Akan Datang</h2>

      <!-- Events List -->
      <div v-if="events.data && events.data.length > 0" class="space-y-4">
        <EventCard
          v-for="event in events.data"
          :key="event.id"
          :event="event"
        />
      </div>

      <!-- Empty State -->
      <EmptyState
        v-else
        icon="calendar_month"
        title="Tiada acara akan datang buat masa ini"
        description="Sila semak semula kemudian untuk maklumat acara terkini."
      />

      <!-- Pagination -->
      <Pagination v-if="events.links" :links="events.links" />

    </div>
  </div>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import EmptyState from '@/Components/Shared/EmptyState.vue'
import Pagination from '@/Components/Shared/Pagination.vue'
import EventCard from '@/Components/Public/EventCard.vue'

defineOptions({ layout: PublicLayout })

defineProps({
  events: {
    type: Object,
    required: true,
  },
  fbEvents: {
    type: Array,
    default: () => [],
  },
})
</script>
