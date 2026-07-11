<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-100 dark:border-zinc-900 overflow-hidden relative">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-6">
      <div class="space-y-4">
        <!-- Badge -->
        <div>
          <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
            Artikel Terbaru
          </span>
        </div>
        <!-- Title & Subtitle -->
        <div>
          <h3 class="text-4xl font-extrabold tracking-tight text-brand-navy dark:text-white leading-[1.2]">
            Blog &amp; Artikel <span class="text-blue-purple-gradient">Highlight</span>
          </h3>
          <p class="text-zinc-500 dark:text-zinc-400 text-sm sm:text-base leading-relaxed mt-2 font-normal">
            Berbagi tutorial, tips, dan pengalaman seputar pengembangan web, mobile, dan teknologi terbaru.
          </p>
        </div>
      </div>
      
      <!-- Link "Lihat Semua Artikel" -->
      <div class="flex-shrink-0">
        <NuxtLink 
          to="/blog" 
          class="inline-flex items-center text-sm font-bold text-brand-purple dark:text-indigo-400 hover:text-brand-navy dark:hover:text-white transition-colors group cursor-pointer"
        >
          Lihat Semua Artikel
          <svg class="w-4 h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
          </svg>
        </NuxtLink>
      </div>
    </div>

    <!-- Grid -->
    <div 
      v-if="posts && posts.length" 
      ref="blogContainer"
      class="grid md:grid-cols-3 gap-8"
    >
      <Card 
        v-for="post in posts" 
        :key="post.id"
        class="blog-card group flex flex-col hover:shadow-md border border-zinc-150/70 dark:border-zinc-900/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 opacity-0 transform translate-y-8"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-3xl !p-0 shadow-sm relative overflow-hidden' },
          body: { class: '!p-6 !flex-grow !flex !flex-col !justify-between min-h-[280px]' }
        }"
      >
        <template #header>
          <!-- Thumbnail cover -->
          <div class="aspect-video w-full bg-gray-100 dark:bg-zinc-900 overflow-hidden relative">
            <NuxtImg 
              v-if="post.cover_image?.urls?.medium || post.cover_image?.urls?.original"
              :src="post.cover_image.urls.medium || post.cover_image.urls.original" 
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              loading="lazy"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-brand-navy/10 to-brand-purple/20 flex items-center justify-center text-4xl select-none group-hover:scale-105 transition-transform duration-500">
              💡
            </div>
            
            <!-- Category Badge Overlay on bottom-left -->
            <span 
              v-if="post.categories && post.categories.length" 
              class="absolute bottom-3 left-3 px-3 py-1.5 bg-zinc-950/75 backdrop-blur-md border border-white/10 rounded-lg text-[10px] font-bold tracking-wider text-white uppercase"
            >
              {{ post.categories[0].name }}
            </span>
          </div>
        </template>

        <template #content>
          <div class="space-y-3 flex-grow">
            <!-- Meta info (Date & reading time) -->
            <div class="flex items-center space-x-2 text-xs text-zinc-400 dark:text-zinc-500 font-medium">
              <span>{{ formatDate(post.published_at) }}</span>
              <span class="text-zinc-300 dark:text-zinc-800">•</span>
              <span v-if="post.reading_time">{{ post.reading_time }} min read</span>
            </div>

            <!-- Title -->
            <h4 class="text-base sm:text-lg font-bold leading-snug text-zinc-900 dark:text-white group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors line-clamp-2">
              <NuxtLink :to="`/blog/${post.slug}`" class="cursor-pointer">
                {{ post.title }}
              </NuxtLink>
            </h4>
            
            <!-- Description Excerpt -->
            <p class="text-zinc-500 dark:text-zinc-400 text-xs sm:text-sm line-clamp-2 leading-relaxed font-normal">
              {{ post.excerpt }}
            </p>
          </div>
        </template>

        <template #footer>
          <!-- Card Footer (Author & Action Button) -->
          <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-6">
            <!-- Author Info -->
            <div class="flex items-center space-x-2.5">
              <NuxtImg 
                :src="settings?.profile_photo?.urls?.thumbnail || settings?.profile_photo?.urls?.original || '/portrait.png'" 
                :alt="authorName"
                class="w-7 h-7 rounded-full object-cover border border-zinc-200 dark:border-zinc-850"
                width="28"
                height="28"
                loading="lazy"
              />
              <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300">{{ authorName }}</span>
            </div>

            <!-- Action Circle Arrow -->
            <NuxtLink 
              :to="`/blog/${post.slug}`" 
              class="w-8 h-8 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-brand-purple dark:hover:border-brand-green flex items-center justify-center text-zinc-450 hover:text-brand-purple dark:hover:text-brand-green hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-300"
            >
              <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </NuxtLink>
          </div>
        </template>
      </Card>
    </div>

    <!-- Skeleton Loading -->
    <div v-else class="grid md:grid-cols-3 gap-8">
      <Card 
        v-for="i in 3" 
        :key="i"
        :pt="{
          root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm border border-zinc-150/70 dark:border-zinc-900/60' },
          body: { class: '!p-6 min-h-[300px]' }
        }"
      >
        <template #header>
          <Skeleton class="w-full aspect-video rounded-t-3xl" />
        </template>
        
        <template #content>
          <Skeleton width="40%" height="0.875rem" class="mb-3" />
          <Skeleton width="90%" height="1.5rem" class="mb-3" />
          <div class="space-y-2 mb-6">
            <Skeleton width="100%" height="0.875rem" />
            <Skeleton width="75%" height="0.875rem" />
          </div>
          <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-6">
            <div class="flex items-center space-x-2">
              <Skeleton shape="circle" size="1.75rem" />
              <Skeleton width="4rem" height="0.875rem" />
            </div>
            <Skeleton shape="circle" size="2rem" />
          </div>
        </template>
      </Card>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick, computed } from 'vue'

const { $gsap } = useNuxtApp()
const blogContainer = ref<HTMLElement | null>(null)

// Fetch site settings
const { settings } = useSettings()

const authorName = computed(() => {
  const full = settings.value?.owner_full_name
  if (!full) return 'Inteones'
  return full.split(' ').pop() ?? full
})

// Fetch posts (3 articles max for homepage)
const { data: response, pending } = await useFetchAPI<any>('/posts?per_page=3')

// Safe unwrapping
const posts = computed(() => {
  if (response.value && Array.isArray(response.value.data)) {
    return response.value.data
  }
  return []
})

const formatDate = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(date)
}

const initScrollTrigger = () => {
  if (!$gsap || !blogContainer.value) return

  $gsap.fromTo(".blog-card", 
    { 
      opacity: 0, 
      y: 40
    }, 
    {
      opacity: 1,
      y: 0,
      duration: 0.6,
      stagger: 0.15,
      ease: "power2.out",
      scrollTrigger: {
        trigger: blogContainer.value,
        start: "top 80%",
        toggleActions: "play none none none"
      }
    }
  )
}

onMounted(() => {
  if (!pending.value && posts.value.length > 0) {
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
/* Scoped custom styling helper if needed */
</style>
