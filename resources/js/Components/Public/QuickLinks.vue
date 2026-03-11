<template>
  <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
    <component
      :is="link.tag"
      v-for="link in links"
      :key="link.label"
      v-bind="link.attrs"
      :class="[
        'rounded-xl bg-white border border-slate-100 p-4 md:p-6 flex flex-col items-center gap-2',
        'hover:border-primary/50 hover:shadow-md transition-all cursor-pointer text-center group shadow-sm',
        link.spanClass ?? '',
      ]"
    >
      <span class="material-symbols-outlined text-3xl text-primary group-hover:scale-110 transition-transform">
        {{ link.icon }}
      </span>
      <span class="text-xs md:text-sm font-bold text-slate-700">{{ link.label }}</span>
    </component>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const links = [
  {
    label: 'Tonton Langsung',
    icon: 'videocam',
    tag: 'a',
    attrs: { href: '/#hero' },
  },
  {
    label: 'Derma',
    icon: 'volunteer_activism',
    tag: Link,
    attrs: { href: '/derma' },
  },
  {
    label: 'Waktu Solat',
    icon: 'schedule',
    tag: 'a',
    attrs: { href: '/#waktu-solat' },
  },
  {
    label: 'Aktiviti',
    icon: 'calendar_month',
    tag: Link,
    attrs: { href: '/aktiviti' },
  },
  {
    label: 'Media Sosial',
    icon: 'share',
    tag: 'a',
    attrs: {
      href: 'https://linktr.ee/mjfkbt3',
      target: '_blank',
      rel: 'noopener noreferrer',
    },
    spanClass: 'col-span-1',
  },
]
</script>
