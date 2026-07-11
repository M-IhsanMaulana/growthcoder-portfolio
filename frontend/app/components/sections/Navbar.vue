<template>
  <header class="sticky top-0 z-40 w-full bg-white/60 dark:bg-[#09090b]/50 backdrop-blur-lg border-b border-zinc-200/50 dark:border-zinc-800/40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between relative z-10">
      <!-- Brand Logo -->
      <NuxtLink to="/" class="flex items-center group">
        <client-only>
          <NuxtImg 
            :src="isDark ? '/logo-gc-light.png' : '/logo-gc-dark.png'" 
            alt="growthcoder.id" 
            class="h-10 w-auto object-contain"
            width="144"
            height="40"
            loading="eager"
          />
          <template #fallback>
            <div class="h-10 w-36 animate-pulse bg-gray-200 dark:bg-zinc-800 rounded"></div>
          </template>
        </client-only>
      </NuxtLink>

      <!-- Desktop Navigation -->
      <nav class="hidden lg:flex items-center space-x-8">
        <NuxtLink 
          v-for="link in navLinks" 
          :key="link.path" 
          :to="link.path"
          class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-purple dark:hover:text-brand-purple transition-colors duration-300 relative py-2"
          active-class="text-brand-purple dark:text-brand-purple font-semibold"
        >
          {{ link.name }}
          <span 
            v-if="$route.path === link.path"
            class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-[0_0_8px_rgba(92,89,217,0.8)]"
          ></span>
        </NuxtLink>
      </nav>

      <!-- Actions (Theme Toggle & Mobile Menu Btn) -->
      <div class="flex items-center space-x-3">
        <!-- Download CV Button (Desktop) -->
        <Button 
          v-if="settings?.cv_file_url" 
          as="a"
          href="/cv" 
          target="_blank" 
          label="Download CV"
          icon="pi pi-download"
          class="!px-4 !py-2 !text-xs !font-medium !rounded-xl !border-zinc-200 dark:!border-zinc-800 !bg-zinc-900 dark:!bg-zinc-100 !text-white dark:!text-zinc-950 hover:!bg-zinc-800 dark:hover:!bg-white cursor-pointer transition-all duration-300 hidden md:inline-flex"
        />
        <Button 
          v-else
          as="a"
          href="#"
          label="Download CV"
          icon="pi pi-download"
          class="!px-4 !py-2 !text-xs !font-medium !rounded-xl !border-zinc-200 dark:!border-zinc-800 !bg-zinc-900 dark:!bg-zinc-100 !text-white dark:!text-zinc-950 hover:!bg-zinc-800 dark:hover:!bg-white cursor-pointer transition-all duration-300 hidden md:inline-flex"
        />

        <!-- Theme Toggle Button -->
        <Button 
          @click="toggleDark()" 
          severity="secondary"
          variant="outlined"
          class="!p-2.5 !rounded-xl !border-gray-200 dark:!border-zinc-800 hover:!bg-gray-100 dark:hover:!bg-zinc-800 focus:outline-none transition-colors duration-300 cursor-pointer" 
          aria-label="Toggle Theme"
        >
          <template #icon>
            <client-only>
              <span v-if="isDark" class="text-yellow-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="4"></circle>
                  <path d="M12 2v2"></path>
                  <path d="M12 20v2"></path>
                  <path d="m4.93 4.93 1.41 1.41"></path>
                  <path d="m17.66 17.66 1.41 1.41"></path>
                  <path d="M2 12h2"></path>
                  <path d="M20 12h2"></path>
                  <path d="m6.34 17.66-1.41 1.41"></path>
                  <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
              </span>
              <span v-else class="text-zinc-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                </svg>
              </span>
              <template #fallback>
                <div class="w-5 h-5 animate-pulse bg-gray-200 dark:bg-zinc-800 rounded"></div>
              </template>
            </client-only>
          </template>
        </Button>

        <!-- Mobile Menu Open -->
        <Button 
          @click="isMenuOpen = true" 
          severity="secondary"
          variant="outlined"
          class="lg:hidden !p-2.5 !rounded-lg !border-gray-200 dark:!border-zinc-800 hover:!bg-gray-100 dark:hover:!bg-zinc-800 focus:outline-none transition-colors duration-300 cursor-pointer" 
          aria-label="Open Menu"
        >
          <template #icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="4" x2="20" y1="12" y2="12"></line>
              <line x1="4" x2="20" y1="6" y2="6"></line>
              <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
          </template>
        </Button>
      </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <Drawer 
      v-model:visible="isMenuOpen" 
      position="right" 
      class="!bg-white dark:!bg-zinc-950 !border-l !border-gray-200 dark:!border-zinc-800 !w-4/5 !max-w-sm !h-full"
      :pt="{
        header: { class: '!hidden' },
        content: { class: '!p-6 !h-full' }
      }"
    >
      <div class="h-full flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-8">
            <span class="text-xl font-bold text-gray-900 dark:text-white">Menu Navigasi</span>
            <Button 
              @click="isMenuOpen = false" 
              severity="secondary"
              variant="outlined"
              class="!p-2.5 !rounded-lg !border-gray-200 dark:!border-zinc-800 hover:!bg-gray-100 dark:hover:!bg-zinc-800 cursor-pointer" 
              aria-label="Close Menu"
            >
              <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 6 6 18"></path>
                  <path d="m6 6 12 12"></path>
                </svg>
              </template>
            </Button>
          </div>
          
          <nav class="flex flex-col space-y-2">
            <NuxtLink 
              v-for="link in navLinks" 
              :key="link.path" 
              :to="link.path"
              @click="isMenuOpen = false"
              class="text-gray-600 dark:text-gray-300 hover:text-brand-purple dark:hover:text-brand-purple text-base py-3 border-b border-gray-100 dark:border-zinc-900 transition-colors duration-300 block font-medium"
              active-class="text-brand-purple dark:text-brand-purple font-semibold border-brand-purple"
            >
              {{ link.name }}
            </NuxtLink>
          </nav>
        </div>
        
        <div class="text-xs text-center text-gray-400 dark:text-gray-500">
          &copy; {{ new Date().getFullYear() }} growthcoder.id. All rights reserved.
        </div>
      </div>
    </Drawer>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useDark, useToggle } from '@vueuse/core'

const { settings } = useSettings()

const isDark = useDark({
  selector: 'html',
  attribute: 'class',
  valueDark: 'dark',
  valueLight: '',
  initialValue: 'dark',
})
const toggleDark = useToggle(isDark)

const isMenuOpen = ref(false)

const navLinks = [
  { name: 'Home', path: '/' },
  { name: 'About', path: '/about' },
  { name: 'Services', path: '/services' },
  { name: 'Skills', path: '/about/skills' },
  { name: 'Projects', path: '/proyek' },
  { name: 'Blog', path: '/blog' },
  { name: 'Contact', path: '/contact' },
]
</script>
