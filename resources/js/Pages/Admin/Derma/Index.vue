<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import StatusBadge from '@/Components/Admin/StatusBadge.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  donations: { type: Array, required: true },
})

function progressPercent(donation) {
  if (!donation.target_amount || donation.target_amount <= 0) return 0
  return Math.min(100, Math.round((donation.collected_amount / donation.target_amount) * 100))
}

function formatAmount(amount) {
  return new Intl.NumberFormat('ms-MY', { style: 'currency', currency: 'MYR' }).format(amount ?? 0)
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Tabung Derma</h1>
      <p class="text-sm text-slate-500 mt-1">Urus semua tabung derma masjid</p>
    </div>

    <!-- Empty State -->
    <div
      v-if="donations.length === 0"
      class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center"
    >
      <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">volunteer_activism</span>
      <p class="text-slate-500 font-medium">Tiada tabung derma ditemui</p>
    </div>

    <!-- Donation Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <div
        v-for="donation in donations"
        :key="donation.id"
        class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col gap-4"
      >
        <!-- Name & Status -->
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h3 class="font-bold text-slate-900 text-base truncate">{{ donation.name }}</h3>
            <p v-if="donation.description" class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ donation.description }}</p>
          </div>
          <StatusBadge :active="donation.is_active" />
        </div>

        <!-- Progress Bar -->
        <div>
          <div class="flex items-center justify-between text-xs text-slate-500 mb-1.5">
            <span>Terkumpul</span>
            <span class="font-semibold text-primary">{{ progressPercent(donation) }}%</span>
          </div>
          <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
            <div
              class="h-full bg-primary rounded-full transition-all duration-500"
              :style="{ width: `${progressPercent(donation)}%` }"
            />
          </div>
          <div class="flex items-center justify-between text-xs mt-2">
            <span class="font-bold text-slate-900">{{ formatAmount(donation.collected_amount) }}</span>
            <span class="text-slate-400">daripada {{ formatAmount(donation.target_amount) }}</span>
          </div>
        </div>

        <!-- Contributors -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
          <span class="material-symbols-outlined text-sm text-slate-400">group</span>
          <span>{{ donation.contributor_count ?? 0 }} penyumbang</span>
        </div>

        <!-- Edit Button -->
        <Link
          :href="route('admin.derma.edit', donation.id)"
          class="w-full text-center text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors mt-auto"
        >
          <span class="inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">edit</span>
            Edit Tabung
          </span>
        </Link>
      </div>
    </div>
  </div>
</template>
