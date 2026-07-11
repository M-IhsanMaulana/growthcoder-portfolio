<template>
  <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden select-none">
    <!-- Glow Element -->
    <div
      ref="glowRef"
      class="absolute w-[350px] sm:w-[500px] h-[350px] sm:h-[500px] rounded-full bg-gradient-to-tr from-purple-500/70 to-emerald-500/70 dark:from-purple-600/70 dark:to-emerald-500/70 opacity-[0.06] dark:opacity-[0.14] blur-[80px] sm:blur-[120px] transition-opacity duration-300 transform -translate-x-1/2 -translate-y-1/2 will-change-transform"
      style="left: -999px; top: -999px;"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const { $gsap } = useNuxtApp()
const glowRef = ref<HTMLElement | null>(null)

let handleMouseMove: (e: MouseEvent) => void

onMounted(() => {
  if (!process.client || !glowRef.value) return

  let mouseX = -999
  let mouseY = -999
  let currentX = -999
  let currentY = -999
  let isInitialized = false

  handleMouseMove = (e: MouseEvent) => {
    if (!isInitialized) {
      isInitialized = true
      currentX = e.clientX
      currentY = e.clientY
      if (glowRef.value) {
        glowRef.value.style.left = '0'
        glowRef.value.style.top = '0'
      }
    }
    mouseX = e.clientX
    mouseY = e.clientY
  }

  window.addEventListener('mousemove', handleMouseMove)

  if ($gsap) {
    const onTick = () => {
      if (!isInitialized) return

      // Smooth interpolation (lerp) for cursor trailing
      currentX += (mouseX - currentX) * 0.08
      currentY += (mouseY - currentY) * 0.08

      $gsap.set(glowRef.value, {
        x: currentX,
        y: currentY
      })
    }
    
    $gsap.ticker.add(onTick)
    
    // Cleanup ticker reference in unmount
    onUnmounted(() => {
      $gsap.ticker.remove(onTick)
    })
  } else {
    // Fallback if GSAP is not available
    let animId: number
    const updatePosition = () => {
      if (isInitialized) {
        currentX += (mouseX - currentX) * 0.08
        currentY += (mouseY - currentY) * 0.08
        if (glowRef.value) {
          glowRef.value.style.transform = `translate3d(calc(${currentX}px - 50%), calc(${currentY}px - 50%), 0)`
        }
      }
      animId = requestAnimationFrame(updatePosition)
    }
    updatePosition()
    
    onUnmounted(() => {
      cancelAnimationFrame(animId)
    })
  }
})

onUnmounted(() => {
  if (process.client && handleMouseMove) {
    window.removeEventListener('mousemove', handleMouseMove)
  }
})
</script>
