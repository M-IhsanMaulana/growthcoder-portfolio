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
      class="flex flex-wrap justify-center gap-6"
    >
      <div 
        v-for="tech in technologies" 
        :key="tech.id"
        class="tech-card w-[calc(50%-12px)] sm:w-[calc(33.333%-16px)] md:w-[calc(25%-18px)] lg:w-[calc(16.666%-20px)] flex flex-col items-center justify-center p-6 bg-white dark:bg-zinc-950 border border-gray-100 dark:border-zinc-900 rounded-2xl shadow-sm hover:shadow-md hover:border-brand-purple dark:hover:border-brand-green hover:-translate-y-1.5 transition-all duration-300 group opacity-0 transform translate-y-8"
      >
        <!-- Logo -->
        <div class="w-16 h-16 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
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
      </div>
    </div>

    <!-- Skeleton Loading -->
    <div v-else class="flex flex-wrap justify-center gap-6">
      <div 
        v-for="i in 12" 
        :key="i"
        class="w-[calc(50%-12px)] sm:w-[calc(33.333%-16px)] md:w-[calc(25%-18px)] lg:w-[calc(16.666%-20px)] flex flex-col items-center justify-center p-6 bg-white dark:bg-zinc-950 border border-gray-100 dark:border-zinc-900 rounded-2xl shadow-sm"
      >
        <Skeleton shape="circle" size="4rem" class="mb-4" />
        <Skeleton width="5rem" height="1rem" />
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
