<template>
  <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
    <div class="flex flex-col lg:flex-row">

      <!-- Address panel -->
      <div class="lg:w-72 shrink-0 p-6 flex flex-col gap-4 justify-center border-b lg:border-b-0 lg:border-r border-slate-100">
        <div class="flex items-center gap-2 text-primary">
          <span class="material-symbols-outlined text-2xl">mosque</span>
          <span class="font-bold text-slate-900 text-lg leading-tight">{{ name }}</span>
        </div>

        <div class="flex items-start gap-3 text-slate-600">
          <span class="material-symbols-outlined text-lg text-slate-400 mt-0.5 shrink-0">location_on</span>
          <p class="text-sm leading-relaxed">{{ address }}</p>
        </div>

        <div v-if="phone" class="flex items-center gap-3 text-slate-600">
          <span class="material-symbols-outlined text-lg text-slate-400 shrink-0">phone</span>
          <a :href="`tel:${phone}`" class="text-sm hover:text-primary transition-colors">{{ phone }}</a>
        </div>

        <a
          :href="mapsUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="mt-2 inline-flex items-center justify-center gap-2 bg-primary text-white font-bold text-sm px-4 py-2.5 rounded-xl hover:opacity-90 transition-opacity"
        >
          <span class="material-symbols-outlined text-base">directions</span>
          Dapatkan Arah
        </a>

        <a
          :href="mapsUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center justify-center gap-2 text-primary font-semibold text-sm hover:underline"
        >
          <span class="material-symbols-outlined text-base">open_in_new</span>
          Buka di Google Maps
        </a>
      </div>

      <!-- Interactive map -->
      <div class="flex-1" :class="height">
        <iframe
          :src="embedUrl"
          width="100%"
          height="100%"
          style="border: 0;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Lokasi Masjid"
        ></iframe>
      </div>

    </div>
  </div>
</template>

<script setup>
defineProps({
  height: {
    type: String,
    default: 'h-72 lg:h-auto',
  },
  name: {
    type: String,
    default: 'Masjid Jamek Fastabiqul Khayrat Batu 3',
  },
  address: {
    type: String,
    default: 'No. 1, Persiaran Jubli Perak, Batu Tiga, 40150 Shah Alam, Selangor',
  },
  phone: {
    type: String,
    default: '011-3734 7675',
  },
  mapsUrl: {
    type: String,
    default: 'https://maps.app.goo.gl/QMDfe4jAAAgUZEp37',
  },
  embedUrl: {
    type: String,
    default: 'https://maps.google.com/maps?q=3.059286,101.557224&z=17&output=embed',
  },
})
</script>
