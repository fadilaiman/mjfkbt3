<template>
  <Teleport to="body">
    <Transition name="mobile-nav">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex">
        <!-- Dark overlay -->
        <div
          class="absolute inset-0 bg-black/50"
          @click="onClose"
          aria-hidden="true"
        ></div>

        <!-- Slide-in panel from right -->
        <div class="absolute right-0 top-0 h-full w-80 bg-white flex flex-col shadow-2xl">
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-background-dark">
                <span class="material-symbols-outlined text-xl">mosque</span>
              </div>
              <div>
                <p class="font-bold text-sm text-slate-900 leading-tight">Masjid Jamek</p>
                <p class="text-[10px] text-slate-400">Fastabiqul Khayrat Batu 3</p>
              </div>
            </div>
            <button
              @click="onClose"
              class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors"
              aria-label="Tutup menu"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <!-- Nav links -->
          <nav class="flex-1 overflow-y-auto p-6 space-y-1">
            <Link
              href="/"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-xl text-primary">home</span>
              Utama
            </Link>
            <a
              href="/#hero"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-xl text-primary">videocam</span>
              Siaran Langsung
            </a>
            <Link
              href="/kuliah"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-xl text-primary">history</span>
              Kuliah Lepas
            </Link>
            <Link
              href="/aktiviti"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-xl text-primary">calendar_month</span>
              Aktiviti
            </Link>
            <Link
              href="/pengumuman"
              class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-primary transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-xl text-primary">campaign</span>
              Pengumuman
            </Link>

            <!-- Divider -->
            <div class="border-t border-slate-100 my-4"></div>

            <!-- Derma CTA -->
            <Link
              href="/derma"
              class="flex items-center justify-center gap-2 w-full px-6 py-3 rounded-xl bg-primary text-background-dark font-bold text-sm hover:bg-primary/90 transition-colors"
              @click="onClose"
            >
              <span class="material-symbols-outlined text-sm">favorite</span>
              Derma Sekarang
            </Link>
          </nav>

          <!-- Social links at bottom -->
          <div class="p-6 border-t border-slate-100">
            <p class="text-xs text-slate-400 mb-3 font-medium">Ikuti Kami</p>
            <div class="flex items-center gap-3">
              <a
                href="https://youtube.com/@masjidjamekfastabiqulkhairat"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-50 rounded-lg"
                aria-label="YouTube"
              >
                <span class="material-symbols-outlined text-xl">youtube_activity</span>
              </a>
              <a
                href="https://www.tiktok.com/@mjfk.batu03"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-50 rounded-lg"
                aria-label="TikTok"
              >
                <span class="material-symbols-outlined text-xl">video_library</span>
              </a>
              <a
                href="https://www.facebook.com/mjfkbt3/"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-50 rounded-lg"
                aria-label="Facebook"
              >
                <span class="material-symbols-outlined text-xl">thumb_up</span>
              </a>
              <a
                href="https://www.instagram.com/mjfk.bt3"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-50 rounded-lg"
                aria-label="Instagram"
              >
                <span class="material-symbols-outlined text-xl">photo_camera</span>
              </a>
              <a
                href="https://linktr.ee/mjfkbt3"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors p-2 hover:bg-slate-50 rounded-lg"
                aria-label="Linktree"
              >
                <span class="material-symbols-outlined text-xl">link</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  onClose: {
    type: Function,
    required: true,
  },
})
</script>

<style>
.mobile-nav-enter-active,
.mobile-nav-leave-active {
  transition: opacity 0.25s ease;
}
.mobile-nav-enter-from,
.mobile-nav-leave-to {
  opacity: 0;
}
.mobile-nav-panel-enter-active,
.mobile-nav-panel-leave-active {
  transition: transform 0.25s ease;
}
.mobile-nav-panel-enter-from,
.mobile-nav-panel-leave-to {
  transform: translateX(100%);
}
</style>
