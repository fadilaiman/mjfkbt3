<template>
  <section class="space-y-6">
    <div class="flex items-center gap-2">
      <h2 class="text-2xl font-bold flex items-center gap-2 text-slate-900">
        <span class="material-symbols-outlined text-primary">handshake</span>
        Perkhidmatan Kami
      </h2>
    </div>

    <p class="text-sm text-slate-500">
      Klik untuk hubungi terus melalui WhatsApp
    </p>

    <div v-if="contacts && contacts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <WhatsappCard
        v-for="contact in contacts"
        :key="contact.id || contact.wa_number || contact.wa_qr_id"
        :contact="contact"
      />
    </div>

    <div v-else class="flex flex-col items-center justify-center py-12 text-center bg-white rounded-2xl border border-slate-100">
      <span class="material-symbols-outlined text-4xl text-slate-300 mb-3">handshake</span>
      <p class="text-sm font-medium text-slate-500">Tiada maklumat perkhidmatan tersedia</p>
    </div>
  </section>
</template>

<script setup>
import WhatsappCard from '@/Components/Public/WhatsappCard.vue'

defineProps({
  contacts: {
    type: Array,
    default: () => [],
  },
})
</script>
