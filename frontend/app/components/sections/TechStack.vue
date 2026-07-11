<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-zinc-900 bg-gray-50/30 dark:bg-zinc-950/10 rounded-3xl overflow-hidden z-10 relative">
    <div class="text-center mb-12">
      <h2 class="text-xs uppercase tracking-widest text-brand-green font-bold mb-2">Tech Stack</h2>
      <h3 class="text-3xl font-extrabold text-brand-navy dark:text-white">Teknologi yang Saya Gunakan</h3>
    </div>
    
    <!-- Grid container for Tech Cards -->
    <div 
      v-if="technologies && technologies.length" 
      ref="techContainer"
      class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6"
    >
      <div 
        v-for="tech in technologies" 
        :key="tech.id"
        class="tech-card flex flex-col items-center justify-center p-6 bg-white dark:bg-zinc-950 border border-gray-100 dark:border-zinc-900 rounded-2xl shadow-sm hover:shadow-md hover:border-brand-purple dark:hover:border-brand-green hover:-translate-y-1.5 transition-all duration-300 group opacity-0 transform translate-y-8"
      >
        <!-- Logo -->
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-navy/5 to-brand-purple/5 dark:from-zinc-900 dark:to-zinc-800 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300 overflow-hidden p-2">
          <NuxtImg
            v-if="tech.logo?.urls?.thumbnail || tech.logo?.urls?.original"
            :src="tech.logo.urls.thumbnail || tech.logo.urls.original"
            :alt="tech.name"
            class="w-full h-full object-contain"
            loading="lazy"
          />
          <span v-else class="text-xl font-bold text-zinc-400 dark:text-zinc-500">
            {{ tech.name.substring(0, 2).toUpperCase() }}
          </span>
        </div>
        
        <!-- Name -->
        <h4 class="text-sm font-semibold text-brand-navy dark:text-white text-center group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors">
          {{ tech.name }}
        </h4>
        
        <!-- Category Badge -->
        <span class="mt-1.5 text-[10px] text-zinc-400 dark:text-zinc-500 font-medium px-2.5 py-0.5 rounded bg-zinc-150 dark:bg-zinc-900">
          {{ tech.category }}
        </span>
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <div 
        v-for="i in 12" 
        :key="i"
        class="flex flex-col items-center justify-center p-6 bg-white dark:bg-zinc-950 border border-gray-100 dark:border-zinc-900 rounded-2xl shadow-sm"
      >
        <Skeleton shape="circle" size="4rem" class="mb-4" />
        <Skeleton width="5rem" height="1rem" class="mb-2" />
        <Skeleton width="3rem" height="0.75rem" />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick, computed } from 'vue'

const { $gsap } = useNuxtApp()
const techContainer = ref<HTMLElement | null>(null)

// Fetch only featured technologies from API
const { data: response, pending } = await useFetchAPI<any>('/technologies?featured=1')

// Extract the technology list
const technologies = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data
  }
  return []
})

const initScrollTrigger = () => {
  if (!$gsap || !techContainer.value) return

  $gsap.fromTo(".tech-card", 
    { 
      opacity: 0, 
      y: 40
    }, 
    {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0.08,
      ease: "power2.out",
      scrollTrigger: {
        trigger: techContainer.value,
        start: "top 85%",
        toggleActions: "play none none none"
      }
    }
  )
}

onMounted(() => {
  if (!pending.value && technologies.value.length > 0) {
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
/* No longer using marquee styles */
</style>
