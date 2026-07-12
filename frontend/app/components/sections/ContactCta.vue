<template>
  <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-zinc-900 overflow-hidden">
    <div 
      ref="ctaBox"
      class="glass-panel rounded-3xl p-8 md:p-12 shadow-xl bg-gradient-to-br from-white/80 to-brand-light/20 dark:from-zinc-950/80 dark:to-zinc-900/20 text-center space-y-6 relative overflow-hidden border border-brand-purple/10 opacity-0 transform scale-95"
    >
      <!-- Background glowing orb -->
      <div class="absolute -top-16 -left-16 w-32 h-32 bg-brand-purple opacity-20 blur-3xl rounded-full"></div>
      <div class="absolute -bottom-16 -right-16 w-32 h-32 bg-brand-green opacity-20 blur-3xl rounded-full"></div>

      <div class="relative z-10 space-y-4">
        <h2 class="text-xs uppercase tracking-widest text-brand-green font-bold mb-2">Kolaborasi</h2>
        <h3 class="text-3xl font-extrabold text-brand-navy dark:text-white">
          Ada Rencana Proyek? Mari Diskusi
        </h3>
        <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto font-light leading-relaxed">
          Setiap pesan masuk di halaman kontak akan diteruskan secara instan ke bot Telegram pribadi saya. Mari buat sistem impian Anda kenyataan.
        </p>
      </div>

      <!-- Primary Action -->
      <div class="relative z-10 pt-4 flex justify-center">
        <Button 
          as="router-link"
          to="/contact" 
          label="Kirim Pesan Sekarang"
          icon="pi pi-send"
          iconPos="right"
          class="!px-8 !py-3.5 !font-semibold !rounded-lg shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 cursor-pointer"
        />
      </div>

      <!-- Social Links Row -->
      <div class="relative z-10 pt-6 border-t border-gray-100 dark:border-zinc-900/60 flex flex-col items-center gap-3">
        <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Atau hubungi via media sosial</span>
        
        <div class="flex items-center justify-center space-x-6">
          <a 
            v-if="settings?.social_github" 
            :href="settings.social_github" 
            target="_blank" 
            class="p-2 text-gray-500 hover:text-brand-navy dark:hover:text-white hover:scale-110 transition-all duration-300" 
            aria-label="GitHub"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"></path>
              <path d="M9 18c-4.51 2-5-2-7-2"></path>
            </svg>
          </a>
          
          <a 
            v-if="settings?.social_linkedin" 
            :href="settings.social_linkedin" 
            target="_blank" 
            class="p-2 text-gray-500 hover:text-brand-navy dark:hover:text-white hover:scale-110 transition-all duration-300" 
            aria-label="LinkedIn"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
              <rect width="4" height="12" x="2" y="9"></rect>
              <circle cx="4" cy="4" r="2"></circle>
            </svg>
          </a>
          
          <a 
            v-if="settings?.social_telegram" 
            :href="settings.social_telegram" 
            target="_blank" 
            class="p-2 text-gray-500 hover:text-brand-navy dark:hover:text-white hover:scale-110 transition-all duration-300" 
            aria-label="Telegram"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m22 2-7 20-4-9-9-4Z"></path>
              <path d="M22 2 11 13"></path>
            </svg>
          </a>
          
          <a 
            v-if="settings?.social_instagram" 
            :href="settings.social_instagram" 
            target="_blank" 
            class="p-2 text-gray-500 hover:text-brand-navy dark:hover:text-white hover:scale-110 transition-all duration-300" 
            aria-label="Instagram"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
          
          <a 
            v-if="settings?.social_twitter" 
            :href="settings.social_twitter" 
            target="_blank" 
            class="p-2 text-gray-500 hover:text-brand-navy dark:hover:text-white hover:scale-110 transition-all duration-300" 
            aria-label="Twitter/X"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const { settings } = useSettings()
const { $gsap } = useNuxtApp()
const ctaBox = ref<HTMLElement | null>(null)

const initScrollTrigger = () => {
  if (!$gsap || !ctaBox.value) return

  $gsap.fromTo(ctaBox.value, 
    { 
      opacity: 0, 
      scale: 0.95,
      y: 20
    }, 
    {
      opacity: 1,
      scale: 1,
      y: 0,
      duration: 0.8,
      ease: "power3.out",
      scrollTrigger: {
        trigger: ctaBox.value,
        start: "top 85%",
        toggleActions: "play none none none"
      }
    }
  )
}

onMounted(() => {
  nextTick(() => {
    initScrollTrigger()
  })
})
</script>
