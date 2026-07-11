import tailwindcss from '@tailwindcss/vite'
import Aura from '@primeuix/themes/aura'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  routeRules: {
    '/projects': { redirect: { to: '/proyek', statusCode: 301 } }
  },
  css: ['~/assets/css/main.css'],
  app: {
    pageTransition: {
      name: 'page',
      mode: 'out-in'
    }
  },
  modules: [
    '@nuxt/image',
    '@nuxt/fonts',
    '@vueuse/nuxt',
    '@hypernym/nuxt-gsap',
    '@primevue/nuxt-module',
    'nuxt-gtag'
  ],
  gtag: {
    id: process.env.NUXT_PUBLIC_GTAG_ID || ''
  },
  primevue: {
    options: {
      theme: {
        preset: Aura,
        options: {
          darkModeSelector: '.dark',
          cssLayer: {
            name: 'primevue',
            order: 'base, primevue, utilities'
          }
        }
      }
    }
  },
  vite: {
    plugins: [
      tailwindcss()
    ]
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://cms.growthcoder.local/api/v1',
      apiKey: process.env.NUXT_PUBLIC_API_KEY || '',
      googleSiteVerification: process.env.NUXT_PUBLIC_GOOGLE_SITE_VERIFICATION || ''
    }
  },
  nitro: {
    prerender: {
      routes: ['/sitemap.xml']
    }
  },
  gsap: {
    extraPlugins: {
      scrollTrigger: true,
      motionPath: true
    }
  }
})

