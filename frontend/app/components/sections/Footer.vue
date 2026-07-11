<template>
  <footer class="relative overflow-hidden bg-white dark:bg-zinc-950 transition-colors duration-300">
    <!-- Gradient top border -->
    <div class="h-px w-full bg-gradient-to-r from-transparent via-brand-purple/60 to-transparent" />

    <!-- Subtle background decoration -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -bottom-32 -left-32 h-72 w-72 rounded-full bg-brand-purple/5 blur-3xl" />
      <div class="absolute -bottom-20 -right-20 h-56 w-56 rounded-full bg-indigo-500/5 blur-3xl" />
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Main footer content -->
      <div class="py-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- Brand Column -->
        <div class="lg:col-span-1 flex flex-col space-y-5">
          <client-only>
            <NuxtLink to="/" class="inline-block group">
              <NuxtImg
                :src="isDark ? '/logo-gc-light.png' : '/logo-gc-dark.png'"
                alt="growthcoder.id"
                class="h-10 w-auto object-contain transition-opacity duration-300 group-hover:opacity-80"
                width="144"
                height="40"
                loading="lazy"
              />
            </NuxtLink>
            <template #fallback>
              <div class="h-10 w-36 animate-pulse bg-gray-200 dark:bg-zinc-800 rounded" />
            </template>
          </client-only>

          <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-xs">
            Membangun solusi digital yang modern, efisien, dan berdampak.
          </p>

          <!-- Social icons (mobile-friendly row) -->
          <div class="flex items-center gap-3 pt-1">
            <a
              v-if="settings?.social_github"
              :href="settings.social_github"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="GitHub"
              class="social-icon-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4" />
                <path d="M9 18c-4.51 2-5-2-7-2" />
              </svg>
            </a>
            <a
              v-if="settings?.social_linkedin"
              :href="settings.social_linkedin"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="LinkedIn"
              class="social-icon-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                <rect width="4" height="12" x="2" y="9" />
                <circle cx="4" cy="4" r="2" />
              </svg>
            </a>
            <a
              v-if="settings?.social_telegram"
              :href="settings.social_telegram"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="Telegram"
              class="social-icon-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m22 2-7 20-4-9-9-4Z" />
                <path d="M22 2 11 13" />
              </svg>
            </a>
            <a
              v-if="settings?.social_instagram"
              :href="settings.social_instagram"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="Instagram"
              class="social-icon-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="flex flex-col space-y-4">
          <h3 class="footer-heading">Quick Links</h3>
          <nav class="flex flex-col space-y-2.5">
            <NuxtLink
              v-for="link in quickLinks"
              :key="link.to"
              :to="link.to"
              class="footer-link"
            >
              <span class="footer-link-indicator" />
              {{ link.label }}
            </NuxtLink>
          </nav>
        </div>

        <!-- Connect / Contact -->
        <div class="flex flex-col space-y-4">
          <h3 class="footer-heading">Connect</h3>
          <div class="flex flex-col space-y-3">
            <a
              v-if="settings?.social_github"
              :href="settings.social_github"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-connect-link"
            >
              <span class="footer-connect-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4" />
                  <path d="M9 18c-4.51 2-5-2-7-2" />
                </svg>
              </span>
              <span>GitHub</span>
            </a>
            <a
              v-if="settings?.social_linkedin"
              :href="settings.social_linkedin"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-connect-link"
            >
              <span class="footer-connect-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                  <rect width="4" height="12" x="2" y="9" />
                  <circle cx="4" cy="4" r="2" />
                </svg>
              </span>
              <span>LinkedIn</span>
            </a>
            <a
              v-if="settings?.social_telegram"
              :href="settings.social_telegram"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-connect-link"
            >
              <span class="footer-connect-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="m22 2-7 20-4-9-9-4Z" />
                  <path d="M22 2 11 13" />
                </svg>
              </span>
              <span>Telegram</span>
            </a>
            <a
              v-if="settings?.social_instagram"
              :href="settings.social_instagram"
              target="_blank"
              rel="noopener noreferrer"
              class="footer-connect-link"
            >
              <span class="footer-connect-icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                </svg>
              </span>
              <span>Instagram</span>
            </a>
          </div>
        </div>

        <!-- Let's Collaborate CTA -->
        <div class="flex flex-col space-y-4">
          <h3 class="footer-heading">Let's Collaborate</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
            Punya proyek atau ide menarik? Mari wujudkan bersama.
          </p>
          <NuxtLink
            to="/contact"
            class="group inline-flex items-center gap-2 w-fit rounded-lg bg-brand-purple px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-brand-purple/25 transition-all duration-300 hover:shadow-brand-purple/40 hover:scale-105 hover:bg-indigo-600"
          >
            Hubungi Saya
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7 17 17 7" />
              <path d="M7 7h10v10" />
            </svg>
          </NuxtLink>

          <!-- Optional: email/contact quick ref -->
          <a
            v-if="settings?.contact_email"
            :href="`mailto:${settings.contact_email}`"
            class="text-xs text-gray-400 dark:text-gray-500 hover:text-brand-purple dark:hover:text-brand-purple transition-colors duration-200 break-all"
          >
            {{ settings.contact_email }}
          </a>
        </div>

      </div>

      <!-- Bottom bar -->
      <div class="border-t border-gray-100 dark:border-zinc-800/70 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 dark:text-gray-500">
          &copy; {{ new Date().getFullYear() }} {{ siteName }}. All rights reserved.
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500">
          Built with ❤️ by
          <span class="font-medium text-gray-600 dark:text-gray-300">{{ ownerName }}</span>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useDark } from '@vueuse/core'

const isDark = useDark()

const { settings } = useSettings()

const ownerName = computed(() => settings.value?.owner_full_name || 'Muhammad Ihsan Maulana')
const siteName = computed(() => settings.value?.site_name || 'growthcoder.id')

const quickLinks = [
  { to: '/', label: 'Home' },
  { to: '/about', label: 'About' },
  { to: '/services', label: 'Services' },
  { to: '/proyek', label: 'Projects' },
  { to: '/blog', label: 'Blog' },
]
</script>

<style scoped>
@reference "~/assets/css/main.css";
.footer-heading {
  @apply text-sm font-semibold tracking-wider uppercase text-gray-900 dark:text-white;
}

.footer-link {
  @apply relative flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400
         hover:text-brand-purple dark:hover:text-white transition-colors duration-200 w-fit;
}

.footer-link-indicator {
  @apply block h-px w-0 bg-brand-purple transition-all duration-300;
}

.footer-link:hover .footer-link-indicator {
  @apply w-3;
}

.footer-connect-link {
  @apply flex items-center gap-2.5 text-sm text-gray-500 dark:text-gray-400
         hover:text-brand-purple dark:hover:text-white transition-colors duration-200 w-fit;
}

.footer-connect-icon-wrap {
  @apply flex items-center justify-center w-6 h-6 rounded-md
         bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-gray-400
         transition-all duration-200;
}

.footer-connect-link:hover .footer-connect-icon-wrap {
  @apply bg-brand-purple/10 text-brand-purple dark:bg-brand-purple/20 dark:text-white;
}

.social-icon-btn {
  @apply flex items-center justify-center w-8 h-8 rounded-lg
         bg-gray-100 dark:bg-zinc-800 text-gray-500 dark:text-gray-400
         hover:bg-brand-purple/10 hover:text-brand-purple
         dark:hover:bg-brand-purple/20 dark:hover:text-white
         transition-all duration-200;
}
</style>
