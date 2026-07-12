<template>
  <div>
    <!-- Accessible Route Announcer -->
    <NuxtRouteAnnouncer />

    <!-- Custom Page Loader -->
    <UiPageLoader />

    <!-- Intro Splash Screen Animation -->
    <UiSplashScreen />

    <!-- Layout & Routing Entry -->
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'

const { settings } = useSettings()
const config = useRuntimeConfig()

// Determine GSC verification code (prioritize CMS settings, fallback to env)
const gscCode = computed(() => settings.value?.google_site_verification || config.public.googleSiteVerification || '')

// Configure global default head metadata
useHead({
  titleTemplate: (titleChunk) => {
    const suffix = settings.value?.meta_title_suffix || ' | growthcoder.id'
    const defaultTitle = settings.value?.owner_full_name ? `${settings.value.owner_full_name}${suffix}` : 'Muhammad Ihsan Maulana | growthcoder.id'
    return titleChunk ? `${titleChunk}${suffix}` : defaultTitle
  },
  meta: [
    {
      name: 'description',
      content: () => settings.value?.default_meta_desc || 'Portofolio Profesional & Blog Muhammad Ihsan Maulana. Full-Stack Developer & Automation Specialist.'
    },
    {
      name: 'google-site-verification',
      content: () => gscCode.value || undefined
    }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
    { rel: 'icon', type: 'image/png', href: '/favicon.png' },
    { rel: 'apple-touch-icon', href: '/apple-touch-icon.png' }
  ],
  script: [
    {
      innerHTML: `
        (function() {
          try {
            const theme = localStorage.getItem('vueuse-color-scheme') || 'auto';
            const isDark = theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
              document.documentElement.classList.add('dark');
            } else {
              document.documentElement.classList.remove('dark');
            }
          } catch (e) {}
        })();
      `.replace(/\s+/g, ' '),
      type: 'text/javascript'
    }
  ]
})

// Dynamic Google Analytics Initialization on Client-side
onMounted(() => {
  const envId = config.public.gtag?.id
  const cmsId = settings.value?.google_analytics_id
  
  if (!envId && cmsId) {
    const { initialize } = useGtag()
    initialize(cmsId)
  }
})
</script>
