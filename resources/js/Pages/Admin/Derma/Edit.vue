<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  donation: { type: Object, required: true },
})

const form = useForm({
  name: props.donation.name ?? '',
  description: props.donation.description ?? '',
  target_amount: props.donation.target_amount ?? '',
  collected_amount: props.donation.collected_amount ?? '',
  contributor_count: props.donation.contributor_count ?? 0,
  wa_number: props.donation.wa_number ?? '',
  is_active: props.donation.is_active ?? true,
})

function submit() {
  form.put(route('admin.derma.update', props.donation.id))
}
</script>

<template>
  <div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
      <Link
        :href="route('admin.derma.index')"
        class="flex items-center justify-center h-9 w-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
      >
        <span class="material-symbols-outlined text-xl">arrow_back</span>
      </Link>
      <div>
        <h1 class="text-2xl font-black text-slate-900">Edit Tabung Derma</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kemaskini maklumat tabung derma</p>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Fields -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
            <!-- Nama Tabung -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Tabung <span class="text-red-500">*</span></label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
              />
              <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Penerangan -->
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-1.5">Penerangan</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors resize-none"
              />
              <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
            </div>

            <!-- Amounts -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Sasaran -->
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sasaran (RM)</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">RM</span>
                  <input
                    v-model="form.target_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                  />
                </div>
                <p v-if="form.errors.target_amount" class="text-red-500 text-sm mt-1">{{ form.errors.target_amount }}</p>
              </div>

              <!-- Terkumpul -->
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Terkumpul (RM)</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">RM</span>
                  <input
                    v-model="form.collected_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                  />
                </div>
                <p v-if="form.errors.collected_amount" class="text-red-500 text-sm mt-1">{{ form.errors.collected_amount }}</p>
              </div>
            </div>

            <!-- Bil. Penyumbang & WhatsApp -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Bil. Penyumbang</label>
                <input
                  v-model="form.contributor_count"
                  type="number"
                  min="0"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                />
                <p v-if="form.errors.contributor_count" class="text-red-500 text-sm mt-1">{{ form.errors.contributor_count }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nombor WhatsApp (pilihan)</label>
                <input
                  v-model="form.wa_number"
                  type="text"
                  placeholder="60123456789"
                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
                />
                <p v-if="form.errors.wa_number" class="text-red-500 text-sm mt-1">{{ form.errors.wa_number }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900">Tetapan</h3>

            <!-- Status Aktif -->
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary"
              />
              <span class="text-sm font-medium text-slate-700">Status Aktif</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex flex-col gap-3">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-primary text-background-dark font-bold px-4 py-2.5 rounded-xl text-sm hover:opacity-90 transition-opacity disabled:opacity-60 flex items-center justify-center gap-2"
            >
              <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              {{ form.processing ? 'Menyimpan...' : 'Kemaskini Tabung' }}
            </button>
            <Link
              :href="route('admin.derma.index')"
              class="w-full text-center text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 px-4 py-2.5 rounded-xl transition-colors"
            >
              Batal
            </Link>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
