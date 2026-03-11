<script setup>
import { ref, reactive } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import StatusBadge from '@/Components/Admin/StatusBadge.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  contacts: { type: Array, required: true },
})

const editingId = ref(null)
const editForms = reactive({})

function startEdit(contact) {
  editForms[contact.id] = {
    name: contact.name ?? '',
    role: contact.role ?? '',
    wa_number: contact.wa_number ?? '',
    wa_qr_id: contact.wa_qr_id ?? '',
    category: contact.category ?? 'am',
    is_active: contact.is_active ?? true,
    processing: false,
    errors: {},
  }
  editingId.value = contact.id
}

function cancelEdit() {
  editingId.value = null
}

function saveContact(contact) {
  const form = editForms[contact.id]
  if (!form) return

  form.processing = true
  form.errors = {}

  router.put(route('admin.whatsapp.update', contact.id), {
    name: form.name,
    role: form.role,
    wa_number: form.wa_number,
    wa_qr_id: form.wa_qr_id,
    category: form.category,
    is_active: form.is_active,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      form.processing = false
      editingId.value = null
    },
    onError: (errors) => {
      form.processing = false
      form.errors = errors
    },
  })
}

const categoryMap = {
  kewangan: { label: 'Kewangan', class: 'bg-amber-100 text-amber-700' },
  am: { label: 'Am', class: 'bg-slate-100 text-slate-600' },
  pendidikan: { label: 'Pendidikan', class: 'bg-blue-100 text-blue-700' },
  kebajikan: { label: 'Kebajikan', class: 'bg-emerald-100 text-emerald-700' },
}

function getCategoryInfo(cat) {
  return categoryMap[cat] ?? { label: cat, class: 'bg-slate-100 text-slate-600' }
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-black text-slate-900">Kenalan WhatsApp</h1>
      <p class="text-sm text-slate-500 mt-1">Urus semua kenalan WhatsApp masjid</p>
    </div>

    <!-- Empty State -->
    <div
      v-if="contacts.length === 0"
      class="bg-white rounded-2xl border border-slate-100 shadow-sm p-16 text-center"
    >
      <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">chat</span>
      <p class="text-slate-500 font-medium">Tiada kenalan WhatsApp ditemui</p>
    </div>

    <!-- Contact List -->
    <div v-else class="space-y-4">
      <div
        v-for="contact in contacts"
        :key="contact.id"
        class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
      >
        <!-- View Mode -->
        <div v-if="editingId !== contact.id" class="p-5 flex items-start justify-between gap-4">
          <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10">
              <span class="material-symbols-outlined text-xl text-primary">person</span>
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <p class="font-bold text-slate-900">{{ contact.name }}</p>
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="getCategoryInfo(contact.category).class"
                >
                  {{ getCategoryInfo(contact.category).label }}
                </span>
                <StatusBadge :active="contact.is_active" />
              </div>
              <p class="text-sm text-slate-500 mt-0.5">{{ contact.role }}</p>
              <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                <span v-if="contact.wa_number" class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">phone</span>
                  {{ contact.wa_number }}
                </span>
                <span v-if="contact.wa_qr_id" class="flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">qr_code</span>
                  {{ contact.wa_qr_id }}
                </span>
              </div>
            </div>
          </div>

          <button
            type="button"
            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors shrink-0"
            @click="startEdit(contact)"
          >
            <span class="material-symbols-outlined text-sm">edit</span>
            Edit
          </button>
        </div>

        <!-- Edit Mode -->
        <div v-else class="p-5 border-t-2 border-primary/20 bg-slate-50/50">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-bold text-slate-900">Edit Kenalan</p>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-700 transition-colors"
              @click="cancelEdit"
            >
              <span class="material-symbols-outlined text-xl">close</span>
            </button>
          </div>

          <div v-if="editForms[contact.id]" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nama -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Nama</label>
              <input
                v-model="editForms[contact.id].name"
                type="text"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-white"
              />
              <p v-if="editForms[contact.id].errors.name" class="text-red-500 text-xs mt-1">{{ editForms[contact.id].errors.name }}</p>
            </div>

            <!-- Peranan -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Peranan</label>
              <input
                v-model="editForms[contact.id].role"
                type="text"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-white"
              />
              <p v-if="editForms[contact.id].errors.role" class="text-red-500 text-xs mt-1">{{ editForms[contact.id].errors.role }}</p>
            </div>

            <!-- Nombor WhatsApp -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Nombor WhatsApp</label>
              <input
                v-model="editForms[contact.id].wa_number"
                type="text"
                placeholder="60123456789"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-white"
              />
              <p v-if="editForms[contact.id].errors.wa_number" class="text-red-500 text-xs mt-1">{{ editForms[contact.id].errors.wa_number }}</p>
            </div>

            <!-- WA QR ID -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">ID QR WhatsApp</label>
              <input
                v-model="editForms[contact.id].wa_qr_id"
                type="text"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-white"
              />
              <p v-if="editForms[contact.id].errors.wa_qr_id" class="text-red-500 text-xs mt-1">{{ editForms[contact.id].errors.wa_qr_id }}</p>
            </div>

            <!-- Kategori -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 mb-1">Kategori</label>
              <select
                v-model="editForms[contact.id].category"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors bg-white"
              >
                <option value="kewangan">Kewangan</option>
                <option value="am">Am</option>
                <option value="pendidikan">Pendidikan</option>
                <option value="kebajikan">Kebajikan</option>
              </select>
              <p v-if="editForms[contact.id].errors.category" class="text-red-500 text-xs mt-1">{{ editForms[contact.id].errors.category }}</p>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-end pb-2">
              <label class="flex items-center gap-3 cursor-pointer">
                <input
                  v-model="editForms[contact.id].is_active"
                  type="checkbox"
                  class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
                />
                <span class="text-sm font-medium text-slate-700">Aktif</span>
              </label>
            </div>
          </div>

          <!-- Save Actions -->
          <div class="flex items-center gap-3 mt-4">
            <button
              type="button"
              :disabled="editForms[contact.id]?.processing"
              class="inline-flex items-center gap-2 bg-primary text-background-dark font-bold px-4 py-2 rounded-xl text-sm hover:opacity-90 transition-opacity disabled:opacity-60"
              @click="saveContact(contact)"
            >
              <svg v-if="editForms[contact.id]?.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ editForms[contact.id]?.processing ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button
              type="button"
              class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition-colors"
              @click="cancelEdit"
            >
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
