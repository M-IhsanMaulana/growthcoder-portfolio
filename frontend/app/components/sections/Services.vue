<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-zinc-900 overflow-hidden relative">
    <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-start">
      <!-- Left Column: Info & Features -->
      <div class="lg:col-span-5 space-y-8 lg:sticky lg:top-24">
        <!-- Badge -->
        <div>
          <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
            Layanan Saya
          </span>
        </div>

        <!-- Title & Subheadline -->
        <div class="space-y-4">
          <h3 class="text-4xl font-extrabold tracking-tight text-brand-navy dark:text-white leading-[1.2]">
            Solusi Digital yang<br />
            <span class="text-blue-purple-gradient">Saya Tawarkan</span>
          </h3>
          <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base leading-relaxed font-normal">
            Saya membantu individu dan bisnis membangun produk digital yang modern, cepat, aman, dan skalabel.
          </p>
        </div>

        <!-- Features list -->
        <div class="space-y-6">
          <!-- Feature 1: Cepat & Efisien -->
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
              <!-- Lightning Bolt SVG -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div>
              <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Cepat &amp; Efisien</h4>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Proses pengembangan yang terstruktur dan efisien.</p>
            </div>
          </div>

          <!-- Feature 2: Aman & Terpercaya -->
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
              <!-- Shield SVG -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Aman &amp; Terpercaya</h4>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Keamanan data dan performa menjadi prioritas utama.</p>
            </div>
          </div>

          <!-- Feature 3: Scalable -->
          <div class="flex gap-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
              <!-- Arrow Trending Up SVG -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <div>
              <h4 class="text-sm font-bold text-zinc-900 dark:text-white">Scalable</h4>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Dibangun dengan arsitektur yang siap berkembang.</p>
            </div>
          </div>
        </div>

        <!-- Button -->
        <div class="pt-2">
          <Button 
            as="router-link"
            to="/services" 
            label="Lihat Semua Layanan"
            class="!px-6 !py-3 !font-semibold !rounded-xl bg-zinc-950 dark:bg-zinc-800 hover:bg-zinc-900 dark:hover:bg-zinc-700 !text-white !border-0 transition-all duration-300 hover:-translate-y-0.5 cursor-pointer text-sm shadow-sm inline-flex items-center gap-2"
          >
            <template #icon>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </template>
          </Button>
        </div>
      </div>

      <!-- Right Column: Services Grid -->
      <div class="lg:col-span-7">
        <div 
          v-if="services && services.length" 
          ref="servicesContainer"
          class="grid sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6"
        >
          <Card 
            v-for="(service, idx) in services" 
            :key="service.id"
            class="service-card flex flex-col hover:shadow-md border border-zinc-150/70 dark:border-zinc-900/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 group opacity-0 transform translate-y-8"
            :pt="{
              root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-3xl !p-0 shadow-sm relative overflow-hidden' },
              body: { class: '!p-8 !flex-grow !flex !flex-col !justify-between min-h-[250px]' }
            }"
          >
            <template #content>
              <div class="relative w-full h-full flex flex-col justify-between">
                <!-- Card Header (Icon & Index Number) -->
                <div class="flex items-center justify-between mb-6">
                  <div 
                    class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300 border border-zinc-200/50 dark:border-zinc-800/40 [&_svg]:w-6 [&_svg]:h-6"
                    :class="getColorScheme(idx).iconBg"
                  >
                    <div v-if="service.icon && service.icon.trim().startsWith('<svg')" v-html="service.icon" class="h-6 w-6 flex items-center justify-center text-current" />
                    <span v-else>{{ service.icon || '💼' }}</span>
                  </div>
                  <span class="text-xs font-bold text-zinc-300 dark:text-zinc-700 font-mono tracking-wider">
                    {{ String(idx + 1).padStart(2, '0') }}
                  </span>
                </div>

                <!-- Info content -->
                <div class="space-y-3 flex-grow">
                  <h4 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors">
                    {{ service.title }}
                  </h4>
                  <p class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-sm leading-relaxed line-clamp-3">
                    {{ service.short_description }}
                  </p>
                </div>

                <!-- Footer Link -->
                <div class="pt-6 mt-auto">
                  <NuxtLink 
                    to="/services"
                    class="inline-flex items-center text-xs font-bold transition-colors group/link gap-1"
                    :class="getColorScheme(idx).link"
                  >
                    Selengkapnya
                    <svg class="w-3.5 h-3.5 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                  </NuxtLink>
                </div>
              </div>
            </template>
          </Card>
        </div>

        <!-- Skeleton Loading -->
        <div v-else class="grid sm:grid-cols-2 gap-6">
          <Card 
            v-for="i in 4" 
            :key="i"
            :pt="{
              root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm border border-zinc-150/70 dark:border-zinc-900/60' },
              body: { class: '!p-8 min-h-[250px]' }
            }"
          >
            <template #content>
              <div class="flex items-center justify-between mb-6">
                <Skeleton width="3rem" height="3rem" borderRadius="12px" />
                <Skeleton width="1.5rem" height="1rem" />
              </div>
              <Skeleton width="10rem" height="1.5rem" class="mb-4" />
              <div class="space-y-2 mb-6">
                <Skeleton width="100%" height="0.875rem" />
                <Skeleton width="92%" height="0.875rem" />
                <Skeleton width="75%" height="0.875rem" />
              </div>
              <Skeleton width="6rem" height="1rem" />
            </template>
          </Card>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick, computed } from 'vue'

const { $gsap } = useNuxtApp()
const servicesContainer = ref<HTMLElement | null>(null)

// Fetch services from API
const { data: response, pending } = await useFetchAPI<any>('/services')

// Safe unwrapping of API resource collection (up to 6 items to fit grid layout perfectly)
const services = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data.filter((s: any) => s.is_active).slice(0, 6)
  }
  return []
})

// Dynamic colors to match each card index to the screenshot layout
const getColorScheme = (idx: number) => {
  const schemes = [
    {
      link: 'text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300',
      iconBg: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/10 dark:border-purple-500/5'
    },
    {
      link: 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300',
      iconBg: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/10 dark:border-emerald-500/5'
    },
    {
      link: 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300',
      iconBg: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/10 dark:border-blue-500/5'
    },
    {
      link: 'text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300',
      iconBg: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/10 dark:border-amber-500/5'
    },
    {
      link: 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300',
      iconBg: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/10 dark:border-indigo-500/5'
    },
    {
      link: 'text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300',
      iconBg: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/10 dark:border-rose-500/5'
    }
  ]
  return schemes[idx % schemes.length]
}

const initScrollTrigger = () => {
  if (!$gsap || !servicesContainer.value) return

  $gsap.fromTo(".service-card", 
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
        trigger: servicesContainer.value,
        start: "top 80%",
        toggleActions: "play none none none"
      }
    }
  )
}

onMounted(() => {
  if (!pending.value && services.value.length > 0) {
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
/* Scoped styles helper if needed */
</style>
