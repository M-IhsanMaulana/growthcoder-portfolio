<template>
  <div 
    v-if="!isLoaded" 
    ref="splashContainer" 
    class="fixed inset-0 z-[999999] flex flex-col items-center justify-between bg-zinc-950 py-10 overflow-hidden"
  >
    <!-- Top spacer to help center the logo vertically -->
    <div></div>

    <!-- Center Logo & Glow -->
    <div ref="logoContainer" class="flex flex-col items-center justify-center relative px-4 text-center select-none">
      <!-- Ambient Glow Backdrop -->
      <div class="absolute w-48 h-48 bg-brand-purple/20 blur-3xl rounded-full -z-10 animate-pulse-glow"></div>
      
      <!-- Brand Logo -->
      <NuxtImg 
        src="/logo-gc-light.png" 
        alt="growthcoder.id" 
        class="h-12 md:h-16 w-auto object-contain"
        width="230"
        height="64"
        loading="eager"
      />
      
      <!-- Sleek loader bar -->
      <div class="w-24 h-[2px] bg-zinc-800/80 rounded-full overflow-hidden mt-4 relative">
        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-brand-purple to-brand-green w-1/2 animate-shimmer"></div>
      </div>
    </div>

    <!-- Bottom Copyright -->
    <div ref="copyrightContainer" class="text-center select-none px-4">
      <p class="text-xs font-light text-zinc-500 tracking-widest font-sans uppercase">
        &copy; {{ new Date().getFullYear() }} growthcoder.id. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const isLoaded = useState('site-loaded', () => false)
const splashContainer = ref<HTMLElement | null>(null)
const logoContainer = ref<HTMLElement | null>(null)
const copyrightContainer = ref<HTMLElement | null>(null)

const { $gsap } = useNuxtApp()

onMounted(() => {
  // If splash was already played in this session, skip it completely
  if (sessionStorage.getItem('gc-splash-played')) {
    isLoaded.value = true
    return
  }

  if (!$gsap) {
    isLoaded.value = true
    return
  }

  // Create GSAP animation timeline
  const tl = $gsap.timeline({
    onComplete: () => {
      sessionStorage.setItem('gc-splash-played', 'true')
      isLoaded.value = true
    }
  })

  // Set initial states
  $gsap.set(logoContainer.value, { scale: 0.85, opacity: 0 })
  $gsap.set(copyrightContainer.value, { opacity: 0, y: 20 })

  // Animation timeline sequence
  tl.to(logoContainer.value, {
    opacity: 1,
    scale: 1,
    duration: 1.0,
    ease: "power3.out"
  })
  .to(copyrightContainer.value, {
    opacity: 1,
    y: 0,
    duration: 0.8,
    ease: "power2.out"
  }, "-=0.6")
  .to([logoContainer.value, copyrightContainer.value], {
    y: -30,
    opacity: 0,
    duration: 0.5,
    ease: "power3.in",
    delay: 1.5
  })
  .to(splashContainer.value, {
    yPercent: -100,
    duration: 0.8,
    ease: "power4.inOut"
  }, "-=0.2")
})
</script>

<style scoped>
@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(200%);
  }
}
@keyframes pulse-glow {
  0%, 100% {
    opacity: 0.6;
    transform: scale(0.95);
  }
  50% {
    opacity: 1;
    transform: scale(1.05);
  }
}
.animate-shimmer {
  animation: shimmer 1.8s infinite linear;
}
.animate-pulse-glow {
  animation: pulse-glow 3s infinite ease-in-out;
}
</style>
