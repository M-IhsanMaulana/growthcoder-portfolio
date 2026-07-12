<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 pb-20">

    <!-- ─── HERO SECTION ─────────────────────────────────────────────── -->
    <div ref="heroSection" class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10 text-center space-y-6 opacity-0 -translate-y-3">
      <!-- Decorative radial glow orbs -->
      <div class="absolute -top-20 -left-20 w-80 h-80 bg-brand-purple/10 blur-3xl rounded-full pointer-events-none"></div>
      <div class="absolute -top-10 right-0 w-64 h-64 bg-brand-green/10 blur-3xl rounded-full pointer-events-none"></div>

      <!-- Badge -->
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.05)]">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
        <span class="text-[10px] font-bold tracking-widest uppercase text-emerald-600 dark:text-emerald-400">Layanan Saya</span>
      </div>

      <!-- Heading -->
      <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] space-y-1">
        <span class="block text-zinc-900 dark:text-white">Solusi Digital yang</span>
        <span class="block bg-gradient-to-r from-brand-purple to-indigo-500 bg-clip-text text-transparent">
          Saya Tawarkan
        </span>
      </h1>

      <!-- Subtitle -->
      <p class="text-sm md:text-base text-zinc-550 dark:text-zinc-400 leading-relaxed font-light max-w-xl mx-auto">
        Saya membantu individu dan bisnis membangun produk digital yang modern, cepat, aman,
        dan skalabel — dari konsep hingga produksi.
      </p>

      <!-- Divider -->
      <div class="flex justify-center">
        <div class="w-16 h-0.5 bg-gradient-to-r from-brand-purple to-brand-green rounded-full"></div>
      </div>

      <!-- 3 Feature Pillars -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
        <Card
          v-for="pillar in featurePillars"
          :key="pillar.label"
          :pt="{
            root: { class: '!bg-white dark:!bg-zinc-900/60 !border !border-zinc-150 dark:!border-zinc-800 !rounded-2xl shadow-sm hover:shadow-md transition-all duration-300' },
            body: { class: '!p-5' }
          }"
        >
          <template #content>
            <div class="flex items-center gap-3.5">
              <div :class="['flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center', pillar.iconBg]">
                <component :is="'svg'" class="w-5 h-5" :class="pillar.iconColor" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" v-html="pillar.iconPath" />
              </div>
              <div class="text-left">
                <p class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ pillar.label }}</p>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ pillar.desc }}</p>
              </div>
            </div>
          </template>
        </Card>
      </div>
    </div>

    <!-- ─── SERVICES GRID ────────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      
      <!-- Section Header -->
      <div class="flex items-center gap-3 mb-8">
        <span class="w-2 h-2 rounded-full bg-brand-purple"></span>
        <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Semua Layanan</span>
        <div class="flex-1 h-px bg-zinc-200/60 dark:bg-zinc-800/60"></div>
        <span v-if="services.length" class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
          {{ services.length }} Layanan
        </span>
      </div>

      <!-- Services Grid -->
      <div
        v-if="services.length"
        ref="servicesGrid"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
      >
        <Card
          v-for="(service, idx) in services"
          :key="service.id"
          class="service-card cursor-pointer opacity-0 translate-y-6"
          :pt="{
            root: { class: '!bg-white dark:!bg-zinc-900 !border !border-zinc-200/70 dark:!border-zinc-800/80 !rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group' },
            body: { class: '!p-6' }
          }"
          @click="openServiceModal(service, idx)"
        >
          <template #content>
            <!-- Dot grid decoration (top-right) -->
            <div
              class="absolute top-5 right-5 w-16 h-10 opacity-[0.06] dark:opacity-[0.04] pointer-events-none"
              style="background-image: radial-gradient(circle, currentColor 1.5px, transparent 1.5px); background-size: 8px 8px;"
            ></div>

            <!-- Card Header: Badge + Category Name -->
            <div class="flex items-center gap-2 mb-5">
              <!-- Number Badge -->
              <span
                class="px-2.5 py-0.5 text-[10px] font-bold font-mono rounded-md"
                :class="[getScheme(idx).numBg, getScheme(idx).numColor]"
              >
                {{ String(idx + 1).padStart(2, '0') }}
              </span>

              <!-- Category Name (from service.icon string, e.g. "Code", "Server", "Bot", "Zap") -->
              <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                {{ getIconCategoryName(service.icon) }}
              </span>
            </div>

            <!-- Card Body: Icon on Left, Title + Subtitle + Link on Right -->
            <div class="flex items-start gap-4">
              <!-- Icon container (Renders SVG matching the icon string) -->
              <div
                class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-105 [&_svg]:w-5 [&_svg]:h-5"
                :class="getScheme(idx).iconBg"
                v-html="getIconSvg(service.icon)"
              >
              </div>

              <!-- Content wrapper -->
              <div class="flex-1 min-w-0 space-y-2">
                <!-- Title (Full-Stack Web Development, etc.) -->
                <h3 class="text-sm font-extrabold text-zinc-900 dark:text-white leading-snug transition-colors duration-200" :class="getScheme(idx).titleHover">
                  {{ service.title }}
                </h3>
                
                <!-- Description -->
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-relaxed font-light line-clamp-3">
                  {{ service.short_description }}
                </p>

                <!-- Footer link -->
                <div class="pt-2">
                  <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold transition-colors duration-200" :class="getScheme(idx).link">
                    Lihat Detail
                    <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Skeleton Loading (matches the exact layout above) -->
      <div v-else-if="pending" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <Card
          v-for="i in 6"
          :key="i"
          :pt="{
            root: { class: '!bg-white dark:!bg-zinc-900 !border !border-zinc-200/70 dark:!border-zinc-800 !rounded-2xl shadow-sm' },
            body: { class: '!p-6' }
          }"
        >
          <template #content>
            <div class="flex items-center gap-2 mb-5">
              <Skeleton width="1.75rem" height="1.1rem" borderRadius="6px" />
              <Skeleton width="4rem" height="0.875rem" />
            </div>
            <div class="flex items-start gap-4">
              <Skeleton width="2.75rem" height="2.75rem" borderRadius="12px" />
              <div class="flex-1 space-y-2">
                <Skeleton width="10rem" height="0.875rem" />
                <div class="space-y-1.5">
                  <Skeleton width="100%" height="0.75rem" />
                  <Skeleton width="92%" height="0.75rem" />
                  <Skeleton width="75%" height="0.75rem" />
                </div>
                <Skeleton width="5rem" height="0.75rem" class="mt-2" />
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Empty State -->
      <div v-else class="py-20 flex flex-col items-center gap-4 text-center">
        <div class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
          <svg class="w-7 h-7 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22" />
          </svg>
        </div>
        <p class="text-xs text-zinc-400 dark:text-zinc-600 italic">Belum ada layanan aktif yang ditambahkan.</p>
      </div>
    </section>

    <!-- ─── CTA BANNER ────────────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        ref="ctaSection"
        class="opacity-0 translate-y-6 relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-900/60 border border-zinc-200/70 dark:border-zinc-800 p-8 md:p-10 shadow-sm"
      >
        <!-- Subtle background glow decoration -->
        <div class="absolute -top-12 -left-12 w-40 h-40 bg-brand-purple/5 opacity-40 blur-3xl rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-brand-green/5 opacity-40 blur-3xl rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-6 justify-between">
          <!-- Left: Icon + Text Stack -->
          <div class="flex items-center gap-5">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100/40 dark:border-indigo-900/30 flex items-center justify-center text-brand-purple dark:text-indigo-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
            </div>
            <div>
              <p class="text-[9px] font-bold uppercase tracking-widest text-brand-green mb-1">Mulai Proyek Bersama</p>
              <h3 class="text-lg md:text-xl font-extrabold text-zinc-900 dark:text-white leading-tight">
                Ada proyek yang ingin diwujudkan?
              </h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 font-light leading-relaxed">
                Saya terbuka untuk diskusi, kolaborasi, atau peluang kerja baru yang menantang.
              </p>
            </div>
          </div>

          <!-- Right: Hubungi Saya button with arrow-up-right -->
          <div class="flex-shrink-0">
            <Button
              as="router-link"
              to="/contact"
              label="Hubungi Saya"
              icon="pi pi-arrow-up-right"
              iconPos="right"
              class="!px-6 !py-3.5 !font-bold !rounded-xl !text-xs !bg-emerald-500 !border-emerald-500 hover:!bg-emerald-600 transition-all duration-300 cursor-pointer shadow-md shadow-emerald-500/10 !text-white"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- ─── DETAIL MODAL (PrimeVue Dialog) ─────────────────────────────── -->
    <Dialog
      v-model:visible="isModalOpen"
      modal
      :dismissableMask="true"
      :style="{ width: '90vw', maxWidth: '520px' }"
      :pt="{
        root: { class: '!rounded-2xl !border !border-zinc-200 dark:!border-zinc-800 !shadow-2xl' },
        header: { class: '!px-6 !pt-6 !pb-4 !border-b !border-zinc-100 dark:!border-zinc-800/80 !bg-white dark:!bg-zinc-950 !rounded-t-2xl' },
        content: { class: '!px-6 !py-5 !bg-white dark:!bg-zinc-950' },
        footer: { class: '!px-6 !pb-6 !pt-4 !bg-white dark:!bg-zinc-950 !rounded-b-2xl !border-t !border-zinc-100 dark:!border-zinc-800/80' },
        closeButton: { class: '!rounded-xl !w-8 !h-8 hover:!bg-zinc-100 dark:hover:!bg-zinc-900 transition-colors duration-200' },
        mask: { class: '!backdrop-blur-sm' }
      }"
    >
      <!-- Modal Header -->
      <template #header>
        <div class="flex items-center gap-4" v-if="selectedService">
          <div
            class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0 [&_svg]:w-5 [&_svg]:h-5"
            :class="getScheme(selectedServiceIdx).iconBg"
            v-html="getIconSvg(selectedService.icon)"
          >
          </div>
          <div>
            <p class="text-[9px] font-bold uppercase tracking-widest mb-0.5" :class="getScheme(selectedServiceIdx).link">
              Layanan
            </p>
            <h3 class="text-base font-extrabold text-zinc-900 dark:text-white leading-tight">
              {{ selectedService.title }}
            </h3>
          </div>
        </div>
      </template>

      <!-- Modal Body -->
      <div v-if="selectedService" class="space-y-4">
        <!-- Short description highlight -->
        <div class="rounded-xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-100 dark:border-zinc-800 p-4">
          <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed font-light">
            {{ selectedService.short_description }}
          </p>
        </div>

        <!-- Long description (rich text) -->
        <div
          v-if="selectedService.long_description"
          class="rich-text-content prose prose-sm dark:prose-invert max-w-none text-zinc-600 dark:text-zinc-300 leading-relaxed text-xs"
          v-html="selectedService.long_description"
        ></div>
        <p v-else class="text-xs text-center text-zinc-400 dark:text-zinc-600 italic py-3">
          Detail lebih lanjut dapat didiskusikan langsung.
        </p>
      </div>

      <!-- Modal Footer -->
      <template #footer>
        <div class="flex items-center justify-between gap-3">
          <Button
            label="Tutup"
            severity="secondary"
            variant="outlined"
            class="!rounded-xl !text-xs !px-5 cursor-pointer"
            @click="isModalOpen = false"
          />
          <Button
            as="router-link"
            to="/contact"
            label="Hubungi Saya"
            icon="pi pi-envelope"
            class="!rounded-xl !text-xs !font-semibold !px-5 cursor-pointer"
            @click="isModalOpen = false"
          />
        </div>
      </template>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'

// ── Page Meta ──────────────────────────────────────────────────────────
definePageMeta({ layout: 'default' })

// ── Settings ──────────────────────────────────────────────────────────
const { settings, fetchSettings } = useSettings()
await fetchSettings()

useSeoMeta({
  title: 'Layanan Jasa',
  description: 'Temukan layanan pengembangan digital profesional: web app, API backend, bot Telegram, dan solusi skalabel lainnya.',
  ogTitle: 'Layanan Jasa | growthcoder.id',
  ogDescription: 'Temukan layanan pengembangan digital profesional: web app, API backend, bot Telegram, dan solusi skalabel lainnya.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/services' }
  ]
})

// ── Data Fetching ─────────────────────────────────────────────────────
const { data: response, pending } = await useFetchAPI<any>('/services')

const services = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data.filter((s: any) => s.is_active)
  }
  return []
})

// ── Feature Pillars data ───────────────────────────────────────────────
const featurePillars = [
  {
    label: 'Cepat & Efisien',
    desc: 'Proses terstruktur & tepat waktu untuk hasil maksimal.',
    iconBg: 'bg-purple-500/10',
    iconColor: 'text-purple-600 dark:text-purple-400',
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />',
  },
  {
    label: 'Aman & Terpercaya',
    desc: 'Keamanan & performa menjadi prioritas utama.',
    iconBg: 'bg-emerald-500/10',
    iconColor: 'text-emerald-600 dark:text-emerald-400',
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
  },
  {
    label: 'Skalabel',
    desc: 'Arsitektur siap berkembang sesuai kebutuhan.',
    iconBg: 'bg-blue-500/10',
    iconColor: 'text-blue-600 dark:text-blue-400',
    iconPath: '<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
  },
]

// ── Modal ──────────────────────────────────────────────────────────────
const isModalOpen = ref(false)
const selectedService = ref<any>(null)
const selectedServiceIdx = ref(0)

const openServiceModal = (service: any, idx: number) => {
  selectedService.value = service
  selectedServiceIdx.value = idx
  isModalOpen.value = true
}

// ── Icon Mapping (Resolves string to SVG) ─────────────────────────────
const getIconSvg = (iconName: string | null): string => {
  if (!iconName) return '';
  if (iconName.trim().startsWith('<svg')) {
    return iconName;
  }
  const mappings: Record<string, string> = {
    Code: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>`,
    Server: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="2" rx="2" ry="2"/><rect width="20" height="8" x="2" y="14" rx="2" ry="2"/><line x1="6" x2="6.01" y1="6" y2="6"/><line x1="6" x2="6.01" y1="18" y2="18"/></svg>`,
    Bot: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>`,
    Zap: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
  }
  return mappings[iconName] ?? mappings.Code
}

const getIconCategoryName = (icon: string | null): string => {
  if (!icon) return 'Service';
  if (icon.trim().startsWith('<svg')) {
    if (icon.includes('lucide-code-2') || icon.includes('lucide-code')) return 'Code';
    if (icon.includes('lucide-server')) return 'Server';
    if (icon.includes('lucide-bot')) return 'Bot';
    if (icon.includes('lucide-zap')) return 'Zap';
    return 'Service';
  }
  return icon;
}

// ── Color Schemes ──────────────────────────────────────────────────────
const getScheme = (idx: number) => {
  const schemes = [
    {
      numBg: 'bg-purple-100/50 dark:bg-purple-950/40',
      numColor: 'text-purple-600 dark:text-purple-400',
      iconBg: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/10 dark:border-purple-500/5',
      link: 'text-purple-600 dark:text-purple-400 hover:text-purple-700',
      titleHover: 'group-hover:text-purple-600 dark:group-hover:text-purple-400',
    },
    {
      numBg: 'bg-emerald-100/50 dark:bg-emerald-950/40',
      numColor: 'text-emerald-600 dark:text-emerald-400',
      iconBg: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/10 dark:border-emerald-500/5',
      link: 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-700',
      titleHover: 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400',
    },
    {
      numBg: 'bg-blue-100/50 dark:bg-blue-950/40',
      numColor: 'text-blue-600 dark:text-blue-400',
      iconBg: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/10 dark:border-blue-500/5',
      link: 'text-blue-600 dark:text-blue-400 hover:text-blue-700',
      titleHover: 'group-hover:text-blue-600 dark:group-hover:text-blue-400',
    },
    {
      numBg: 'bg-amber-100/50 dark:bg-amber-950/30',
      numColor: 'text-amber-600 dark:text-amber-500',
      iconBg: 'bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/10 dark:border-amber-500/5',
      link: 'text-amber-600 dark:text-amber-500 hover:text-amber-700',
      titleHover: 'group-hover:text-amber-600 dark:group-hover:text-amber-500',
    },
    {
      numBg: 'bg-indigo-100/50 dark:bg-indigo-950/40',
      numColor: 'text-indigo-600 dark:text-indigo-400',
      iconBg: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/10 dark:border-indigo-500/5',
      link: 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-700',
      titleHover: 'group-hover:text-indigo-600 dark:group-hover:text-indigo-400',
    },
    {
      numBg: 'bg-rose-100/50 dark:bg-rose-950/40',
      numColor: 'text-rose-600 dark:text-rose-400',
      iconBg: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/10 dark:border-rose-500/5',
      link: 'text-rose-600 dark:text-rose-400 hover:text-rose-700',
      titleHover: 'group-hover:text-rose-600 dark:group-hover:text-rose-400',
    },
  ]
  return schemes[idx % schemes.length]
}

// ── GSAP ───────────────────────────────────────────────────────────────
const { $gsap } = useNuxtApp()
const heroSection = ref<HTMLElement | null>(null)
const servicesGrid = ref<HTMLElement | null>(null)
const ctaSection = ref<HTMLElement | null>(null)

const initAnimations = () => {
  if (!$gsap) return

  if (heroSection.value) {
    $gsap.to(heroSection.value, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', delay: 0.05 })
  }

  if (servicesGrid.value) {
    $gsap.to('.service-card', {
      opacity: 1,
      y: 0,
      duration: 0.55,
      stagger: 0.08,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: servicesGrid.value,
        start: 'top 82%',
        toggleActions: 'play none none none',
      },
    })
  }

  if (ctaSection.value) {
    $gsap.to(ctaSection.value, {
      opacity: 1,
      y: 0,
      duration: 0.65,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: ctaSection.value,
        start: 'top 88%',
        toggleActions: 'play none none none',
      },
    })
  }
}

onMounted(() => {
  nextTick(() => initAnimations())
})

watch(pending, (isPending) => {
  if (!isPending) {
    nextTick(() => initAnimations())
  }
})
</script>
