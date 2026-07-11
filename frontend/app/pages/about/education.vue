<template>
  <div class="pb-16">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
      <!-- Left Column: Sticky Title & Info -->
      <div class="lg:col-span-4 lg:sticky lg:top-24 space-y-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100/30 dark:border-indigo-900/30 shadow-xs">
          <span class="text-xs shrink-0">🎓</span>
          <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">PENDIDIKAN</span>
        </div>
        
        <h1 class="text-3xl sm:text-4xl font-extrabold text-brand-navy dark:text-white leading-tight">
          Riwayat <span class="bg-gradient-to-r from-brand-purple to-indigo-500 dark:to-brand-green bg-clip-text text-transparent">Pendidikan</span>
        </h1>
        <div class="w-16 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-xs"></div>
        <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base font-light leading-relaxed">
          Rekam jejak akademis dan pembentukan fondasi teoritis saya.
        </p>

        <!-- Decorative Pattern -->
        <div class="hidden lg:block relative h-32 w-full overflow-hidden mt-8 opacity-25 dark:opacity-10 pointer-events-none">
          <div class="absolute -left-10 -bottom-10 w-36 h-36 rounded-full bg-brand-purple/15 dark:bg-brand-purple/5 blur-3xl"></div>
          <div class="absolute bottom-4 left-4 w-24 h-16 text-gray-400 dark:text-zinc-700" style="background-image: radial-gradient(circle, currentColor 1.2px, transparent 1.2px); background-size: 8px 8px;"></div>
        </div>
      </div>

      <!-- Right Column: Custom Timeline List -->
      <div 
        ref="timelineContainer"
        class="lg:col-span-8 relative pl-6 sm:pl-10 py-2 border-l border-indigo-100 dark:border-zinc-800/80 space-y-12"
      >
        <div 
          v-if="educations && educations.length" 
          class="space-y-12"
        >
          <!-- Education Card Wrapper -->
          <div 
            v-for="(item, idx) in educations" 
            :key="item.id"
            class="timeline-item opacity-0 relative group"
          >
            <!-- Marker dot on timeline line -->
            <div 
              class="absolute -left-[calc(1.5rem+9.5px)] sm:-left-[calc(2.5rem+9.5px)] top-6 w-4.5 h-4.5 rounded-full border-4 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center transition-transform duration-300 group-hover:scale-125 z-10"
              :class="getPalette(idx).markerBorder"
            >
              <div class="w-1.5 h-1.5 rounded-full" :class="getPalette(idx).markerDot"></div>
            </div>

            <!-- Gradient Border Wrapper -->
            <div
              class="relative rounded-3xl p-[1.5px] shadow-xs transition-all duration-300 group-hover:shadow-md"
              :style="getPalette(idx).gradientBorder"
            >
              <!-- Speech-bubble Card inner -->
              <div class="relative bg-white dark:bg-zinc-950 rounded-[calc(1.5rem-1.5px)] p-6 sm:p-8">
                <!-- Left triangular pointer -->
                <div
                  class="absolute top-[22px] -left-[10px] w-4 h-4 rotate-45 border-l-2 border-b-2 bg-white dark:bg-zinc-950 z-40 transition-colors duration-300"
                  :class="getPalette(idx).pointerBorder"
                ></div>

                <div class="relative z-10">
                  <!-- Card Header -->
                  <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                      <!-- Logo / Fallback initials -->
                      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-base shrink-0 overflow-hidden shadow-xs border border-gray-105 dark:border-zinc-900/80" :class="getPalette(idx).iconBg">
                        <NuxtImg 
                          v-if="item.logo?.urls?.medium || item.logo?.urls?.original"
                          :src="item.logo.urls.medium || item.logo.urls.original" 
                          :alt="item.institution"
                          class="w-full h-full object-cover"
                        />
                        <span v-else class="font-bold">
                          {{ item.institution.substring(0, 2).toUpperCase() }}
                        </span>
                      </div>

                      <!-- Degree, Major, Institution, Location -->
                      <div class="space-y-0.5">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">
                          {{ item.degree }}
                          <span v-if="item.major" class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                            di {{ item.major }}
                          </span>
                        </h2>
                        
                        <!-- Institution & Location parsed -->
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                          <span>{{ getInstLocation(item).institution }}</span>
                          <span v-if="getInstLocation(item).location" class="text-gray-300 dark:text-zinc-700 mx-1.5">•</span>
                          <span class="font-normal text-gray-450 dark:text-gray-500">{{ getInstLocation(item).location }}</span>
                        </p>

                        <!-- Separate type badge (e.g. "(Hybrid)") if present -->
                        <div v-if="getInstLocation(item).type" class="pt-1">
                          <span class="text-[11px] font-bold tracking-wider text-indigo-500 dark:text-indigo-400">
                            {{ getInstLocation(item).type }}
                          </span>
                        </div>
                      </div>
                    </div>

                    <!-- Date / Academic Period Badge -->
                    <div class="shrink-0 self-start">
                      <span 
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-colors duration-300"
                        :class="getPalette(idx).badge"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>{{ formatEduPeriod(item.start_date, item.end_date) }}</span>
                      </span>
                    </div>
                  </div>

                  <!-- GPA & Description -->
                  <div class="space-y-4 mt-6 border-t border-gray-50 dark:border-zinc-900/50 pt-4">
                    <!-- GPA Tag styled beautifully -->
                    <div v-if="item.gpa">
                      <span class="inline-flex items-center space-x-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-brand-green dark:text-brand-green border border-brand-green/20 rounded-lg text-xs font-bold shadow-2xs">
                        <span>🎓 IPK / GPA:</span>
                        <span class="font-black">{{ item.gpa }}</span>
                      </span>
                    </div>

                    <!-- Academic Description (HTML content) -->
                    <div 
                      v-if="item.description" 
                      class="rich-text-content prose prose-sm max-w-none text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-light"
                      v-html="item.description"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16 bg-white dark:bg-zinc-950 border border-gray-150 dark:border-zinc-900 rounded-3xl p-8">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" class="text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 14.25a11.952 11.952 0 00-6.825-1.192 12.083 12.083 0 01.665-6.479L12 14z" />
          </svg>
          <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada riwayat pendidikan yang ditambahkan di CMS.</p>
        </div>

        <!-- Bottom Timeline Axis End Node -->
        <div v-if="educations && educations.length" class="relative pl-4 pt-2">
          <div class="absolute -left-[calc(1.5rem+16.5px)] sm:-left-[calc(2.5rem+16.5px)] top-3 w-8 h-8 rounded-full border border-indigo-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center text-indigo-400 dark:text-zinc-660 transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m14-6l-7 7-7-7" />
            </svg>
          </div>
          <span class="text-xs text-brand-purple dark:text-indigo-400 font-bold tracking-wide ml-4 animate-pulse">
            Perjalanan masih berlanjut...
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue'

definePageMeta({
  layout: 'about'
})

const { $gsap } = useNuxtApp()
const { settings } = useSettings()

useSeoMeta({
  title: 'Pendidikan',
  description: 'Rekam jejak akademis dan pembentukan fondasi teoritis/praktis Muhammad Ihsan Maulana.',
  ogTitle: 'Pendidikan | growthcoder.id',
  ogDescription: 'Rekam jejak akademis dan pembentukan fondasi teoritis/praktis Muhammad Ihsan Maulana.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/about/education' }
  ]
})

const timelineContainer = ref<HTMLElement | null>(null)

// Fetch educations list from API
const { data: response } = await useFetchAPI<any>('/educations')

const educations = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data
  }
  return []
})

// Palette definitions matching image design
const colorPalettes = [
  {
    markerBorder: 'border-brand-purple dark:border-brand-purple/70',
    markerDot: 'bg-brand-purple',
    badge: 'bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-900/30',
    iconBg: 'bg-indigo-50 dark:bg-indigo-950/40 text-brand-purple dark:text-indigo-400 border border-indigo-100/30 dark:border-indigo-900/20',
    gradientBorder: 'background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%)',
    pointerBorder: 'border-indigo-500',
  },
  {
    markerBorder: 'border-brand-green dark:border-brand-green/70',
    markerDot: 'bg-brand-green',
    badge: 'bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30',
    iconBg: 'bg-emerald-50 dark:bg-emerald-950/40 text-brand-green dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-900/20',
    gradientBorder: 'background: linear-gradient(135deg, #10b981 0%, #34d399 50%, #6ee7b7 100%)',
    pointerBorder: 'border-emerald-500',
  },
  {
    markerBorder: 'border-indigo-500 dark:border-indigo-500/70',
    markerDot: 'bg-indigo-500',
    badge: 'bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100/50 dark:border-indigo-900/30',
    iconBg: 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100/30 dark:border-indigo-900/20',
    gradientBorder: 'background: linear-gradient(135deg, #6366f1 0%, #3b82f6 50%, #60a5fa 100%)',
    pointerBorder: 'border-blue-500',
  },
  {
    markerBorder: 'border-amber-500 dark:border-amber-500/70',
    markerDot: 'bg-amber-500',
    badge: 'bg-amber-50/50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-100/30 dark:border-amber-900/30',
    iconBg: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-100/30 dark:border-amber-900/20',
    gradientBorder: 'background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #fb923c 100%)',
    pointerBorder: 'border-amber-500',
  }
]

const getPalette = (idx: number) => {
  return colorPalettes[idx % colorPalettes.length]
}

// Parses location strings to separate parenthesized info like "(Hybrid)"
const getInstLocation = (item: any) => {
  const institution = item.institution || ''
  const location = item.location || ''
  
  if (!location) return { institution, location: '', type: null }
  
  // Look for "(Hybrid)" or similar inside parenthesis
  const parenMatch = location.match(/\(([^)]+)\)/)
  if (parenMatch) {
    const type = parenMatch[0] // e.g. "(Hybrid)"
    const cleanLocation = location.replace(parenMatch[0], '').trim()
    return {
      institution,
      location: cleanLocation,
      type
    }
  }
  
  return {
    institution,
    location,
    type: null
  }
}

// Helper to format dates in Indonesian
const formatEduPeriod = (startStr: string, endStr: string | null) => {
  const monthsIndo = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ]

  const parseDateStr = (dateStr: string) => {
    const [year, month] = dateStr.split('-')
    const monthIdx = parseInt(month, 10) - 1
    return `${monthsIndo[monthIdx]} ${year}`
  }

  const startFormatted = parseDateStr(startStr)
  const endFormatted = endStr ? parseDateStr(endStr) : 'Sekarang'

  return `${startFormatted} – ${endFormatted}`
}

onMounted(() => {
  nextTick(() => {
    if (!$gsap || educations.value.length === 0) return

    // Stagger slide-in each education item
    $gsap.fromTo('.timeline-item', 
      { 
        opacity: 0, 
        x: -30 
      },
      {
        opacity: 1,
        x: 0,
        duration: 0.6,
        stagger: 0.15,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: timelineContainer.value,
          start: 'top 75%',
          toggleActions: 'play none none none'
        }
      }
    )
  })
})
</script>
