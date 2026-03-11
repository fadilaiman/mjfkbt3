<template>
  <div>
    <AppHead title="Hubungi Kami" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Page Header -->
      <div class="flex items-center gap-3 mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
          <span class="material-symbols-outlined text-2xl text-primary">call</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Hubungi Kami</h1>
      </div>

      <!-- WhatsApp Directory -->
      <section>
        <WhatsappDirectory :contacts="contacts" />
      </section>

      <!-- Mosque Info Card -->
      <section class="mt-12">
        <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">info</span>
          Maklumat Masjid
        </h2>
        <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
          <!-- Address -->
          <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
              <span class="material-symbols-outlined text-primary text-lg">location_on</span>
            </div>
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-0.5">Alamat</p>
              <p class="text-sm font-medium text-slate-700">
                {{ mosque?.address ?? 'Persiaran Jubli Perak Batu 3, Shah Alam, Petaling Jaya, Malaysia' }}
              </p>
            </div>
          </div>

          <!-- Phone -->
          <div v-if="mosque?.phone" class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
              <span class="material-symbols-outlined text-primary text-lg">phone</span>
            </div>
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-0.5">Telefon</p>
              <a :href="`tel:${mosque.phone}`" class="text-sm font-medium text-slate-700 hover:text-primary transition-colors">
                {{ mosque.phone }}
              </a>
            </div>
          </div>

          <!-- Email -->
          <div v-if="mosque?.email" class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
              <span class="material-symbols-outlined text-primary text-lg">mail</span>
            </div>
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-0.5">E-mel</p>
              <a :href="`mailto:${mosque.email}`" class="text-sm font-medium text-slate-700 hover:text-primary transition-colors">
                {{ mosque.email }}
              </a>
            </div>
          </div>

          <!-- Linktree / Website -->
          <div v-if="mosque?.linktree" class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10">
              <span class="material-symbols-outlined text-primary text-lg">link</span>
            </div>
            <div>
              <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-0.5">Linktree</p>
              <a
                :href="mosque.linktree"
                target="_blank"
                rel="noopener noreferrer"
                class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
              >
                {{ mosque.linktree }}
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- Google Maps -->
      <section class="mt-12">
        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">map</span>
          Lokasi Masjid
        </h2>
        <GoogleMapEmbed height="h-72 lg:h-96" />
      </section>

      <!-- Social Links -->
      <section class="mt-12">
        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">share</span>
          Media Sosial
        </h2>
        <SocialLinks size="lg" variant="dark" />
      </section>

    </div>
  </div>
</template>

<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import WhatsappDirectory from '@/Components/Public/WhatsappDirectory.vue'
import GoogleMapEmbed from '@/Components/Public/GoogleMapEmbed.vue'
import SocialLinks from '@/Components/Public/SocialLinks.vue'

defineOptions({ layout: PublicLayout })

defineProps({
  contacts: {
    type: Array,
    default: () => [],
  },
  mosque: {
    type: Object,
    default: null,
  },
})
</script>
