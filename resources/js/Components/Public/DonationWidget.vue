<template>
  <div class="rounded-2xl bg-gradient-to-r from-background-dark via-[#1a3d24] to-background-dark p-8 md:p-12 text-white relative overflow-hidden">

    <!-- Dot grid texture -->
    <div class="absolute inset-0 pointer-events-none opacity-20" style="background-image: radial-gradient(circle, #13ec49 1px, transparent 1px); background-size: 24px 24px;"></div>

    <!-- Glow orb top-left -->
    <div class="absolute -top-16 -left-16 w-64 h-64 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(19,236,73,0.18) 0%, transparent 70%);"></div>

    <!-- Glow orb bottom-right -->
    <div class="absolute -bottom-20 -right-10 w-72 h-72 rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(19,236,73,0.12) 0%, transparent 70%);"></div>

    <!-- Decorative large icon -->
    <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
      <span class="material-symbols-outlined text-9xl">mintmark</span>
    </div>

    <div class="relative z-10 grid md:grid-cols-2 gap-12 items-center">
      <!-- Left: Info + CTA -->
      <div class="space-y-4">
        <p class="text-xs font-black uppercase tracking-widest text-primary">Tabung Derma</p>
        <h2 class="text-3xl md:text-4xl font-bold text-white leading-tight">
          {{ donation.name }}
        </h2>
        <p v-if="donation.description" class="text-slate-300 text-sm mt-2 leading-relaxed">
          {{ donation.description }}
        </p>
        <div class="pt-2">
          <a
            href="https://toyyibpay.com/Infaq-Keperluan-MJFK"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 font-bold text-background-dark shadow-xl shadow-primary/20 hover:scale-105 transition-transform"
          >
            <span class="material-symbols-outlined text-lg">volunteer_activism</span>
            Derma Sekarang
          </a>
        </div>
      </div>

      <!-- Right: Progress -->
      <div class="space-y-4">
        <div class="flex justify-between text-sm font-bold">
          <span>RM {{ formatAmount(donation.collected_amount) }} terkumpul</span>
          <span class="text-primary">Sasaran: RM {{ formatAmount(donation.target_amount) }}</span>
        </div>

        <!-- Progress bar -->
        <div class="h-4 w-full rounded-full bg-white/10 p-1">
          <div
            class="h-full rounded-full bg-primary transition-all duration-700"
            :style="{ width: progressPercent + '%' }"
          ></div>
        </div>

        <!-- Percentage text -->
        <p class="text-sm font-bold text-slate-300">
          <span class="text-primary text-xl font-black">{{ progressPercent }}%</span>
          tercapai
        </p>

        <!-- Amount large display -->
        <div class="mt-4 pt-4 border-t border-white/10">
          <p class="text-4xl font-black text-white">
            RM {{ formatAmount(donation.collected_amount) }}
          </p>
          <p class="text-slate-400 text-sm mt-1">
            daripada sasaran RM {{ formatAmount(donation.target_amount) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  donation: {
    type: Object,
    required: true,
  },
})

const progressPercent = computed(() => {
  if (!props.donation.target_amount || props.donation.target_amount <= 0) return 0
  const pct = (props.donation.collected_amount / props.donation.target_amount) * 100
  return Math.min(Math.round(pct * 10) / 10, 100)
})

function formatAmount(amount) {
  if (amount == null) return '0.00'
  return Number(amount).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>
