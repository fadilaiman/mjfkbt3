<script setup>
defineProps({
  show: { type: Boolean, required: true },
  title: { type: String, default: 'Sahkan Tindakan' },
  message: { type: String, default: '' },
  confirmText: { type: String, default: 'Padam' },
  confirmClass: { type: String, default: 'bg-red-600 text-white hover:bg-red-700' },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click.self="emit('cancel')"
      >
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50" @click="emit('cancel')" />

        <!-- Modal Card -->
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
          <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
              <span class="material-symbols-outlined text-xl text-red-600">warning</span>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-bold text-slate-900">{{ title }}</h3>
              <p class="text-sm text-slate-500 mt-1">{{ message }}</p>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button
              type="button"
              class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors"
              @click="emit('cancel')"
            >
              Batal
            </button>
            <button
              type="button"
              :class="['px-4 py-2 rounded-xl text-sm font-bold transition-colors', confirmClass]"
              @click="emit('confirm')"
            >
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
