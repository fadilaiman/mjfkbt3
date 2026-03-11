<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Sidebar (desktop fixed, mobile overlay) -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen w-64 bg-white border-r border-slate-100 shadow-sm flex flex-col z-40 transition-transform duration-300',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Sidebar Header -->
      <div class="flex items-center gap-3 p-6 border-b border-slate-100">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-background-dark">
          <span class="material-symbols-outlined text-xl font-bold">mosque</span>
        </div>
        <div class="min-w-0">
          <p class="font-bold text-sm text-slate-900 truncate">Panel Admin</p>
          <p class="text-[10px] text-slate-400 truncate">MJFKBT3</p>
        </div>
      </div>

      <!-- Nav Items -->
      <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        <Link
          v-for="item in navItems"
          :key="item.route"
          :href="item.href"
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-l-xl text-sm font-medium transition-all',
            isActive(item.href)
              ? 'bg-primary/10 text-primary border-r-2 border-primary font-bold'
              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
          ]"
        >
          <span class="material-symbols-outlined text-xl">{{ item.icon }}</span>
          {{ item.label }}
        </Link>
      </nav>

      <!-- Sidebar Footer: Logout -->
      <div class="p-3 border-t border-slate-100">
        <form @submit.prevent="handleLogout">
          <button
            type="submit"
            class="flex w-full items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition-colors"
          >
            <span class="material-symbols-outlined text-xl">logout</span>
            Log Keluar
          </button>
        </form>
      </div>
    </aside>

    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-black/50 md:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Main Area -->
    <div class="md:ml-64 flex flex-col min-h-screen">
      <!-- Topbar -->
      <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 sticky top-0 z-20">
        <div class="flex items-center gap-4">
          <!-- Hamburger (mobile) -->
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors"
            aria-label="Toggle menu"
          >
            <span class="material-symbols-outlined">menu</span>
          </button>
          <!-- Page title slot -->
          <div class="text-sm font-bold text-slate-900">
            <slot name="title">Dashboard</slot>
          </div>
        </div>

        <!-- Right: Admin info -->
        <div class="flex items-center gap-3">
          <span class="text-sm font-medium text-slate-600 hidden sm:block">
            {{ adminName }}
          </span>
          <div class="h-9 w-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
            {{ adminInitial }}
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6 bg-slate-50 flex-1">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const sidebarOpen = ref(false)

const adminName = computed(() => page.props.auth?.user?.name ?? 'Admin')
const adminInitial = computed(() => adminName.value.charAt(0).toUpperCase())

const navItems = [
  { label: 'Dashboard', icon: 'dashboard', href: '/admin/dashboard' },
  { label: 'Pengumuman', icon: 'campaign', href: '/admin/pengumuman' },
  { label: 'Aktiviti', icon: 'event', href: '/admin/aktiviti' },
  { label: 'Tabung Derma', icon: 'volunteer_activism', href: '/admin/derma' },
  { label: 'Kandungan Auto', icon: 'sync', href: '/admin/kandungan/youtube' },
  { label: 'Muat Naik Fail', icon: 'upload_file', href: '/admin/fail' },
  { label: 'WhatsApp', icon: 'chat', href: '/admin/whatsapp' },
  { label: 'Log Aktiviti', icon: 'history', href: '/admin/log' },
  { label: 'Tetapan', icon: 'settings', href: '/admin/tetapan' },
]

function isActive(href) {
  return page.url.startsWith(href)
}

function handleLogout() {
  router.post(route('admin.logout'))
}
</script>
