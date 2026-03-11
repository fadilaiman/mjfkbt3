<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
})

function submit() {
  form.post(route('admin.login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <div class="min-h-screen bg-background-light flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-sm">
      <!-- Header -->
      <div class="flex flex-col items-center mb-8">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-background-dark mb-4">
          <span class="material-symbols-outlined text-3xl font-bold">mosque</span>
        </div>
        <h1 class="text-xl font-black text-slate-900 text-center">Panel Pentadbir MJFKBT3</h1>
        <p class="text-sm text-slate-500 mt-1">Log masuk untuk meneruskan</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-5">
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
            Emel
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
            placeholder="admin@contoh.com"
          />
          <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
            Kata Laluan
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-colors"
            placeholder="••••••••"
          />
          <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full bg-primary text-background-dark font-bold py-2.5 px-4 rounded-xl text-sm transition-opacity hover:opacity-90 disabled:opacity-60 flex items-center justify-center gap-2"
        >
          <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
          </svg>
          {{ form.processing ? 'Sedang Log Masuk...' : 'Log Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>
