<template>
  <div class="rounded-2xl border border-slate-100 bg-white p-6 h-full shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-bold text-lg text-slate-900">Waktu Solat</h3>
      <span class="text-xs font-semibold text-primary px-2 py-1 bg-primary/10 rounded-full">
        Shah Alam (SGR01)
      </span>
    </div>

    <!-- Hijri date -->
    <p v-if="hijriDate" class="text-xs text-slate-500 mb-5">{{ hijriDate }}</p>
    <div v-else class="h-4 mb-5"></div>

    <!-- Prayer rows -->
    <div v-if="prayerTimes" class="space-y-2">
      <div
        v-for="prayer in prayerList"
        :key="prayer.key"
        :class="[
          'flex items-center justify-between rounded-xl transition-all duration-300',
          isActivePrayer(prayer.key)
            ? 'p-4 bg-primary/20 border-2 border-primary ring-4 ring-primary/5 scale-[1.02]'
            : 'p-3 bg-slate-50'
        ]"
      >
        <!-- Active prayer -->
        <template v-if="isActivePrayer(prayer.key)">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">notifications_active</span>
            <div>
              <div class="text-sm font-bold text-slate-900">{{ prayer.label }}</div>
              <div v-if="activePrayer?.minutes_until > 0" class="text-[10px] uppercase font-bold text-slate-500">
                bermula dalam {{ activePrayer.minutes_until }}m
              </div>
              <div v-else class="text-[10px] uppercase font-bold text-primary">
                Sedang Berlangsung
              </div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-sm font-black text-primary">{{ prayerTimes[prayer.key] ?? '—' }}</div>
          </div>
        </template>

        <!-- Non-active prayer -->
        <template v-else>
          <span class="text-sm font-medium text-slate-700">{{ prayer.label }}</span>
          <span class="text-sm font-bold text-slate-900">{{ prayerTimes[prayer.key] ?? '—' }}</span>
        </template>
      </div>
    </div>

    <!-- Skeleton if no prayer times -->
    <div v-else class="space-y-2">
      <div v-for="n in 6" :key="n" class="h-12 rounded-xl bg-slate-100 animate-pulse"></div>
    </div>

    <!-- Footer -->
    <p class="text-xs text-slate-400 mt-5 text-center">Data dari JAKIM e-Solat</p>
  </div>
</template>

<script setup>
const props = defineProps({
  prayerTimes: {
    type: Object,
    default: null,
  },
  activePrayer: {
    type: Object,
    default: null,
  },
  hijriDate: {
    type: String,
    default: '',
  },
})

const prayerList = [
  { key: 'subuh', label: 'Subuh' },
  { key: 'syuruk', label: 'Syuruk' },
  { key: 'zohor', label: 'Zohor' },
  { key: 'asar', label: 'Asar' },
  { key: 'maghrib', label: 'Maghrib' },
  { key: 'isyak', label: 'Isyak' },
]

function isActivePrayer(key) {
  if (!props.activePrayer) return false
  return props.activePrayer.name === key
}
</script>
