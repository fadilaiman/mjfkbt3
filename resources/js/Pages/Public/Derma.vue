<template>
  <div>
    <AppHead title="Derma & Sumbangan" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Page Header -->
      <div class="flex items-center gap-3 mb-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
          <span class="material-symbols-outlined text-2xl text-primary">volunteer_activism</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Derma &amp; Sumbangan</h1>
      </div>

      <!-- Intro Text -->
      <p class="text-slate-600 mb-8 max-w-2xl">
        Sokongan anda amat bererti untuk pembangunan dan program masjid kami.
      </p>

      <!-- Active Donation Widgets -->
      <div v-if="activeDonations.length > 0" class="space-y-8">
        <DonationWidget
          v-for="donation in activeDonations"
          :key="donation.id"
          :donation="donation"
        />
      </div>
      <EmptyState
        v-else
        icon="volunteer_activism"
        title="Tiada tabung derma aktif buat masa ini"
        description="Sila hubungi pihak masjid untuk maklumat lanjut."
      />

      <!-- Divider -->
      <div class="my-12 border-t border-slate-200"></div>

      <!-- Cara Derma Section -->
      <section class="mb-10">
        <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary">info</span>
          Cara Derma
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Step 1 -->
          <div class="rounded-xl bg-white border border-slate-100 shadow-sm p-6 flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-background-dark font-black text-lg">
              1
            </div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Hubungi Bendahari melalui WhatsApp</h3>
              <p class="text-sm text-slate-500">
                Hubungi Bendahari masjid terus melalui WhatsApp untuk membuat sumbangan dan dapatkan pengesahan.
              </p>
            </div>
          </div>
          <!-- Step 2 -->
          <div class="rounded-xl bg-white border border-slate-100 shadow-sm p-6 flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-background-dark font-black text-lg">
              2
            </div>
            <div>
              <h3 class="font-bold text-slate-900 mb-1">Derma terus ke akaun masjid</h3>
              <p class="text-sm text-slate-500">
                Pindahan terus ke akaun bank masjid. Admin akan kemaskini maklumat akaun dari semasa ke semasa.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- WhatsApp Kewangan Contacts -->
      <section v-if="kewanganContacts.length > 0">
        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-[#25D366]">chat</span>
          Hubungi Bendahari
        </h2>
        <p class="text-sm text-slate-500 mb-6">Klik untuk hubungi terus melalui WhatsApp</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <WhatsappCard
            v-for="contact in kewanganContacts"
            :key="contact.id"
            :contact="contact"
          />
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import EmptyState from '@/Components/Shared/EmptyState.vue'
import DonationWidget from '@/Components/Public/DonationWidget.vue'
import WhatsappCard from '@/Components/Public/WhatsappCard.vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  donations: {
    type: Array,
    default: () => [],
  },
  whatsappContacts: {
    type: Array,
    default: () => [],
  },
})

const activeDonations = computed(() => {
  return props.donations.filter(d => d.is_active)
})

const kewanganContacts = computed(() => {
  return props.whatsappContacts.filter(c => c.category === 'kewangan')
})
</script>
