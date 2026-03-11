<template>
  <div>
    <AppHead title="" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">

      <!-- 1. Hero Grid -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
          <YoutubeHero :liveStatus="liveStatus" :latestVideo="latestVideo" />
        </div>
        <div class="lg:col-span-1">
          <PrayerTimesWidget
            :prayerTimes="prayerTimes"
            :activePrayer="activePrayer"
            :hijriDate="prayerTimes?.hijri_date ?? ''"
          />
        </div>
      </section>

      <!-- 2. Quick Links -->
      <section>
        <QuickLinks />
      </section>

      <!-- 3. Donation Section -->
      <section v-if="firstActiveDonation">
        <DonationWidget :donation="firstActiveDonation" />
      </section>

      <!-- 4. Kuliah Section -->
      <section class="bg-white rounded-2xl p-6 md:p-8">
        <VideoCarousel :videos="latestVideos" />
      </section>

      <!-- 5. TikTok Section -->
      <section>
        <TikTokStrip :videos="tiktokVideos" />
      </section>

      <!-- 6. Info Terkini (Facebook Posts) -->
      <section>
        <FacebookFeed :posts="fbPosts" :pageUrl="facebookPageUrl" />
      </section>

      <!-- 7. WhatsApp Directory Section -->
      <section>
        <WhatsappDirectory :contacts="whatsappContacts" />
      </section>

      <!-- 8. Google Maps Section -->
      <section>
        <GoogleMapEmbed height="h-72 lg:h-80" />
      </section>

    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import AppHead from '@/Components/Shared/AppHead.vue'
import YoutubeHero from '@/Components/Public/YoutubeHero.vue'
import PrayerTimesWidget from '@/Components/Public/PrayerTimesWidget.vue'
import QuickLinks from '@/Components/Public/QuickLinks.vue'
import DonationWidget from '@/Components/Public/DonationWidget.vue'
import VideoCarousel from '@/Components/Public/VideoCarousel.vue'
import TikTokStrip from '@/Components/Public/TikTokStrip.vue'
import FacebookFeed from '@/Components/Public/FacebookFeed.vue'
import WhatsappDirectory from '@/Components/Public/WhatsappDirectory.vue'
import GoogleMapEmbed from '@/Components/Public/GoogleMapEmbed.vue'

defineOptions({ layout: PublicLayout })

const props = defineProps({
  liveStatus: {
    type: Object,
    default: null,
  },
  latestVideo: {
    type: Object,
    default: null,
  },
  prayerTimes: {
    type: Object,
    default: null,
  },
  activePrayer: {
    type: Object,
    default: null,
  },
  donations: {
    type: Array,
    default: () => [],
  },
  latestVideos: {
    type: Array,
    default: () => [],
  },
  tiktokVideos: {
    type: Array,
    default: () => [],
  },
  fbPosts: {
    type: Array,
    default: () => [],
  },
  whatsappContacts: {
    type: Array,
    default: () => [],
  },
})

const firstActiveDonation = computed(() => {
  return props.donations.length > 0 ? props.donations[0] : null
})

const facebookPageUrl = 'https://www.facebook.com/mjfkbt3'
</script>
