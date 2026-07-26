<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 pb-20">

    <!-- ─── HERO & STATS SECTION ───────────────────────────────────────── -->
    <div ref="heroSection" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10 opacity-0 -translate-y-3">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <!-- Left: Text content -->
        <div class="lg:col-span-8 space-y-5">
          <!-- Badge -->
          <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/30 shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-brand-purple animate-pulse"></span>
            <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">PROYEK SAYA</span>
          </div>

          <!-- Heading -->
          <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] text-zinc-900 dark:text-white">
            Proyek yang <br class="hidden sm:inline" />
            Telah <span class="bg-gradient-to-r from-brand-purple to-brand-green bg-clip-text text-transparent">Dikerjakan</span>
          </h1>

          <!-- Divider -->
          <div class="w-20 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-sm"></div>

          <!-- Subtitle -->
          <p class="text-sm md:text-base text-zinc-550 dark:text-zinc-400 leading-relaxed font-light max-w-xl">
            Berbagai proyek yang telah saya bangun dengan sepenuh hati menggunakan teknologi modern dan best practices.
          </p>
        </div>

        <!-- Right: Stats Card -->
        <div class="lg:col-span-4 flex justify-start lg:justify-end">
          <div class="w-full max-w-md p-6 rounded-3xl glass-card-premium border border-zinc-200/50 dark:border-zinc-800/40 shadow-md relative overflow-hidden flex items-center space-x-6">
            <!-- Icon -->
            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-brand-purple/10 flex items-center justify-center text-brand-purple dark:text-indigo-400 animate-float-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
            </div>
            
            <!-- Statistics info split -->
            <div class="flex-grow flex items-center justify-between">
              <div class="flex flex-col">
                <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none">
                  {{ stats?.projects_count || '0' }}+
                </span>
                <span class="text-[9px] text-zinc-400 dark:text-zinc-550 dark:text-zinc-500 font-bold uppercase tracking-wider mt-2.5 leading-none">Proyek Selesai</span>
              </div>
              
              <!-- Vertical Divider line -->
              <div class="h-8 w-px bg-zinc-200 dark:bg-zinc-800 mx-4"></div>

              <div class="flex flex-col flex-1 pl-2">
                <span class="text-2xl font-black text-zinc-900 dark:text-white leading-none">
                  {{ stats?.years_of_experience || '0' }}+
                </span>
                <span class="text-[9px] text-zinc-400 dark:text-zinc-550 dark:text-zinc-500 font-bold uppercase tracking-wider mt-2.5 leading-none">Thn Pengalaman</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── FILTER & SEARCH BAR SECTION ────────────────────────────────── -->
    <div ref="filterSection" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 opacity-0 translate-y-3 relative z-30">
      <div class="flex flex-col lg:flex-row gap-6 lg:items-center justify-between pb-6 border-b border-zinc-200/50 dark:border-zinc-800/40">
        <!-- Dynamic Category Pills (Horizontal Scrollable) -->
        <div class="flex flex-nowrap items-center gap-2.5 overflow-x-auto pb-3 scrollbar-none w-full lg:w-auto flex-1 lg:flex-none pr-4">
          <button 
            @click="selectCategory('all')" 
            class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm whitespace-nowrap"
            :class="activeCategory === 'all' 
              ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0' 
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800/80 hover:bg-zinc-100 dark:hover:bg-zinc-850'"
          >
            Semua
          </button>
          <button 
            v-for="cat in categories" 
            :key="cat.slug"
            @click="selectCategory(cat.slug)" 
            class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm whitespace-nowrap"
            :class="activeCategory === cat.slug 
              ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0' 
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800/80 hover:bg-zinc-100 dark:hover:bg-zinc-850'"
          >
            {{ cat.name }}
          </button>
        </div>

        <!-- Search and Sort controls -->
        <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end flex-shrink-0">
          <!-- Search box -->
          <div class="relative w-full sm:w-60 lg:w-64 flex-grow sm:flex-grow-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-400">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
              </svg>
            </span>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari proyek..." 
              class="w-full !pl-10 !pr-4 !py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-brand-purple focus:ring-1 focus:ring-brand-purple/30 transition-all duration-300 shadow-xs"
            />
          </div>

          <!-- Sort selection dropdown -->
          <UiSelectFilter 
            v-model="sortBy" 
            :options="sortOptions" 
            class="w-36 sm:w-40 flex-shrink-0"
          />
        </div>
      </div>
    </div>

    <!-- ─── PROJECTS GRID ──────────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      
      <!-- Loaded projects grid -->
      <div 
        v-if="!pending && processedProjects.length" 
        ref="projectsContainer"
        class="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
      >
        <Card 
          v-for="project in processedProjects" 
          :key="project.id"
          class="project-card group flex flex-col hover:shadow-md border border-zinc-150/70 dark:border-zinc-900/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 relative overflow-hidden"
          :pt="{
            root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-3xl !p-0 shadow-sm relative overflow-hidden' },
            body: { class: '!p-6 !flex-grow !flex !flex-col !justify-between min-h-[220px]' }
          }"
        >
          <template #header>
            <!-- Cover image -->
            <div class="aspect-video w-full bg-gray-100 dark:bg-zinc-900 overflow-hidden relative">
              <NuxtImg 
                v-if="project.cover_image?.urls?.medium || project.cover_image?.urls?.original"
                :src="project.cover_image.urls.medium || project.cover_image.urls.original" 
                :alt="project.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />
              <div v-else class="w-full h-full bg-gradient-to-br from-brand-navy/10 to-brand-purple/20 flex items-center justify-center text-4xl select-none group-hover:scale-105 transition-transform duration-500">
                💻
              </div>
              
              <!-- Category tag overlay -->
              <span 
                v-if="project.category?.name" 
                class="absolute top-4 left-4 px-3 py-1.5 bg-zinc-950/75 backdrop-blur-md border border-white/10 rounded-lg text-[10px] font-bold tracking-wider text-white"
              >
                {{ project.category.name }}
              </span>
            </div>
          </template>
          
          <template #content>
            <div class="space-y-2 flex-grow">
              <!-- Title -->
              <h4 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors">
                <NuxtLink :to="`/proyek/${project.slug}`" class="cursor-pointer">
                  {{ project.title }}
                </NuxtLink>
              </h4>
              <!-- Description -->
              <p class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-sm leading-relaxed line-clamp-2 font-normal">
                {{ project.short_description }}
              </p>
            </div>
          </template>

          <template #footer>
            <!-- Card footer: tech stacks & external links -->
            <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-6">
              <!-- Tech Badges -->
              <div v-if="project.technologies && project.technologies.length" class="flex flex-wrap gap-1.5 max-w-[70%]">
                <span 
                  v-for="tech in project.technologies.slice(0, 3)" 
                  :key="tech.id"
                  class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-350 text-[10px] font-semibold rounded-md border border-zinc-150/20 dark:border-zinc-800/10 flex items-center gap-1.5"
                >
                  <NuxtImg
                    v-if="tech.logo?.urls?.thumbnail || tech.logo?.urls?.medium || tech.logo?.urls?.original"
                    :src="tech.logo?.urls?.thumbnail || tech.logo?.urls?.medium || tech.logo?.urls?.original"
                    :alt="tech.name"
                    class="w-3.5 h-3.5 object-contain"
                  />
                  {{ tech.name }}
                </span>
              </div>
              <div v-else></div>

              <!-- Action buttons -->
              <div class="flex items-center space-x-2">
                <!-- Project External Demo Link -->
                <a 
                  v-if="project.live_url || project.github_url || project.telegram_url"
                  :href="project.live_url || project.github_url || project.telegram_url"
                  target="_blank"
                  class="w-8 h-8 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-600 flex items-center justify-center text-zinc-450 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-300"
                  title="Link eksternal"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                  </svg>
                </a>
                
                <!-- View Detail Link -->
                <NuxtLink 
                  :to="`/proyek/${project.slug}`" 
                  class="w-8 h-8 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-brand-purple dark:hover:border-brand-green flex items-center justify-center text-zinc-450 hover:text-brand-purple dark:hover:text-brand-green hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-300"
                  title="Lihat detail proyek"
                >
                  <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>
                </NuxtLink>
              </div>
            </div>
          </template>
        </Card>
      </div>

      <!-- Pending / Skeleton Loading State -->
      <div v-else-if="pending" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card 
          v-for="i in 3" 
          :key="i"
          :pt="{
            root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm border border-zinc-150/70 dark:border-zinc-900/60' },
            body: { class: '!p-6 min-h-[220px]' }
          }"
        >
          <template #header>
            <Skeleton class="w-full aspect-video rounded-t-3xl" />
          </template>
          
          <template #content>
            <Skeleton width="70%" height="1.5rem" class="mb-3" />
            <div class="space-y-2 mb-6">
              <Skeleton width="100%" height="0.875rem" />
              <Skeleton width="85%" height="0.875rem" />
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-6">
              <div class="flex gap-2">
                <Skeleton width="3rem" height="1.25rem" />
                <Skeleton width="4rem" height="1.25rem" />
              </div>
              <Skeleton shape="circle" size="2rem" />
            </div>
          </template>
        </Card>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-20 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
        <span class="text-5xl">📁</span>
        <h4 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-4">Proyek tidak ditemukan</h4>
        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 max-w-md mx-auto">
          Tidak ada hasil untuk filter "{{ activeCategory === 'all' ? 'Semua' : activeCategory }}" atau pencarian "{{ searchQuery }}".
        </p>
      </div>
    </section>

    <!-- ─── CTA BANNER SECTION ─────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
      <div
        ref="ctaSection"
        class="opacity-0 translate-y-6 rounded-[2rem] p-[1.5px] bg-gradient-to-r from-brand-purple/20 via-indigo-500/20 to-brand-green/20 dark:from-brand-purple/40 dark:via-indigo-500/35 dark:to-brand-green/40 hover:from-brand-purple hover:via-indigo-500 hover:to-brand-green transition-all duration-300 shadow-xs"
      >
        <div class="relative overflow-hidden rounded-[1.95rem] bg-white dark:bg-zinc-950 p-6 sm:p-8 group">
          <!-- Subtle gradient background pattern inside -->
          <div class="absolute inset-0 bg-gradient-to-r from-brand-purple/5 via-transparent to-brand-green/5 opacity-50 dark:opacity-30"></div>
          <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-6 justify-between">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1">
              <!-- Icon wrapper -->
              <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-brand-green to-emerald-500 flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send">
                  <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
              </div>
              <!-- Text content -->
              <div class="space-y-1">
                <h4 class="text-base sm:text-lg font-extrabold text-zinc-900 dark:text-white leading-tight">Ada proyek yang ingin diwujudkan?</h4>
                <p class="text-xs sm:text-sm text-zinc-550 dark:text-zinc-400 leading-relaxed font-light">Saya terbuka untuk diskusi, kolaborasi, atau peluang kerja baru yang menantang.</p>
              </div>
            </div>
            <!-- Action button -->
            <div class="flex-shrink-0">
              <Button 
                as="router-link"
                to="/contact" 
                label="Hubungi Saya"
                icon="pi pi-arrow-up-right"
                iconPos="right"
                class="!px-6 !py-3.5 !font-bold !rounded-xl !text-xs shadow-md shadow-brand-purple/20 transition-all duration-300 cursor-pointer !text-white"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import UiSelectFilter from '~/components/ui/SelectFilter.vue'

definePageMeta({ layout: 'default' })

// Site settings
const { settings, fetchSettings } = useSettings()
await fetchSettings()

useSeoMeta({
  title: 'Portofolio Proyek',
  description: 'Tinjau daftar studi kasus proyek, open source, dan solusi digital yang telah dibangun oleh Muhammad Ihsan Maulana.',
  ogTitle: 'Portofolio Proyek | growthcoder.id',
  ogDescription: 'Tinjau daftar studi kasus proyek, open source, dan solusi digital yang telah dibangun oleh Muhammad Ihsan Maulana.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/proyek' }
  ]
})

// Data fetching from API
const { data: statsResponse } = await useFetchAPI<any>('/stats')
const { data: categoriesResponse } = await useFetchAPI<any>('/project-categories')
const { data: projectsResponse, pending } = await useFetchAPI<any>('/projects')

// Refs & Options
const activeCategory = ref<string>('all')
const searchQuery = ref<string>('')
const sortBy = ref<string>('latest')

const sortOptions = [
  { label: 'Terbaru', value: 'latest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Custom Urutan', value: 'order' }
]

const projectsContainer = ref<HTMLElement | null>(null)
const heroSection = ref<HTMLElement | null>(null)
const filterSection = ref<HTMLElement | null>(null)
const ctaSection = ref<HTMLElement | null>(null)

const { $gsap } = useNuxtApp()

// Compute statistics
const stats = computed(() => statsResponse.value?.data || null)

// Compute categories list
const categories = computed(() => categoriesResponse.value?.data || [])

// Compute raw projects list
const projects = computed(() => projectsResponse.value?.data || [])

// Filter and sort projects
const processedProjects = computed(() => {
  let list = [...projects.value]

  // 1. Filter by category
  if (activeCategory.value !== 'all') {
    list = list.filter((p: any) => p.category?.slug === activeCategory.value)
  }

  // 2. Filter by search query
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter((p: any) => {
      const matchTitle = p.title.toLowerCase().includes(q)
      const matchDesc = p.short_description.toLowerCase().includes(q)
      const matchTech = p.technologies?.some((t: any) => t.name.toLowerCase().includes(q))
      return matchTitle || matchDesc || matchTech
    })
  }

  // 3. Sort logic
  if (sortBy.value === 'latest') {
    list.sort((a, b) => {
      const dateA = a.published_at ? new Date(a.published_at).getTime() : 0
      const dateB = b.published_at ? new Date(b.published_at).getTime() : 0
      return dateB - dateA
    })
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => {
      const dateA = a.published_at ? new Date(a.published_at).getTime() : 0
      const dateB = b.published_at ? new Date(b.published_at).getTime() : 0
      return dateA - dateB
    })
  } else if (sortBy.value === 'order') {
    list.sort((a, b) => (a.order || 0) - (b.order || 0))
  }

  return list
})

// GSAP staggered filter transitions
const selectCategory = (categorySlug: string) => {
  if (activeCategory.value === categorySlug) return

  if (!$gsap) {
    activeCategory.value = categorySlug
    return
  }

  $gsap.to(".project-card", {
    opacity: 0,
    y: 20,
    duration: 0.25,
    stagger: 0.05,
    onComplete: () => {
      activeCategory.value = categorySlug
      nextTick(() => {
        $gsap.fromTo(".project-card", 
          { opacity: 0, y: 20 },
          { opacity: 1, y: 0, duration: 0.4, stagger: 0.08, ease: "power2.out" }
        )
      })
    }
  })
}

// Entrance Animations setup
const initAnimations = () => {
  if (!$gsap) return

  if (heroSection.value) {
    $gsap.to(heroSection.value, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' })
  }

  if (filterSection.value) {
    $gsap.to(filterSection.value, { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out', delay: 0.15 })
  }

  if (projectsContainer.value) {
    $gsap.fromTo(".project-card", 
      { opacity: 0, y: 40 },
      {
        opacity: 1,
        y: 0,
        duration: 0.6,
        stagger: 0.1,
        ease: "power2.out",
        scrollTrigger: {
          trigger: projectsContainer.value,
          start: "top 85%",
          toggleActions: "play none none none"
        }
      }
    )
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

<style scoped>
/* Custom hide scrollbars helper */
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
