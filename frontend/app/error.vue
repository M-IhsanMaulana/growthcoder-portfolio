<template>
  <div
    class="flex flex-col min-h-screen bg-[#f5f5ff] dark:bg-zinc-950 text-gray-900 dark:text-gray-100 transition-colors duration-300 relative overflow-hidden"
  >
    <!-- Interactive Glow Cursor Background -->
    <UiGlowCursor />

    <!-- Navbar -->
    <SectionsNavbar class="relative z-50" />

    <!-- Main Content -->
    <main class="flex-grow relative z-10 flex flex-col justify-center">
      <!-- Background decorative elements -->
      <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute top-10 left-1/4 w-2 h-2 rounded-full bg-brand-purple/40" />
        <div class="absolute top-1/3 right-1/4 w-1.5 h-1.5 rounded-full bg-brand-purple/30" />
        <div class="absolute bottom-1/3 left-1/3 w-1 h-1 rounded-full bg-indigo-400/40" />
        <div class="absolute top-1/2 left-1/6 w-2.5 h-2.5 rounded-full bg-brand-purple/20" />
        <div class="absolute bottom-1/4 right-1/3 w-1 h-1 rounded-full bg-indigo-400/30" />
      </div>

      <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Two-column layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">

          <!-- Left column: Text Content -->
          <div class="order-2 lg:order-1 space-y-6">

            <!-- Badge -->
            <div
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border"
              :class="errorConfig.badgeClass"
            >
              <component :is="errorConfig.badgeIcon" class="w-3.5 h-3.5" />
              <span class="text-xs font-semibold">{{ errorConfig.badge }}</span>
            </div>

            <!-- Error Code Heading -->
            <h1
              class="font-black leading-none tracking-tighter drop-shadow-sm"
              :class="[errorConfig.headingClass, is404 ? 'text-[7rem] sm:text-[9rem]' : 'text-[6rem] sm:text-[8rem]']"
            >
              {{ error?.statusCode || 404 }}
            </h1>

            <!-- Error Message -->
            <div class="space-y-3">
              <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-snug">
                {{ errorConfig.title }}
                <span :class="errorConfig.accentClass">{{ errorConfig.titleAccent }}</span>
              </h2>
              <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm">
                {{ errorConfig.description }}
              </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-2">
              <button
                id="btn-back-home"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
                :class="errorConfig.btnPrimaryClass"
                @click="handleClearError"
              >
                <!-- Home icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                  <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Kembali ke Beranda
              </button>

              <!-- Retry button for server errors -->
              <button
                v-if="isServerError"
                id="btn-retry"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm text-gray-700 dark:text-gray-300 text-sm font-semibold hover:border-brand-purple/40 hover:text-brand-purple dark:hover:text-brand-purple hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
                @click="handleRetry"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                  <path d="M3 3v5h5" />
                </svg>
                Coba Lagi
              </button>

              <!-- Explore projects for 404 -->
              <NuxtLink
                v-else
                id="btn-explore-projects"
                to="/proyek"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm text-gray-700 dark:text-gray-300 text-sm font-semibold hover:border-brand-purple/40 hover:text-brand-purple dark:hover:text-brand-purple hover:-translate-y-0.5 transition-all duration-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 16v-4" />
                  <path d="M12 8h.01" />
                </svg>
                Jelajahi Proyek
              </NuxtLink>
            </div>

            <!-- Popular Pages (only for 404) -->
            <div v-if="is404" class="pt-2 space-y-3">
              <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                Atau lihat beberapa halaman populer:
              </p>
              <div class="flex flex-wrap gap-2">
                <NuxtLink
                  v-for="page in popularPages"
                  :key="page.to"
                  :to="page.to"
                  :id="`quick-link-${page.label.toLowerCase()}`"
                  class="group flex flex-col items-center gap-1.5 px-4 py-3 rounded-xl bg-white dark:bg-zinc-900 border border-gray-100 dark:border-zinc-800 hover:border-brand-purple/30 dark:hover:border-brand-purple/40 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 hover:-translate-y-0.5 transition-all duration-200 shadow-sm min-w-[64px]"
                >
                  <component :is="page.icon" class="w-5 h-5 text-gray-400 group-hover:text-brand-purple transition-colors duration-200" />
                  <span class="text-xs text-gray-500 dark:text-gray-400 group-hover:text-brand-purple dark:group-hover:text-brand-purple transition-colors duration-200">
                    {{ page.label }}
                  </span>
                </NuxtLink>
              </div>
            </div>

            <!-- Error detail info for server errors -->
            <div
              v-if="isServerError && error?.message"
              class="pt-2 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-100 dark:border-rose-900/40"
            >
              <p class="text-xs text-rose-600 dark:text-rose-400 font-mono leading-relaxed break-all">
                {{ error.message }}
              </p>
            </div>
          </div>

          <!-- Right column: Illustration -->
          <div class="order-1 lg:order-2 flex items-center justify-center">
            <div class="relative w-full max-w-md">
              <!-- Circular background blob -->
              <div
                class="absolute inset-0 m-auto w-80 h-80 rounded-full blur-sm"
                :class="errorConfig.blobClass"
              />

              <!-- Floating decorations -->
              <div class="absolute top-8 right-12 text-brand-purple/40 text-xl animate-pulse">✦</div>
              <div class="absolute top-24 right-4 text-indigo-400/30 text-sm animate-pulse" style="animation-delay: 0.5s">✦</div>
              <div class="absolute bottom-16 left-8 text-brand-purple/30 text-xs animate-pulse" style="animation-delay: 1s">✦</div>
              <div class="absolute top-12 left-12 text-indigo-300/40 text-lg animate-pulse" style="animation-delay: 1.5s">+</div>
              <div class="absolute bottom-24 right-16 text-brand-purple/20 text-base animate-pulse" style="animation-delay: 0.8s">+</div>

              <!-- Planet decorations (404 only) -->
              <template v-if="is404">
                <div class="absolute top-6 right-6 w-10 h-10 rounded-full bg-gradient-to-br from-green-300 to-emerald-400 dark:from-green-400 dark:to-emerald-500 shadow-lg shadow-green-300/30" />
                <div class="absolute top-20 left-6 w-8 h-8 rounded-full bg-gradient-to-br from-purple-300 to-indigo-400 dark:from-purple-400 dark:to-indigo-500 shadow-lg shadow-purple-300/30 opacity-70" />
              </template>

              <!-- Warning decoration (5xx errors) -->
              <template v-if="isServerError">
                <div class="absolute top-6 right-6 flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-500 text-lg animate-pulse shadow-md">⚠</div>
                <div class="absolute top-20 left-6 w-8 h-8 rounded-full bg-gradient-to-br from-orange-300 to-red-300 dark:from-orange-400 dark:to-red-400 shadow-lg opacity-60 animate-pulse" style="animation-delay: 1s" />
              </template>

              <!-- Main illustration -->
              <div class="relative z-10 flex items-center justify-center py-8">
                <div
                  class="rounded-full overflow-hidden shadow-2xl animate-float"
                  :class="[
                    is404 ? 'w-72 h-72 sm:w-80 sm:h-80 shadow-purple-200/60 dark:shadow-purple-900/40' : 'w-64 h-64 sm:w-72 sm:h-72 shadow-rose-200/60 dark:shadow-rose-900/40'
                  ]"
                >
                  <NuxtImg
                    :src="errorConfig.illustration"
                    :alt="errorConfig.illustrationAlt"
                    class="w-full h-full object-cover"
                    width="320"
                    height="320"
                    loading="eager"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Help Bar -->
      <div class="relative z-10 border-t border-gray-100 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="flex items-center justify-center w-9 h-9 rounded-full bg-amber-50 dark:bg-amber-900/30 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                  <path d="M12 17h.01" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Butuh bantuan?</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Jika menurutmu ini adalah kesalahan, jangan ragu untuk menghubungi saya.</p>
              </div>
            </div>
            <NuxtLink
              id="btn-contact-help"
              to="/contact"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:border-brand-purple/40 hover:text-brand-purple dark:hover:text-brand-purple hover:-translate-y-0.5 transition-all duration-200 shadow-sm whitespace-nowrap shrink-0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
              Hubungi Saya
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m7 7 10 10" />
                <path d="M17 7v10H7" />
              </svg>
            </NuxtLink>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, h } from 'vue'

const props = defineProps({
  error: {
    type: Object,
    default: () => ({ statusCode: 404, message: 'Page not found' })
  }
})

const statusCode = computed(() => props.error?.statusCode ?? 404)
const is404 = computed(() => statusCode.value === 404)
const isServerError = computed(() => statusCode.value >= 500)

// ─── Error config map ───────────────────────────────────────────────
type ErrorConfig = {
  badge: string
  badgeClass: string
  badgeIcon: ReturnType<typeof h>
  title: string
  titleAccent: string
  accentClass: string
  headingClass: string
  description: string
  btnPrimaryClass: string
  illustration: string
  illustrationAlt: string
  blobClass: string
}

const errorConfigs: Record<string, ErrorConfig> = {
  404: {
    badge: 'Halaman Tidak Ditemukan',
    badgeClass: 'bg-brand-purple/10 dark:bg-brand-purple/20 border-brand-purple/20 dark:border-brand-purple/30 text-brand-purple',
    badgeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'w-3.5 h-3.5', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
      h('circle', { cx: '11', cy: '11', r: '8' }),
      h('path', { d: 'm21 21-4.3-4.3' })
    ]),
    title: 'Oops! Halaman yang kamu cari',
    titleAccent: 'tidak ditemukan.',
    accentClass: 'block text-brand-purple dark:text-brand-purple',
    headingClass: 'text-brand-purple dark:text-brand-purple',
    description: 'Halaman ini mungkin telah dihapus, dipindahkan, atau URL yang kamu masukkan salah.',
    btnPrimaryClass: 'bg-brand-purple shadow-brand-purple/30 hover:bg-brand-purple/90 hover:shadow-brand-purple/40',
    illustration: '/astronaut-404.png',
    illustrationAlt: 'Astronaut lost in space',
    blobClass: 'bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30',
  },
  500: {
    badge: 'Kesalahan Server',
    badgeClass: 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400',
    badgeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'w-3.5 h-3.5', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
      h('path', { d: 'm21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3' }),
      h('path', { d: 'M12 9v4' }),
      h('path', { d: 'M12 17h.01' })
    ]),
    title: 'Waduh! Terjadi',
    titleAccent: 'kesalahan pada server.',
    accentClass: 'block text-rose-500 dark:text-rose-400',
    headingClass: 'text-rose-500 dark:text-rose-400',
    description: 'Server sedang mengalami masalah. Tim kami sudah mengetahuinya dan sedang memperbaikinya. Coba lagi beberapa saat.',
    btnPrimaryClass: 'bg-rose-500 shadow-rose-300/30 hover:bg-rose-600 hover:shadow-rose-400/40',
    illustration: '/server-error-500.png',
    illustrationAlt: 'Broken server illustration',
    blobClass: 'bg-gradient-to-br from-rose-100 to-orange-100 dark:from-rose-900/20 dark:to-orange-900/20',
  },
  503: {
    badge: 'Layanan Tidak Tersedia',
    badgeClass: 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/50 text-amber-600 dark:text-amber-400',
    badgeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'w-3.5 h-3.5', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
      h('path', { d: 'M12 2v4' }),
      h('path', { d: 'M12 18v4' }),
      h('path', { d: 'M4.93 4.93l2.83 2.83' }),
      h('path', { d: 'M16.24 16.24l2.83 2.83' }),
      h('path', { d: 'M2 12h4' }),
      h('path', { d: 'M18 12h4' }),
      h('path', { d: 'M4.93 19.07l2.83-2.83' }),
      h('path', { d: 'M16.24 7.76l2.83-2.83' })
    ]),
    title: 'Sedang dalam',
    titleAccent: 'pemeliharaan.',
    accentClass: 'block text-amber-500 dark:text-amber-400',
    headingClass: 'text-amber-500 dark:text-amber-400',
    description: 'Website sedang dalam proses pemeliharaan atau update. Kamu bisa kembali lagi dalam beberapa saat.',
    btnPrimaryClass: 'bg-amber-500 shadow-amber-300/30 hover:bg-amber-600 hover:shadow-amber-400/40',
    illustration: '/server-error-500.png',
    illustrationAlt: 'Server under maintenance',
    blobClass: 'bg-gradient-to-br from-amber-100 to-yellow-100 dark:from-amber-900/20 dark:to-yellow-900/20',
  },
  403: {
    badge: 'Akses Ditolak',
    badgeClass: 'bg-orange-50 dark:bg-orange-950/30 border-orange-200 dark:border-orange-800/50 text-orange-600 dark:text-orange-400',
    badgeIcon: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', class: 'w-3.5 h-3.5', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
      h('rect', { width: '18', height: '11', x: '3', y: '11', rx: '2', ry: '2' }),
      h('path', { d: 'M7 11V7a5 5 0 0 1 10 0v4' })
    ]),
    title: 'Kamu tidak punya',
    titleAccent: 'izin akses.',
    accentClass: 'block text-orange-500 dark:text-orange-400',
    headingClass: 'text-orange-500 dark:text-orange-400',
    description: 'Halaman ini bersifat privat dan memerlukan izin khusus untuk mengaksesnya.',
    btnPrimaryClass: 'bg-orange-500 shadow-orange-300/30 hover:bg-orange-600 hover:shadow-orange-400/40',
    illustration: '/astronaut-404.png',
    illustrationAlt: 'Access denied illustration',
    blobClass: 'bg-gradient-to-br from-orange-100 to-amber-100 dark:from-orange-900/20 dark:to-amber-900/20',
  },
}

// Resolve config: exact match → server error default → fallback 404
const errorConfig = computed<ErrorConfig>(() => {
  const code = statusCode.value
  if (errorConfigs[code]) return errorConfigs[code]
  if (code >= 500) return errorConfigs[500]
  if (code >= 400) return errorConfigs[404]
  return errorConfigs[404]
})

// ─── Icon components for quick links ───────────────────────────────
const IconAbout = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
  h('path', { d: 'M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2' }),
  h('circle', { cx: '12', cy: '7', r: '4' })
])

const IconSkills = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
  h('polyline', { points: '16 18 22 12 16 6' }),
  h('polyline', { points: '8 6 2 12 8 18' })
])

const IconProjects = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
  h('rect', { width: '20', height: '14', x: '2', y: '3', rx: '2' }),
  h('line', { x1: '8', x2: '16', y1: '21', y2: '21' }),
  h('line', { x1: '12', x2: '12', y1: '17', y2: '21' })
])

const IconBlog = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
  h('path', { d: 'M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z' }),
  h('polyline', { points: '14 2 14 8 20 8' }),
  h('line', { x1: '16', x2: '8', y1: '13', y2: '13' }),
  h('line', { x1: '16', x2: '8', y1: '17', y2: '17' }),
  h('line', { x1: '10', x2: '8', y1: '9', y2: '9' })
])

const IconContact = () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2', 'stroke-linecap': 'round', 'stroke-linejoin': 'round' }, [
  h('path', { d: 'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.92 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z' })
])

const popularPages = [
  { to: '/about', label: 'About', icon: IconAbout },
  { to: '/about#skills', label: 'Skills', icon: IconSkills },
  { to: '/proyek', label: 'Projects', icon: IconProjects },
  { to: '/blog', label: 'Blog', icon: IconBlog },
  { to: '/contact', label: 'Contact', icon: IconContact },
]

const handleClearError = () => {
  clearError({ redirect: '/' })
}

const handleRetry = () => {
  clearError({ redirect: useRequestURL().pathname })
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-12px); }
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}
</style>
