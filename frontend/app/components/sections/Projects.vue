<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-zinc-900 overflow-hidden relative">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-6">
      <div class="space-y-4">
        <!-- Badge -->
        <div>
          <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
            Proyek Unggulan
          </span>
        </div>
        <!-- Title & Subtitle -->
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight text-brand-navy dark:text-white leading-[1.2]">
            Proyek Pilihan<br />
            yang Telah <span class="text-blue-purple-gradient">Dibangun</span>
          </h3>
          <p class="text-zinc-500 dark:text-zinc-400 text-sm sm:text-base leading-relaxed mt-2 font-normal">
            Beberapa proyek terbaik yang telah saya kerjakan untuk klien dan produk pribadi.
          </p>
        </div>
      </div>
      
      <!-- Link "Lihat Semua Proyek" -->
      <div class="flex-shrink-0">
        <NuxtLink 
          to="/proyek" 
          class="inline-flex items-center text-sm font-bold text-brand-purple dark:text-indigo-400 hover:text-brand-navy dark:hover:text-white transition-colors group cursor-pointer"
        >
          Lihat Semua Proyek
          <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
          </svg>
        </NuxtLink>
      </div>
    </div>

    <!-- Category Pills Tabs -->
    <div class="flex flex-wrap items-center gap-3 mb-10 overflow-x-auto pb-2 scrollbar-none">
      <!-- All option -->
      <button 
        @click="selectCategory('all')" 
        class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm"
        :class="activeCategory === 'all' 
          ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0' 
          : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/50 dark:border-zinc-800/40 hover:bg-zinc-200/60 dark:hover:bg-zinc-800/70'"
      >
        Semua
      </button>
      
      <!-- Dynamic API categories options -->
      <button 
        v-for="cat in categories" 
        :key="cat.slug"
        @click="selectCategory(cat.slug)" 
        class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm"
        :class="activeCategory === cat.slug 
          ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0' 
          : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/50 dark:border-zinc-800/40 hover:bg-zinc-200/60 dark:hover:bg-zinc-800/70'"
      >
        {{ cat.name }}
      </button>
    </div>

    <!-- Grid -->
    <div 
      v-if="filteredProjects && filteredProjects.length" 
      ref="projectsContainer"
      class="grid md:grid-cols-2 lg:grid-cols-3 gap-8"
    >
      <Card 
        v-for="project in filteredProjects" 
        :key="project.id"
        class="project-card group flex flex-col hover:shadow-md border border-zinc-150/70 dark:border-zinc-900/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 opacity-0 transform translate-y-8"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-3xl !p-0 shadow-sm relative overflow-hidden' },
          body: { class: '!p-6 !flex-grow !flex !flex-col !justify-between min-h-[220px]' }
        }"
      >
        <template #header>
          <!-- Card Cover Image -->
          <div class="aspect-[4/3] w-full bg-gray-100 dark:bg-zinc-900 overflow-hidden relative">
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
            
            <!-- Category Badge Overlay on top-left -->
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
          <!-- Card Footer (Tech badges & Action detail button) -->
          <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-6">
            <!-- Tech badges -->
            <div v-if="project.technologies && project.technologies.length" class="flex flex-wrap gap-2">
              <span 
                v-for="tech in project.technologies.slice(0, 3)" 
                :key="tech.id"
                class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-350 text-[10px] font-semibold rounded-md border border-zinc-150/20 dark:border-zinc-800/10"
              >
                {{ tech.name }}
              </span>
            </div>
            <div v-else></div>

            <!-- Action external link/detail -->
            <NuxtLink 
              :to="`/proyek/${project.slug}`" 
              class="w-8 h-8 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-brand-purple dark:hover:border-brand-green flex items-center justify-center text-zinc-450 hover:text-brand-purple dark:hover:text-brand-green hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-300"
            >
              <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </NuxtLink>
          </div>
        </template>
      </Card>
    </div>

    <!-- Empty state -->
    <div v-else-if="!pending" class="text-center py-16 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
      <span class="text-4xl">📁</span>
      <h4 class="text-sm font-semibold text-zinc-500 mt-4">Belum ada proyek dalam kategori ini</h4>
    </div>

    <!-- Skeleton Loading -->
    <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      <Card 
        v-for="i in 3" 
        :key="i"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm border border-zinc-150/70 dark:border-zinc-900/60' },
          body: { class: '!p-6 min-h-[220px]' }
        }"
      >
        <template #header>
          <Skeleton class="w-full aspect-[4/3] rounded-t-3xl" />
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
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick, computed } from 'vue'

const { $gsap } = useNuxtApp()
const projectsContainer = ref<HTMLElement | null>(null)
const activeCategory = ref<string>('all')

// Fetch projects (all featured ones)
const { data: response, pending } = await useFetchAPI<any>('/projects?featured=1')

// Fetch categories from API
const { data: categoriesResponse } = await useFetchAPI<any>('/project-categories')

// Extract projects
const projects = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data
  }
  return []
})

// Extract categories
const categories = computed(() => {
  if (categoriesResponse.value && Array.isArray(categoriesResponse.value.data)) {
    return categoriesResponse.value.data
  }
  return []
})

// Filter projects dynamically
const filteredProjects = computed(() => {
  const list = projects.value
  if (activeCategory.value === 'all') return list
  return list.filter((p: any) => p.category?.slug === activeCategory.value)
})

// Smooth transition for category filtering using GSAP
const selectCategory = (categorySlug: string) => {
  if (activeCategory.value === categorySlug) return

  if (!$gsap) {
    activeCategory.value = categorySlug
    return
  }

  // Fade out current items
  $gsap.to(".project-card", {
    opacity: 0,
    y: 20,
    duration: 0.25,
    stagger: 0.05,
    onComplete: () => {
      activeCategory.value = categorySlug
      nextTick(() => {
        // Fade in filtered items
        $gsap.fromTo(".project-card", 
          { opacity: 0, y: 20 },
          { opacity: 1, y: 0, duration: 0.4, stagger: 0.08, ease: "power2.out" }
        )
      })
    }
  })
}

const initScrollTrigger = () => {
  if (!$gsap || !projectsContainer.value) return

  $gsap.fromTo(".project-card", 
    { 
      opacity: 0, 
      y: 40
    }, 
    {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0.12,
      ease: "power2.out",
      scrollTrigger: {
        trigger: projectsContainer.value,
        start: "top 80%",
        toggleActions: "play none none none"
      }
    }
  )
}

onMounted(() => {
  if (!pending.value && filteredProjects.value.length > 0) {
    nextTick(() => {
      initScrollTrigger()
    })
  }
})

watch(pending, (isPending) => {
  if (!isPending) {
    nextTick(() => {
      initScrollTrigger()
    })
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
