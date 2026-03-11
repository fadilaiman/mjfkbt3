<template>
  <div
    class="rounded-2xl bg-white border border-slate-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow cursor-pointer"
    @click="openWhatsApp"
  >
    <!-- Service icon -->
    <div
      class="h-14 w-14 shrink-0 rounded-full flex items-center justify-center"
      :class="service.iconBg"
    >
      <span class="material-symbols-outlined text-2xl" :class="service.iconColor">
        {{ service.icon }}
      </span>
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <p class="font-bold text-slate-900 leading-snug">{{ service.label }}</p>
      <p class="text-sm text-slate-500 mt-0.5">Hubungi {{ contact.name }}</p>
    </div>

    <!-- WhatsApp button -->
    <button
      class="shrink-0 bg-[#25D366] text-white rounded-full px-4 py-2 text-sm font-bold hover:bg-[#1ebe5a] transition-colors flex items-center gap-1.5"
      @click.stop="openWhatsApp"
      aria-label="Hubungi melalui WhatsApp"
    >
      <span class="material-symbols-outlined text-base">chat</span>
      <span class="hidden sm:inline">WhatsApp</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  contact: {
    type: Object,
    required: true,
  },
})

const serviceMap = {
  kewangan:   { label: 'Nak INFAQ?',                    icon: 'volunteer_activism', iconBg: 'bg-emerald-50', iconColor: 'text-emerald-500', message: 'Saya Nak Infaq' },
  am:         { label: 'Ada Soalan Berkaitan Masjid?',  icon: 'support_agent',      iconBg: 'bg-blue-50',    iconColor: 'text-blue-500',    message: null },
  pendidikan: { label: 'Nak Belajar Mengaji?',          icon: 'menu_book',          iconBg: 'bg-purple-50',  iconColor: 'text-purple-500',  message: null },
  kebajikan:  { label: 'Kebajikan & Bantuan',           icon: 'favorite',           iconBg: 'bg-rose-50',    iconColor: 'text-rose-500',    message: null },
}

const service = computed(() => {
  return serviceMap[props.contact.category] ?? {
    label: props.contact.role,
    icon: 'person',
    iconBg: 'bg-primary/10',
    iconColor: 'text-primary',
  }
})

const whatsappUrl = computed(() => {
  if (props.contact.wa_qr_id) {
    return `https://wa.me/qr/${props.contact.wa_qr_id}`
  }
  if (props.contact.wa_number) {
    const cleaned = props.contact.wa_number.replace(/\D/g, '')
    const msg = service.value.message ? `?text=${encodeURIComponent(service.value.message)}` : ''
    return `https://wa.me/${cleaned}${msg}`
  }
  return null
})

function openWhatsApp() {
  if (whatsappUrl.value) {
    window.open(whatsappUrl.value, '_blank', 'noopener,noreferrer')
  }
}
</script>
