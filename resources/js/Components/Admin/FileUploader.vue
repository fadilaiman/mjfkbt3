<script setup>
import { ref } from 'vue'

const props = defineProps({
  accept: { type: String, default: 'image/*,.pdf' },
  maxSizeMb: { type: Number, default: 10 },
  modelValue: { type: [File, null], default: null },
})

const emit = defineEmits(['update:modelValue'])

const dragging = ref(false)
const error = ref('')
const fileInput = ref(null)

function formatSize(bytes) {
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}

function validateAndSet(file) {
  error.value = ''
  if (!file) return

  const maxBytes = props.maxSizeMb * 1024 * 1024
  if (file.size > maxBytes) {
    error.value = `Fail terlalu besar. Had maksimum ialah ${props.maxSizeMb} MB.`
    return
  }

  emit('update:modelValue', file)
}

function onDrop(e) {
  dragging.value = false
  const file = e.dataTransfer?.files?.[0]
  if (file) validateAndSet(file)
}

function onFileChange(e) {
  const file = e.target.files?.[0]
  if (file) validateAndSet(file)
}

function removeFile() {
  emit('update:modelValue', null)
  error.value = ''
  if (fileInput.value) fileInput.value.value = ''
}

function triggerInput() {
  fileInput.value?.click()
}
</script>

<template>
  <div>
    <div
      v-if="!modelValue"
      class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors"
      :class="dragging ? 'border-primary bg-primary/5' : 'border-slate-300 hover:border-primary/50 bg-white'"
      @click="triggerInput"
      @dragover.prevent="dragging = true"
      @dragleave="dragging = false"
      @drop.prevent="onDrop"
    >
      <span class="material-symbols-outlined text-4xl text-slate-400 mb-3 block">upload_file</span>
      <p class="text-sm font-medium text-slate-600">Seret fail di sini atau klik untuk pilih</p>
      <p class="text-xs text-slate-400 mt-1">{{ accept }} — maksimum {{ maxSizeMb }} MB</p>
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        class="hidden"
        @change="onFileChange"
      />
    </div>

    <div
      v-else
      class="border-2 border-primary/40 rounded-xl p-5 bg-primary/5 flex items-center gap-4"
    >
      <span class="material-symbols-outlined text-3xl text-primary">insert_drive_file</span>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-slate-800 truncate">{{ modelValue.name }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ formatSize(modelValue.size) }}</p>
      </div>
      <button
        type="button"
        class="flex items-center justify-center h-8 w-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors"
        @click="removeFile"
      >
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>

    <p v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</p>
  </div>
</template>
