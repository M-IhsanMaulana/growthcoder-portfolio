<template>
  <Transition name="fade">
    <div 
      v-if="showLoader" 
      class="fixed inset-0 z-[99999] flex items-center justify-center bg-zinc-50/75 dark:bg-zinc-950/75 backdrop-blur-md"
    >
      <div class="glass-card-premium p-8 rounded-2xl flex flex-col items-center max-w-xs w-full text-center shadow-2xl relative overflow-hidden">
        <!-- Minimal subtle glow -->
        <div class="absolute -top-10 -left-10 w-24 h-24 bg-brand-purple/10 blur-2xl rounded-full"></div>
        <div class="absolute -bottom-10 -right-10 w-24 h-24 bg-brand-green/10 blur-2xl rounded-full"></div>

        <!-- Dynamic Brand Logo -->
        <NuxtImg 
          :src="isDark ? '/logo-gc-light.png' : '/logo-gc-dark.png'" 
          alt="loading..." 
          class="h-8 w-auto object-contain mb-5 select-none"
          width="115"
          height="32"
          loading="eager"
        />
        
        <!-- Modern minimalist loader line -->
        <div class="w-32 h-[3px] bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden mt-1 relative">
          <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-brand-purple to-brand-green w-1/3 rounded-full animate-progress-flow"></div>
        </div>

        <!-- Subtle loading message -->
        <span class="mt-4 text-[10px] font-medium tracking-widest text-zinc-400 dark:text-zinc-500 uppercase select-none font-sans">
          Memuat Halaman
        </span>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useDark } from '@vueuse/core'

// Determine if the page is currently in dark mode
const isDark = useDark({
  selector: 'html',
  attribute: 'class',
  valueDark: 'dark',
  valueLight: '',
  initialValue: 'dark',
})

// Shared site-loaded state from splash screen
const isSplashLoaded = useState('site-loaded', () => false)

// States for initial hydration/load and route transitions
const isInitialLoading = ref(true)
const isRouteLoading = ref(false)

// Start loading delay timer to prevent flashing on rapid route changes
let routeStartTimeout: any = null

// Show loader only if:
// 1. Splash screen has completed (to prevent double loaders).
// 2. Either initial page load is active or a routing load is active.
const showLoader = computed(() => {
  if (!isSplashLoaded.value) return false
  return isInitialLoading.value || isRouteLoading.value
})

const nuxtApp = useNuxtApp()

// Hooks registration
const handlePageStart = () => {
  // Clear any pre-existing timers
  if (routeStartTimeout) clearTimeout(routeStartTimeout)
  
  // Show loader after 120ms to avoid flicker on instant pages
  routeStartTimeout = setTimeout(() => {
    isRouteLoading.value = true
  }, 120)
}

const handlePageFinish = () => {
  if (routeStartTimeout) {
    clearTimeout(routeStartTimeout)
    routeStartTimeout = null
  }
  isRouteLoading.value = false
}

onMounted(() => {
  // Initial page load finishes shortly after mounting
  setTimeout(() => {
    isInitialLoading.value = false
  }, 500)

  // Listen to Nuxt runtime hooks for routing transitions
  nuxtApp.hook('page:start', handlePageStart)
  nuxtApp.hook('page:finish', handlePageFinish)
})

onBeforeUnmount(() => {
  if (routeStartTimeout) clearTimeout(routeStartTimeout)
})
</script>

<style scoped>
@keyframes progress-flow {
  0% {
    left: -40%;
    width: 30%;
  }
  50% {
    width: 50%;
  }
  100% {
    left: 110%;
    width: 30%;
  }
}

.animate-progress-flow {
  animation: progress-flow 1.4s infinite ease-in-out;
}

/* Fade transition styles */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
