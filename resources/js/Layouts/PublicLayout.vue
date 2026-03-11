<template>
  <div class="min-h-screen bg-background-light text-slate-900">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 w-full border-b border-slate-100 bg-white/80 backdrop-blur-md">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">
          <!-- Left: Logo + Name -->
          <Link href="/" class="flex items-center gap-3 min-w-0">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary text-background-dark">
              <span class="material-symbols-outlined text-2xl font-bold">mosque</span>
            </div>
            <span class="hidden sm:block text-xl font-bold tracking-tight text-slate-900 truncate max-w-xs lg:max-w-sm">
              Masjid Jamek Fastabiqul Khayrat Batu 3
            </span>
          </Link>

          <!-- Center: Nav links (desktop only) -->
          <nav class="hidden md:flex items-center gap-8">
            <Link
              href="/"
              class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
              :class="{ 'text-primary': $page.url === '/' }"
            >
              Utama
            </Link>
            <a
              href="/#hero"
              class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
            >
              Siaran Langsung
            </a>
            <Link
              href="/kuliah"
              class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
              :class="{ 'text-primary': $page.url.startsWith('/kuliah') }"
            >
              Kuliah Lepas
            </Link>
            <Link
              href="/aktiviti"
              class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
              :class="{ 'text-primary': $page.url.startsWith('/aktiviti') }"
            >
              Aktiviti
            </Link>
            <Link
              href="/pengumuman"
              class="text-sm font-medium text-slate-700 hover:text-primary transition-colors"
              :class="{ 'text-primary': $page.url.startsWith('/pengumuman') }"
            >
              Pengumuman
            </Link>
          </nav>

          <!-- Right: Derma button + hamburger -->
          <div class="flex items-center gap-3">
            <Link
              href="/derma"
              class="hidden sm:flex items-center gap-2 rounded-full bg-primary px-5 py-2 text-sm font-bold text-background-dark hover:bg-primary/90 transition-all shadow-lg shadow-primary/20"
            >
              <span class="material-symbols-outlined text-sm">favorite</span>
              Derma
            </Link>
            <button
              @click="mobileNavOpen = true"
              class="md:hidden p-2 text-slate-600 rounded-lg hover:bg-slate-100 transition-colors"
              aria-label="Buka menu"
            >
              <span class="material-symbols-outlined">menu</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile Nav Drawer -->
    <MobileNav :isOpen="mobileNavOpen" :onClose="() => mobileNavOpen = false" />

    <!-- Main Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-200 bg-white pt-16 pb-8">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
          <!-- Column 1: Logo + Tagline + Social -->
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-background-dark">
                <span class="material-symbols-outlined text-xl font-bold">mosque</span>
              </div>
              <span class="text-lg font-bold tracking-tight text-slate-900">MJFKBT3</span>
            </div>
            <p class="text-sm font-bold text-slate-500">Satu Tempat, Semua Maklumat</p>
            <p class="text-xs text-slate-400 leading-relaxed">
              Berkhidmat demi komuniti dengan kecemerlangan dalam iman, ilmu, dan pembangunan masyarakat.
            </p>
            <div class="flex gap-4">
              <a
                href="https://youtube.com/@masjidjamekfastabiqulkhairat"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors"
                aria-label="YouTube"
              >
                <span class="material-symbols-outlined">youtube_activity</span>
              </a>
              <a
                href="https://www.tiktok.com/@mjfk.batu03"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors"
                aria-label="TikTok"
              >
                <span class="material-symbols-outlined">video_library</span>
              </a>
              <a
                href="https://www.facebook.com/mjfkbt3/"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors"
                aria-label="Facebook"
              >
                <span class="material-symbols-outlined">thumb_up</span>
              </a>
              <a
                href="https://www.instagram.com/mjfk.bt3"
                target="_blank"
                rel="noopener noreferrer"
                class="text-slate-400 hover:text-primary transition-colors"
                aria-label="Instagram"
              >
                <span class="material-symbols-outlined">photo_camera</span>
              </a>
            </div>
          </div>

          <!-- Column 2: Pautan Pantas -->
          <div class="space-y-4">
            <h4 class="font-bold text-sm text-slate-900">Pautan Pantas</h4>
            <ul class="space-y-2 text-xs text-slate-500">
              <li>
                <a href="/#hero" class="hover:text-primary transition-colors">Siaran Langsung</a>
              </li>
              <li>
                <Link href="/kuliah" class="hover:text-primary transition-colors">Kuliah Lepas</Link>
              </li>
              <li>
                <Link href="/aktiviti" class="hover:text-primary transition-colors">Aktiviti</Link>
              </li>
              <li>
                <Link href="/pengumuman" class="hover:text-primary transition-colors">Pengumuman</Link>
              </li>
              <li>
                <Link href="/derma" class="hover:text-primary transition-colors">Derma</Link>
              </li>
            </ul>
          </div>

          <!-- Column 3: Hubungi Kami -->
          <div class="space-y-4">
            <h4 class="font-bold text-sm text-slate-900">Hubungi Kami</h4>
            <div class="space-y-3 text-xs text-slate-500">
              <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">location_on</span>
                <span>Persiaran Jubli Perak Batu 3, Shah Alam, Petaling Jaya, Malaysia</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] shrink-0">phone</span>
                <a href="tel:+60113734 7675" class="hover:text-primary transition-colors">011-3734 7675</a>
              </div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] shrink-0">mail</span>
                <a href="mailto:mjfkbt3@gmail.com" class="hover:text-primary transition-colors">mjfkbt3@gmail.com</a>
              </div>
              <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px] shrink-0">link</span>
                <a
                  href="https://linktr.ee/mjfkbt3"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="hover:text-primary transition-colors"
                >
                  linktr.ee/mjfkbt3
                </a>
              </div>
            </div>
          </div>

          <!-- Column 4: Google Maps -->
          <div>
            <h4 class="font-bold text-sm text-slate-900 mb-4">Lokasi Kami</h4>
            <div class="h-40 rounded-xl overflow-hidden shadow-sm">
              <iframe
                src="https://maps.google.com/maps?q=3.059286,101.557224&z=17&output=embed"
                width="100%"
                height="100%"
                style="border: 0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Lokasi Masjid"
              ></iframe>
            </div>
          </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">
            © 2026 Masjid Jamek Fastabiqul Khayrat Batu 3. Hak Cipta Terpelihara.
          </p>
          <div class="flex items-center gap-6">
            <Link href="/terma" class="text-[10px] font-bold text-slate-400 hover:text-primary transition-colors">
              Terma Penggunaan
            </Link>
            <Link href="/privasi" class="text-[10px] font-bold text-slate-400 hover:text-primary transition-colors">
              Dasar Privasi
            </Link>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import MobileNav from '@/Components/Public/MobileNav.vue'

const mobileNavOpen = ref(false)
</script>
