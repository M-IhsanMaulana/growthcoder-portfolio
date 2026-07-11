<template>
  <div class="space-y-16 pb-16">
    <!-- Hero Section (Portrait and Biography) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
      <!-- Left Column: Portrait -->
      <div ref="portraitCol" class="lg:col-span-5 flex justify-center lg:justify-start opacity-0 transform translate-x-[-20px]">
        <div class="relative w-full max-w-[340px] aspect-[4/5] rounded-[2rem] overflow-hidden border border-gray-200 dark:border-zinc-800 bg-gray-50 dark:bg-zinc-900 shadow-2xl group flex flex-col">
          <!-- Glowing border effect -->
          <div class="absolute -inset-1.5 bg-gradient-to-r from-brand-purple via-indigo-500 to-brand-green rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-700"></div>
          
          <!-- Floating star badge on top-right -->
          <div class="absolute top-4 right-4 w-7 h-7 rounded-full bg-brand-purple flex items-center justify-center text-white shadow-lg border border-white/10 z-20 transition-transform duration-300 group-hover:scale-110">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
              <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
            </svg>
          </div>

          <!-- Photo container -->
          <div class="relative w-full h-full rounded-[1.9rem] overflow-hidden bg-gray-50 dark:bg-zinc-900 z-10">
            <NuxtImg 
              v-if="settings?.profile_photo?.urls?.medium || settings?.profile_photo?.urls?.original"
              :src="settings.profile_photo.urls.medium || settings.profile_photo.urls.original" 
              :alt="settings?.owner_full_name || 'Muhammad Ihsan Maulana'" 
              class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
            />
            <div v-else class="absolute inset-0 flex items-center justify-center text-4xl font-extrabold text-gray-400">
              {{ initials }}
            </div>
            
            <!-- Floating Badge Overlay -->
            <div class="absolute bottom-4 left-4 right-4 p-4 rounded-2xl glass-panel border border-white/20 dark:border-white/10 shadow-lg bg-white/80 dark:bg-zinc-950/85 flex items-center space-x-3 backdrop-blur-md">
              <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-gradient-to-br from-brand-purple to-indigo-600 flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" class="animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
              </div>
              <div class="min-w-0">
                <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ settings?.owner_title || 'Software Developer' }}</h4>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight truncate">Building digital solutions with clean code.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Biography -->
      <div ref="bioCol" class="lg:col-span-7 space-y-6 opacity-0 transform translate-x-[20px] pt-2">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/30">
          <span class="w-1.5 h-1.5 rounded-full bg-brand-purple animate-pulse"></span>
          <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">TENTANG SAYA</span>
        </div>
        
        <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
          Hai, Saya <span class="bg-gradient-to-r from-brand-purple via-indigo-500 to-brand-green bg-clip-text text-transparent">{{ firstName }}</span> 👋
        </h2>
        <h3 class="text-lg md:text-xl font-medium text-gray-600 dark:text-gray-300">
          {{ settings?.owner_title || 'Software Developer' }}
        </h3>
        
        <div class="w-20 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-sm"></div>
        
        <!-- Narrated Bio -->
        <div 
          v-if="settings?.about_bio"
          class="rich-text-content text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base font-light"
          v-html="settings.about_bio"
        ></div>
        <div v-else class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-light italic">
          Belum ada narasi biografi yang ditambahkan di CMS.
        </div>

        <!-- Horizontal Stats Section under Biography (Modern Card Style) -->
        <div 
          v-if="stats && stats.length" 
          ref="statsBlock" 
          class="flex flex-wrap gap-4 pt-8 border-t border-gray-150 dark:border-zinc-900 opacity-0 transform translate-y-4"
        >
          <div 
            v-for="(stat, idx) in stats" 
            :key="idx" 
            class="flex items-center space-x-4 px-5 py-3 rounded-2xl bg-white dark:bg-zinc-950 border border-gray-150 dark:border-zinc-900 shadow-xs hover:shadow-md hover:border-brand-purple/20 transition-all duration-300 group"
          >
            <!-- Circular Icon container -->
            <div class="flex-shrink-0 w-11 h-11 rounded-full bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center text-brand-purple dark:text-indigo-400">
              <span class="text-xl transform group-hover:scale-110 transition-transform duration-300" v-if="stat.emoji">{{ stat.emoji }}</span>
            </div>
            <!-- Value & Label -->
            <div class="flex flex-col">
              <span class="text-2xl font-black text-gray-900 dark:text-white leading-none mb-0.5">{{ stat.value }}</span>
              <span class="text-[9px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider leading-none">{{ stat.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Cards (My Workflow, Dev Philosophy) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Card 1: My Workflow -->
      <Card 
        ref="cardServices" 
        class="transition-all duration-300 opacity-0 transform translate-y-8 relative overflow-hidden"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !border-t-4 !border-x !border-b !border-t-emerald-400 dark:!border-t-emerald-600 !border-x-transparent !border-b-transparent hover:!border-emerald-500/40 dark:hover:!border-emerald-500/30 hover:!border-t-emerald-500 !rounded-3xl !p-0 shadow-xs relative overflow-hidden transition-all duration-300' },
          body: { class: '!p-6 sm:!p-8' }
        }"
      >
        <template #content>
          <!-- Dot Grid Pattern -->
          <div class="absolute top-4 right-4 w-16 h-12 opacity-15 dark:opacity-5 pointer-events-none" style="background-image: radial-gradient(circle, currentColor 1.2px, transparent 1.2px); background-size: 8px 8px;"></div>

          <div class="flex items-center space-x-3 mb-6 pb-3 border-b border-gray-100 dark:border-zinc-900">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-brand-green shadow-inner">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
              </svg>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">My Workflow</h3>
          </div>
          <div class="space-y-4">
            <!-- Dynamic workflows list -->
            <div 
              v-for="wf in workflows" 
              :key="wf.id" 
              class="flex items-start space-x-4 p-3 rounded-2xl hover:bg-gray-50/80 dark:hover:bg-zinc-900/30 border border-transparent hover:border-gray-100 dark:hover:border-zinc-900/50 transition-all duration-300"
            >
              <!-- Icon Wrapper -->
              <div 
                class="flex-shrink-0 w-10 h-10 rounded-2xl flex items-center justify-center border shadow-xs transition-transform duration-300 hover:scale-105 [&_svg]:w-4.5 [&_svg]:h-4.5"
                :class="[getIconColorClasses(wf.icon).bg, getIconColorClasses(wf.icon).text]"
                v-html="getIconSvg(wf.icon)"
              >
              </div>
              <!-- Text details -->
              <div class="space-y-1 flex-1 min-w-0">
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-200 leading-none mb-1.5">{{ wf.title }}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-light">{{ wf.short_description }}</p>
              </div>
            </div>
            <!-- Fallback or empty -->
            <div v-if="!workflows.length" class="text-xs text-gray-400 italic text-center py-8">
              Belum ada data alur kerja aktif.
            </div>
          </div>
        </template>
      </Card>

      <!-- Card 2: Development Philosophy -->
      <Card 
        ref="cardTech" 
        class="transition-all duration-300 opacity-0 transform translate-y-8 relative overflow-hidden"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !border-t-4 !border-x !border-b !border-t-brand-purple dark:!border-t-indigo-500 !border-x-transparent !border-b-transparent hover:!border-brand-purple/40 dark:hover:!border-brand-purple/30 hover:!border-t-brand-purple !rounded-3xl !p-0 shadow-xs relative overflow-hidden transition-all duration-300' },
          body: { class: '!p-6 sm:!p-8' }
        }"
      >
        <template #content>
          <!-- Dot Grid Pattern -->
          <div class="absolute top-4 right-4 w-16 h-12 opacity-15 dark:opacity-5 pointer-events-none" style="background-image: radial-gradient(circle, currentColor 1.2px, transparent 1.2px); background-size: 8px 8px;"></div>

          <div class="flex items-center space-x-3 mb-6 pb-3 border-b border-gray-100 dark:border-zinc-900">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-brand-purple shadow-inner">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8a5 5 0 100-10 5 5 0 000 10z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12" />
              </svg>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">Development Philosophy</h3>
          </div>
          <div class="space-y-4">
            <!-- Dynamic philosophies list -->
            <div 
              v-for="phil in philosophies" 
              :key="phil.id" 
              class="flex items-start space-x-4 p-3 rounded-2xl hover:bg-gray-50/80 dark:hover:bg-zinc-900/30 border border-transparent hover:border-gray-100 dark:hover:border-zinc-900/50 transition-all duration-300"
            >
              <!-- Icon Wrapper -->
              <div 
                class="flex-shrink-0 w-10 h-10 rounded-2xl flex items-center justify-center border shadow-xs transition-transform duration-300 hover:scale-105 [&_svg]:w-4.5 [&_svg]:h-4.5"
                :class="[getIconColorClasses(phil.icon).bg, getIconColorClasses(phil.icon).text]"
                v-html="getIconSvg(phil.icon)"
              >
              </div>
              <!-- Text details -->
              <div class="space-y-1 flex-1 min-w-0">
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-200 leading-none mb-1.5">{{ phil.title }}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-light">{{ phil.description }}</p>
              </div>
            </div>
            <!-- Fallback or empty -->
            <div v-if="!philosophies.length" class="text-xs text-gray-400 italic text-center py-8">
              Belum ada data filosofi pengembangan aktif.
            </div>
          </div>
        </template>
      </Card>
    </div>

    <!-- Bottom CTA Card (Full Width Banner with Gradient Border Wrapper) -->
    <div ref="ctaCard" class="opacity-0 transform translate-y-8">
      <div class="rounded-[2rem] p-[1.5px] bg-gradient-to-r from-brand-purple/20 via-indigo-500/20 to-brand-green/20 dark:from-brand-purple/40 dark:via-indigo-500/35 dark:to-brand-green/40 hover:from-brand-purple hover:via-indigo-500 hover:to-brand-green transition-all duration-300 shadow-xs">
        <div class="relative overflow-hidden rounded-[1.95rem] bg-white dark:bg-zinc-950 p-6 sm:p-8 group">
          <!-- Subtle gradient background pattern inside -->
          <div class="absolute inset-0 bg-gradient-to-r from-brand-purple/5 via-transparent to-brand-green/5 opacity-50 dark:opacity-30"></div>
          <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-6 justify-between">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1">
              <!-- Icon wrapper -->
              <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-brand-green to-emerald-500 flex items-center justify-center text-white shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              </div>
              <!-- Text content -->
              <div class="space-y-1">
                <h4 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white leading-tight">Ayo bangun sesuatu yang luar biasa bersama!</h4>
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed font-light">Saya terbuka untuk proyek baru, kolaborasi, dan peluang karir yang menantang.</p>
              </div>
            </div>
            <!-- Action button -->
            <div class="flex-shrink-0">
              <Button 
                as="NuxtLink"
                to="/contact" 
                label="Hubungi Saya"
                icon="pi pi-envelope"
                class="!px-6 !py-3.5 !font-bold !rounded-xl !text-xs shadow-md shadow-brand-purple/20 transition-all duration-300 cursor-pointer"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'

definePageMeta({
  layout: 'about'
})

const { settings } = useSettings()
const { $gsap } = useNuxtApp()

useSeoMeta({
  title: 'Tentang Saya',
  description: 'Pelajari lebih lanjut tentang profil profesional, keahlian, filosofi pengembangan, dan statistik karir Muhammad Ihsan Maulana.',
  ogTitle: 'Tentang Saya | growthcoder.id',
  ogDescription: 'Pelajari lebih lanjut tentang profil profesional, keahlian, filosofi pengembangan, dan statistik karir Muhammad Ihsan Maulana.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'profile',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/about' }
  ],
  script: [
    {
      type: 'application/ld+json',
      innerHTML: JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': [
          {
            '@type': 'AboutPage',
            '@id': 'https://growthcoder.id/about/#webpage',
            'url': 'https://growthcoder.id/about',
            'name': 'Tentang Saya - growthcoder.id',
            'description': 'Biografi, workflow, filosofi pengembangan, dan statistik profesional Muhammad Ihsan Maulana.',
            'about': {
              '@id': 'https://growthcoder.id/#person'
            }
          },
          {
            '@type': 'Person',
            '@id': 'https://growthcoder.id/#person',
            'name': settings.value?.owner_full_name || 'Muhammad Ihsan Maulana',
            'jobTitle': settings.value?.owner_title || 'Full-Stack Developer',
            'url': 'https://growthcoder.id',
            'image': settings.value?.profile_photo?.urls?.original || 'https://growthcoder.id/portrait.png',
            'description': settings.value?.about_bio?.replace(/<[^>]*>/g, '') || '',
            'address': {
              '@type': 'PostalAddress',
              'addressLocality': settings.value?.about_location || 'Indonesia'
            },
            'sameAs': [
              settings.value?.social_linkedin,
              settings.value?.social_github,
              settings.value?.social_telegram,
              settings.value?.social_instagram,
              settings.value?.social_twitter
            ].filter(Boolean)
          }
        ]
      })
    }
  ]
})

// GSAP Refs
const portraitCol = ref<any>(null)
const bioCol = ref<any>(null)
const cardServices = ref<any>(null)
const cardTech = ref<any>(null)
const statsBlock = ref<any>(null)
const ctaCard = ref<any>(null)

// Fetch active workflows and development philosophies
const { data: rawWorkflows } = await useFetchAPI<any>('/workflows')
const { data: rawPhilosophies } = await useFetchAPI<any>('/development-philosophies')

const workflows = computed(() => {
  if (rawWorkflows.value && Array.isArray(rawWorkflows.value.data)) {
    return rawWorkflows.value.data.filter((w: any) => w.is_active).slice(0, 4)
  }
  return []
})

const philosophies = computed(() => {
  if (rawPhilosophies.value && Array.isArray(rawPhilosophies.value.data)) {
    return rawPhilosophies.value.data.filter((p: any) => p.is_active).slice(0, 4)
  }
  return []
})

const stats = computed(() => {
  return settings.value?.about_stats || []
})

const firstName = computed(() => {
  const name = settings.value?.owner_full_name || 'Ihsan'
  return name.split(' ')[0]
})

const initials = computed(() => {
  const name = settings.value?.owner_full_name || 'Muhammad Ihsan Maulana'
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
})

// Helper to convert string icon name to Lucide SVG path
const getIconSvg = (iconName: string | null): string => {
  if (!iconName) return ''
  if (iconName.trim().startsWith('<svg')) {
    return iconName
  }
  const mappings: Record<string, string> = {
    Search: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,
    Layers: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-10 5 10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>`,
    Code2: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m18 16 4-4-4-4"/><path d="m6 8-4 4 4 4"/><path d="m14.5 4-5 16"/></svg>`,
    Rocket: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>`,
    Code: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>`,
    ShieldCheck: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>`,
    Sparkles: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="m5 3 1 2.5L8.5 6 6 7 5 9.5 4 7 1.5 6 4 5.5z"/><path d="m19 17 1 2.5 2.5.5-2.5 1-1 2.5-1-2.5-2.5-1 2.5-1z"/></svg>`,
    Star: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`
  }
  
  return mappings[iconName] ?? `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`
}

// Helper to convert string icon name to modern color styling classes
const getIconColorClasses = (iconName: string | null): { bg: string, text: string } => {
  const mappings: Record<string, { bg: string, text: string }> = {
    Search: { bg: 'bg-blue-50 dark:bg-blue-950/40 border-blue-100 dark:border-blue-900/30', text: 'text-blue-600 dark:text-blue-400' },
    Layers: { bg: 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-100 dark:border-emerald-900/30', text: 'text-emerald-600 dark:text-emerald-400' },
    Code2: { bg: 'bg-purple-50 dark:bg-purple-950/40 border-purple-100 dark:border-purple-900/30', text: 'text-purple-600 dark:text-purple-400' },
    Rocket: { bg: 'bg-rose-50 dark:bg-rose-950/40 border-rose-100 dark:border-rose-900/30', text: 'text-rose-600 dark:text-rose-400' },
    Code: { bg: 'bg-sky-50 dark:bg-sky-950/40 border-sky-100 dark:border-sky-900/30', text: 'text-sky-600 dark:text-sky-400' },
    ShieldCheck: { bg: 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-100 dark:border-indigo-900/30', text: 'text-indigo-600 dark:text-indigo-400' },
    Sparkles: { bg: 'bg-amber-50 dark:bg-amber-950/40 border-amber-100 dark:border-amber-900/30', text: 'text-amber-600 dark:text-amber-400' },
    Star: { bg: 'bg-yellow-50 dark:bg-yellow-950/40 border-yellow-100 dark:border-yellow-900/30', text: 'text-yellow-600 dark:text-yellow-400' }
  }
  return mappings[iconName || ''] ?? { bg: 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-100 dark:border-indigo-900/30', text: 'text-brand-purple dark:text-indigo-400' }
}

onMounted(() => {
  if (!$gsap) return

  const tl = $gsap.timeline({ delay: 0.1 })

  tl.to(portraitCol.value, {
    opacity: 1,
    x: 0,
    duration: 0.6,
    ease: 'power3.out'
  })
  .to(bioCol.value, {
    opacity: 1,
    x: 0,
    duration: 0.6,
    ease: 'power3.out'
  }, '-=0.4')
  .to(statsBlock.value, {
    opacity: 1,
    y: 0,
    duration: 0.5,
    ease: 'power3.out'
  }, '-=0.2')
  .to([cardServices.value?.$el, cardTech.value?.$el], {
    opacity: 1,
    y: 0,
    duration: 0.5,
    stagger: 0.1,
    ease: 'power3.out'
  }, '-=0.3')
  .to(ctaCard.value, {
    opacity: 1,
    y: 0,
    duration: 0.5,
    ease: 'power3.out'
  }, '-=0.2')
})
</script>
