<template>
  <div class="space-y-12 pb-16">

    <!-- Header Section (Badge, Title, and Quote) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
      <!-- Left Column: Title & Description -->
      <div class="lg:col-span-7 space-y-4 skills-header-left opacity-0 transform translate-y-6">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100/30 dark:border-indigo-900/30 shadow-xs">
          <span class="text-xs shrink-0">💼</span>
          <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">SKILLS & KEAHLIAN</span>
        </div>
        
        <h1 class="text-3xl sm:text-5xl font-extrabold text-brand-navy dark:text-white leading-tight">
          Keahlian <span class="bg-gradient-to-r from-brand-purple via-indigo-500 to-brand-green bg-clip-text text-transparent">Saya</span>
        </h1>
        <div class="w-16 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-xs"></div>
        
        <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base font-light leading-relaxed">
          Berbagai teknologi dan tools yang saya kuasai untuk membangun solusi digital yang modern, efisien, dan scalable.
        </p>
      </div>

      <!-- Right Column: Motivation Card -->
      <div class="lg:col-span-5 skills-header-right opacity-0 transform translate-y-6">
        <div class="rounded-3xl p-[1px] bg-gradient-to-r from-brand-purple/20 via-indigo-500/20 to-brand-green/20 dark:from-brand-purple/40 dark:via-indigo-500/30 dark:to-brand-green/45 shadow-sm">
          <div class="relative overflow-hidden rounded-[1.4rem] bg-white dark:bg-zinc-950 p-6 flex items-start gap-4">
            <div class="absolute inset-0 bg-gradient-to-r from-brand-purple/3 via-transparent to-brand-green/3 opacity-50 dark:opacity-30"></div>
            
            <!-- Icon Container -->
            <div class="flex-shrink-0 w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-500 to-brand-purple flex items-center justify-center text-white shadow-md shadow-indigo-500/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            
            <div class="space-y-1 z-10">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white">Selalu Belajar, Selalu Berkembang</h4>
              <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">
                Saya percaya bahwa teknologi terus berkembang. Saya selalu berusaha memperbarui skill dan mengikuti perkembangan terbaru.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Divider Section: Teknologi & Tools -->
    <div class="skills-section-divider opacity-0 transform translate-y-4">
      <div class="flex items-center space-x-3 pb-2">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-brand-purple dark:text-indigo-400 shadow-inner">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
        </div>
        <h3 class="font-extrabold text-gray-900 dark:text-white uppercase tracking-wider text-xs sm:text-sm">Teknologi & Tools</h3>
      </div>
      <!-- Gradient divider line -->
      <div class="h-px bg-gradient-to-r from-brand-purple/40 via-indigo-400/30 to-brand-green/30 dark:from-brand-purple/60 dark:via-indigo-400/50 dark:to-brand-green/50"></div>
    </div>

    <!-- Skills Grid -->
    <div v-if="skills && skills.length" class="space-y-6">
      <!-- Top Grid: Technical Skills (Frontend, Backend, Database, DevOps) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Gradient border wrapper for each technical skill card -->
        <div 
          v-for="skillGroup in technicalSkills" 
          :key="skillGroup.id"
          class="skills-category-card opacity-0 transform translate-y-8 transition-all duration-300 rounded-3xl p-[1px] shadow-xs hover:shadow-md"
          :class="getCategoryStyles(skillGroup.name).gradientBorder"
        >
          <Card 
            :pt="{
              root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-[1.4rem] transition-all duration-300 h-full' },
              body: { class: '!p-6 !h-full !flex !flex-col' }
            }"
          >
            <template #content>
              <div class="space-y-4 flex-1">
                <!-- Header info of Category -->
                <div class="flex items-center space-x-3 mb-1">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="getCategoryStyles(skillGroup.name).iconContainer">
                    <span v-html="getCategoryStyles(skillGroup.name).iconSvg" class="w-5 h-5 flex items-center justify-center [&_svg]:w-5 [&_svg]:h-5"></span>
                  </div>
                  <h3 class="font-extrabold text-sm sm:text-base text-gray-900 dark:text-white leading-tight">
                    {{ skillGroup.name }}
                  </h3>
                </div>

                <!-- Description -->
                <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">
                  {{ getCategoryDescription(skillGroup.name) }}
                </p>

                <!-- Tags container -->
                <div class="flex flex-wrap gap-2 pt-2">
                  <div 
                    v-for="item in skillGroup.items" 
                    :key="item.id"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border bg-gray-50/50 dark:bg-zinc-900/40 hover:bg-white dark:hover:bg-zinc-900 transition-all duration-200 shadow-3xs"
                    :class="getCategoryStyles(skillGroup.name).tagBorder"
                  >
                    <NuxtImg
                      v-if="item.technology?.logo?.urls?.medium || item.technology?.logo?.urls?.original"
                      :src="item.technology.logo.urls.medium || item.technology.logo.urls.original"
                      :alt="item.display_name"
                      class="w-4 h-4 object-contain"
                    />
                    <span v-else class="w-4 h-4 rounded-md bg-indigo-50 dark:bg-indigo-950/40 text-[9px] font-extrabold text-brand-purple dark:text-indigo-400 flex items-center justify-center uppercase">
                      {{ item.display_name.substring(0, 2) }}
                    </span>
                    <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 leading-none">
                      {{ item.display_name }}
                    </span>
                  </div>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </div>

      <!-- Bottom Grid: Other and Soft Skills (Pills, No Icons) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gradient border wrapper for each non-technical skill card -->
        <div 
          v-for="skillGroup in nonTechnicalSkills" 
          :key="skillGroup.id"
          class="skills-category-card opacity-0 transform translate-y-8 transition-all duration-300 rounded-3xl p-[1px] shadow-xs hover:shadow-md"
          :class="getCategoryStyles(skillGroup.name).gradientBorder"
        >
          <Card 
            :pt="{
              root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-[1.4rem] transition-all duration-300 h-full' },
              body: { class: '!p-6 !h-full !flex !flex-col' }
            }"
          >
            <template #content>
              <div class="space-y-4 flex-1">
                <!-- Header info of Category -->
                <div class="flex items-center space-x-3 mb-1">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="getCategoryStyles(skillGroup.name).iconContainer">
                    <span v-html="getCategoryStyles(skillGroup.name).iconSvg" class="w-5 h-5 flex items-center justify-center [&_svg]:w-5 [&_svg]:h-5"></span>
                  </div>
                  <h3 class="font-extrabold text-sm sm:text-base text-gray-900 dark:text-white leading-tight">
                    {{ skillGroup.name }}
                  </h3>
                </div>

                <!-- Description -->
                <p class="text-xs text-gray-500 dark:text-gray-400 font-light leading-relaxed">
                  {{ getCategoryDescription(skillGroup.name) }}
                </p>

                <!-- Tags container -->
                <div class="flex flex-wrap gap-2 pt-2">
                  <span 
                    v-for="item in skillGroup.items" 
                    :key="item.id"
                    class="text-[11px] px-3 py-1.5 rounded-xl shadow-3xs"
                    :class="getCategoryStyles(skillGroup.name).pillClass"
                  >
                    {{ item.display_name }}
                  </span>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16 bg-white dark:bg-zinc-950 border border-gray-150 dark:border-zinc-900 rounded-3xl p-8">
      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" class="text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
      <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada data keahlian aktif di CMS.</p>
    </div>

    <!-- Bottom Section: Contribution quote & Dynamic Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
      <!-- Left side: Continue Learning banner -->
      <div class="lg:col-span-6 skills-bottom-left opacity-0 transform translate-y-8 flex">
        <div class="rounded-3xl p-[1px] bg-gradient-to-r from-brand-purple/20 via-indigo-500/20 to-brand-green/20 dark:from-brand-purple/40 dark:via-indigo-500/35 dark:to-brand-green/40 shadow-xs flex-1">
          <div class="relative overflow-hidden rounded-[1.4rem] bg-white dark:bg-zinc-950 p-6 sm:p-8 flex flex-col sm:flex-row items-start gap-4 h-full">
            <div class="absolute inset-0 bg-gradient-to-r from-brand-purple/4 via-transparent to-brand-green/4 opacity-50 dark:opacity-30"></div>
            
            <!-- Rocket Icon (inline SVG) -->
            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-brand-purple flex items-center justify-center text-white shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
              </svg>
            </div>
            
            <div class="space-y-1.5 z-10">
              <h4 class="text-base font-extrabold text-gray-900 dark:text-white">Terus Belajar & Berkontribusi</h4>
              <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed">
                Saya senang mempelajari hal baru dan menantang diri dengan teknologi terbaru untuk memberikan hasil terbaik di setiap proyek yang dikerjakan.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right side: 3 Stats Cards -->
      <div class="lg:col-span-6 grid grid-cols-3 gap-4">
        <!-- Stat Card 1: Technologies - gradient border indigo/purple -->
        <div class="skills-stat-card opacity-0 transform translate-y-8 rounded-3xl p-[1px] shadow-xs hover:shadow-md transition-all duration-300 bg-gradient-to-br from-indigo-300/40 via-brand-purple/20 to-indigo-500/30 dark:from-indigo-500/50 dark:via-brand-purple/40 dark:to-indigo-400/50 group">
          <div class="bg-white dark:bg-zinc-950 rounded-[1.4rem] p-5 sm:p-6 flex flex-col justify-between h-full">
            <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-brand-purple dark:text-indigo-400 flex items-center justify-center">
              <i class="pi pi-book text-sm transition-transform duration-300 group-hover:scale-115"></i>
            </div>
            <div class="mt-4 space-y-0.5">
              <h5 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white leading-none">
                {{ statsData.technologies }}+
              </h5>
              <p class="text-[9px] sm:text-[10px] text-gray-450 dark:text-gray-500 font-bold uppercase tracking-wider leading-tight">
                Teknologi Dipelajari
              </p>
            </div>
          </div>
        </div>

        <!-- Stat Card 2: Projects - gradient border blue -->
        <div class="skills-stat-card opacity-0 transform translate-y-8 rounded-3xl p-[1px] shadow-xs hover:shadow-md transition-all duration-300 bg-gradient-to-br from-blue-300/40 via-blue-500/20 to-indigo-400/30 dark:from-blue-500/50 dark:via-blue-400/40 dark:to-indigo-500/50 group">
          <div class="bg-white dark:bg-zinc-950 rounded-[1.4rem] p-5 sm:p-6 flex flex-col justify-between h-full">
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
              <i class="pi pi-code text-sm transition-transform duration-300 group-hover:scale-115"></i>
            </div>
            <div class="mt-4 space-y-0.5">
              <h5 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white leading-none">
                {{ statsData.projects }}+
              </h5>
              <p class="text-[9px] sm:text-[10px] text-gray-450 dark:text-gray-500 font-bold uppercase tracking-wider leading-tight">
                Proyek Dikerjakan
              </p>
            </div>
          </div>
        </div>

        <!-- Stat Card 3: Experience - gradient border amber/orange -->
        <div class="skills-stat-card opacity-0 transform translate-y-8 rounded-3xl p-[1px] shadow-xs hover:shadow-md transition-all duration-300 bg-gradient-to-br from-amber-300/40 via-orange-400/20 to-amber-500/30 dark:from-amber-500/50 dark:via-orange-400/40 dark:to-amber-400/50 group">
          <div class="bg-white dark:bg-zinc-950 rounded-[1.4rem] p-5 sm:p-6 flex flex-col justify-between h-full">
            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
              <i class="pi pi-trophy text-sm transition-transform duration-300 group-hover:scale-115"></i>
            </div>
            <div class="mt-4 space-y-0.5">
              <h5 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white leading-none">
                {{ statsData.experience }}+
              </h5>
              <p class="text-[9px] sm:text-[10px] text-gray-450 dark:text-gray-500 font-bold uppercase tracking-wider leading-tight">
                Tahun Pengalaman
              </p>
            </div>
          </div>
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
  title: 'Keahlian & Teknologi',
  description: 'Daftar keahlian teknis, database, devops, tools, dan soft skills yang dikuasai oleh Muhammad Ihsan Maulana.',
  ogTitle: 'Keahlian & Teknologi | growthcoder.id',
  ogDescription: 'Daftar keahlian teknis, database, devops, tools, dan soft skills yang dikuasai oleh Muhammad Ihsan Maulana.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/about/skills' }
  ]
})

// Fetch skills from API
const { data: response } = await useFetchAPI<any>('/skills')

// Fetch statistics from dynamic API
const { data: statsResponse } = await useFetchAPI<any>('/stats')

// Compute categories and split them into technical vs non-technical
const skills = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data
  }
  return []
})

const technicalSkills = computed(() => {
  return skills.value.filter((group: any) => {
    const name = group.name.toLowerCase()
    return name.includes('frontend') || name.includes('backend') || name.includes('database') || name.includes('devops')
  })
})

const nonTechnicalSkills = computed(() => {
  return skills.value.filter((group: any) => {
    const name = group.name.toLowerCase()
    return !name.includes('frontend') && !name.includes('backend') && !name.includes('database') && !name.includes('devops')
  })
})

// Dynamic stats computed
const statsData = computed(() => {
  return {
    technologies: statsResponse.value?.data?.technologies_count ?? 50,
    projects: statsResponse.value?.data?.projects_count ?? 10,
    experience: statsResponse.value?.data?.years_of_experience ?? 3
  }
})

// Mappings for Card gradient border, category icon container, and pill style classes
const getCategoryStyles = (name: string) => {
  const normalized = name.toLowerCase().trim()
  if (normalized.includes('frontend')) {
    return {
      // Light: subtle indigo-to-purple gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-indigo-200/60 via-brand-purple/20 to-indigo-300/40 dark:from-indigo-500/50 dark:via-brand-purple/40 dark:to-indigo-400/50',
      iconContainer: 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-100/30 dark:border-indigo-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>`,
      tagBorder: 'border-indigo-200/70 dark:border-indigo-800/40 hover:border-indigo-400/50 dark:hover:border-indigo-600/50',
      pillClass: 'bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 border border-indigo-100/50 dark:border-indigo-900/30'
    }
  }
  if (normalized.includes('backend')) {
    return {
      // Light: subtle emerald gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-emerald-200/60 via-brand-green/20 to-emerald-300/40 dark:from-emerald-500/50 dark:via-brand-green/40 dark:to-emerald-400/50',
      iconContainer: 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100/30 dark:border-emerald-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>`,
      tagBorder: 'border-emerald-200/70 dark:border-emerald-800/40 hover:border-emerald-400/50 dark:hover:border-emerald-600/50',
      pillClass: 'bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 border border-emerald-100/50 dark:border-emerald-900/30'
    }
  }
  if (normalized.includes('database') || normalized.includes('storage')) {
    return {
      // Light: subtle purple gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-purple-200/60 via-violet-400/20 to-purple-300/40 dark:from-purple-500/50 dark:via-violet-400/40 dark:to-purple-400/50',
      iconContainer: 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-100/30 dark:border-purple-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>`,
      tagBorder: 'border-purple-200/70 dark:border-purple-800/40 hover:border-purple-400/50 dark:hover:border-purple-600/50',
      pillClass: 'bg-purple-50/50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-300 border border-purple-100/50 dark:border-purple-900/30'
    }
  }
  if (normalized.includes('devops') || normalized.includes('tools')) {
    return {
      // Light: subtle teal gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-teal-200/60 via-cyan-400/20 to-teal-300/40 dark:from-teal-500/50 dark:via-cyan-400/40 dark:to-teal-400/50',
      iconContainer: 'bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 border border-teal-100/30 dark:border-teal-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" /></svg>`,
      tagBorder: 'border-teal-200/70 dark:border-teal-800/40 hover:border-teal-400/50 dark:hover:border-teal-600/50',
      pillClass: 'bg-teal-50/50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-300 border border-teal-100/50 dark:border-teal-900/30'
    }
  }
  if (normalized.includes('other')) {
    return {
      // Light: subtle amber gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-amber-200/60 via-orange-300/20 to-amber-300/40 dark:from-amber-500/50 dark:via-orange-400/40 dark:to-amber-400/50',
      iconContainer: 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-100/30 dark:border-amber-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" /></svg>`,
      tagBorder: 'border-amber-200/70 dark:border-amber-800/40 hover:border-amber-400/50 dark:hover:border-amber-600/50',
      pillClass: 'bg-amber-50/60 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-200/50 dark:border-amber-900/30 font-semibold'
    }
  }
  if (normalized.includes('soft')) {
    return {
      // Light: subtle pink gradient; Dark: more vivid
      gradientBorder: 'bg-gradient-to-br from-pink-200/60 via-rose-300/20 to-pink-300/40 dark:from-pink-500/50 dark:via-rose-400/40 dark:to-pink-400/50',
      iconContainer: 'bg-pink-50 dark:bg-pink-950/40 text-pink-600 dark:text-pink-400 border border-pink-100/30 dark:border-pink-900/20',
      iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`,
      tagBorder: 'border-pink-200/70 dark:border-pink-800/40 hover:border-pink-400/50 dark:hover:border-pink-600/50',
      pillClass: 'bg-pink-50/60 dark:bg-pink-950/20 text-pink-600 dark:text-pink-450 border border-pink-200/50 dark:border-pink-900/30 font-semibold'
    }
  }
  return {
    gradientBorder: 'bg-gradient-to-br from-indigo-200/60 via-brand-purple/20 to-indigo-300/40 dark:from-indigo-500/50 dark:via-brand-purple/40 dark:to-indigo-400/50',
    iconContainer: 'bg-indigo-50 dark:bg-indigo-950/40 text-brand-purple dark:text-indigo-400 border border-indigo-100/30 dark:border-indigo-900/20',
    iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>`,
    tagBorder: 'border-indigo-200/70 dark:border-indigo-800/40 hover:border-indigo-400/50 dark:hover:border-indigo-600/50',
    pillClass: 'bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 border border-indigo-100/50 dark:border-indigo-900/30'
  }
}

// Mappings for description text as they aren't stored in basic skills table
const getCategoryDescription = (name: string): string => {
  const normalized = name.toLowerCase().trim()
  if (normalized.includes('frontend')) {
    return 'Membangun antarmuka yang responsif, interaktif, dan user-friendly.'
  }
  if (normalized.includes('backend')) {
    return 'Membangun sistem backend yang aman, efisien, dan terstruktur.'
  }
  if (normalized.includes('database')) {
    return 'Mengelola data dengan struktur yang optimal dan aman.'
  }
  if (normalized.includes('devops')) {
    return 'Tools untuk pengembangan, deployment, dan manajemen sistem.'
  }
  if (normalized.includes('other')) {
    return 'Kemampuan tambahan yang mendukung kualitas pekerjaan dan kolaborasi tim.'
  }
  if (normalized.includes('soft')) {
    return 'Kemampuan non-teknis yang membantu komunikasi, manajemen, dan kerja tim.'
  }
  return 'Keahlian dan teknologi yang saya kuasai.'
}

onMounted(() => {
  nextTick(() => {
    if (!$gsap) return

    // Run GSAP Entrance Animations
    const tl = $gsap.timeline({ delay: 0.15 })

    tl.to('.skills-header-left', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      ease: 'power3.out'
    })
    .to('.skills-header-right', {
      opacity: 1,
      y: 0,
      duration: 0.6,
      ease: 'power3.out'
    }, '-=0.4')
    .to('.skills-section-divider', {
      opacity: 1,
      y: 0,
      duration: 0.4,
      ease: 'power3.out'
    }, '-=0.3')
    .to('.skills-category-card', {
      opacity: 1,
      y: 0,
      duration: 0.5,
      stagger: 0.08,
      ease: 'power3.out'
    }, '-=0.2')
    .to('.skills-bottom-left', {
      opacity: 1,
      y: 0,
      duration: 0.5,
      ease: 'power3.out'
    }, '-=0.3')
    .to('.skills-stat-card', {
      opacity: 1,
      y: 0,
      duration: 0.5,
      stagger: 0.06,
      ease: 'power3.out'
    }, '-=0.3')
  })
})
</script>
