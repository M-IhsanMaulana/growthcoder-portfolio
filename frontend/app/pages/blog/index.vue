<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-[#09090B] pb-20">

    <!-- ─── HERO HEADER SECTION ─────────────────────────────────────────── -->
    <div ref="heroSection" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-10 opacity-0 -translate-y-3">
      <div class="space-y-4 max-w-3xl">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/30 shadow-xs">
          <span class="w-1.5 h-1.5 rounded-full bg-brand-purple animate-pulse"></span>
          <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">BLOG</span>
        </div>

        <!-- Heading -->
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] text-zinc-900 dark:text-white">
          Artikel &amp; <span class="bg-gradient-to-r from-brand-purple to-brand-green bg-clip-text text-transparent">Insight</span>
        </h1>

        <!-- Divider -->
        <div class="w-20 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-sm"></div>

        <!-- Subtitle -->
        <p class="text-sm md:text-base text-zinc-500 dark:text-zinc-400 leading-relaxed font-light mt-2">
          Berbagi pengetahuan, pengalaman, dan insight seputar pengembangan web, teknologi, produktivitas, dan hal-hal yang saya pelajari.
        </p>
      </div>
    </div>

    <!-- ─── FILTER & SORT BAR SECTION ───────────────────────────────────── -->
    <div ref="filterSection" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 opacity-0 translate-y-3 relative z-30">
      <div class="flex flex-col lg:flex-row gap-6 lg:items-center justify-between pb-6 border-b border-zinc-200/50 dark:border-zinc-800/40">
        <!-- Dynamic Category Pills (Horizontal Scrollable) -->
        <div class="flex flex-nowrap items-center gap-2.5 overflow-x-auto pb-3 scrollbar-none w-full lg:w-auto flex-1 lg:flex-none pr-4">
          <button 
            @click="selectCategory('all')" 
            class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm whitespace-nowrap"
            :class="activeCategory === 'all' 
              ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0 font-bold' 
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800/80 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
          >
            Semua
          </button>
          <button 
            v-for="cat in categories" 
            :key="cat.slug"
            @click="selectCategory(cat.slug)" 
            class="px-5 py-2.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-300 cursor-pointer shadow-sm whitespace-nowrap"
            :class="activeCategory === cat.slug 
              ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white border-0 font-bold' 
              : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-800/80 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
          >
            {{ cat.name }}
          </button>
        </div>

        <!-- Sort selection dropdown -->
        <div class="flex items-center gap-3 w-full lg:w-auto justify-end flex-shrink-0">
          <UiSelectFilter 
            v-model="sortBy" 
            :options="sortOptions" 
            class="w-36 flex-shrink-0"
          />
        </div>
      </div>
    </div>

    <!-- ─── MAIN CONTENT & SIDEBAR GRID ─────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Blog Cards Grid (9 Columns) -->
        <div class="lg:col-span-8 xl:col-span-9 space-y-12">
          
          <!-- Loaded Posts -->
          <div 
            v-if="!pending && processedPosts.length" 
            ref="postsContainer"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
          >
            <Card 
              v-for="post in processedPosts" 
              :key="post.id"
              class="blog-card group flex flex-col hover:shadow-md border border-zinc-150/70 dark:border-zinc-900/60 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-300 relative overflow-hidden"
              :pt="{
                root: { class: '!bg-white dark:!bg-zinc-950 !border-0 !rounded-3xl !p-0 shadow-sm relative overflow-hidden flex flex-col' },
                body: { class: '!p-5 !flex-grow !flex !flex-col !justify-between min-h-[220px]' }
              }"
            >
              <template #header>
                <!-- Cover Image container -->
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
                  
                  <!-- Category Overlay Badge (Top-Left) -->
                  <span 
                    v-if="post.categories && post.categories.length" 
                    class="absolute top-3.5 left-3.5 px-3 py-1 bg-brand-purple text-[9px] font-extrabold tracking-wider text-white uppercase rounded-lg shadow-sm"
                    :class="getCategoryColorClass(post.categories[0].slug)"
                  >
                    {{ post.categories[0].name }}
                  </span>
                </div>
              </template>
              
              <template #content>
                <div class="space-y-2.5 flex-grow">
                  <!-- Title -->
                  <h4 class="text-base font-extrabold text-zinc-900 dark:text-white group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors leading-snug">
                    <NuxtLink :to="`/blog/${post.slug}`" class="cursor-pointer line-clamp-2">
                      {{ post.title }}
                    </NuxtLink>
                  </h4>
                  <!-- Description -->
                  <p class="text-zinc-500 dark:text-zinc-400 text-xs leading-relaxed line-clamp-2 font-normal">
                    {{ post.excerpt }}
                  </p>
                </div>
              </template>

              <template #footer>
                <!-- Card Footer (Author & Date Meta) -->
                <div class="flex items-center space-x-2.5 pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-4 flex-shrink-0">
                  <!-- Author Avatar -->
                  <NuxtImg 
                    :src="settings?.profile_photo?.urls?.thumbnail || settings?.profile_photo?.urls?.original || '/portrait.png'" 
                    :alt="authorName"
                    class="w-6 h-6 rounded-full object-cover border border-zinc-200 dark:border-zinc-800"
                    width="24"
                    height="24"
                    loading="lazy"
                  />
                  <!-- Author Info Details line -->
                  <div class="flex items-center space-x-1.5 text-[10px] font-semibold text-zinc-450 dark:text-zinc-500 whitespace-nowrap overflow-hidden">
                    <span class="text-zinc-750 dark:text-zinc-350 truncate font-bold max-w-[80px] sm:max-w-none">{{ authorName }}</span>
                    <span>•</span>
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span>•</span>
                    <span v-if="post.reading_time">{{ post.reading_time }} min read</span>
                  </div>
                </div>
              </template>
            </Card>
          </div>

          <!-- Skeleton Loading state -->
          <div v-else-if="pending" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <Card 
              v-for="i in 6" 
              :key="i"
              :pt="{
                root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm border border-zinc-150/70 dark:border-zinc-900/60 flex flex-col' },
                body: { class: '!p-5 !flex-grow !flex !flex-col min-h-[220px]' }
              }"
            >
              <template #header>
                <Skeleton class="w-full aspect-video rounded-t-3xl" />
              </template>
              
              <template #content>
                <div class="space-y-3 mt-2 flex-grow">
                  <Skeleton width="90%" height="1.25rem" class="mb-1" />
                  <Skeleton width="100%" height="0.875rem" />
                  <Skeleton width="80%" height="0.875rem" />
                </div>
              </template>
              
              <template #footer>
                <div class="flex items-center justify-between pt-4 border-t border-zinc-100 dark:border-zinc-900 mt-4">
                  <div class="flex items-center space-x-2">
                    <Skeleton shape="circle" size="1.5rem" />
                    <Skeleton width="4rem" height="0.75rem" />
                  </div>
                  <Skeleton width="2rem" height="0.75rem" />
                </div>
              </template>
            </Card>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-20 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
            <span class="text-5xl">💡</span>
            <h4 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 mt-4">Artikel tidak ditemukan</h4>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 max-w-md mx-auto">
              Tidak ada hasil untuk filter "{{ activeCategory === 'all' ? 'Semua' : activeCategory }}" atau kata kunci pencarian "{{ searchInput }}".
            </p>
          </div>

          <!-- Pagination Footer Controls -->
          <div v-if="!pending && lastPage > 1" class="flex items-center justify-center space-x-2 pt-6">
            <!-- Left Arrow Page Control -->
            <button 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="w-10 h-10 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-all disabled:opacity-55 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-zinc-900 cursor-pointer shadow-xs"
              aria-label="Previous Page"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>

            <!-- Numbered Controls -->
            <template v-for="(pageNum, index) in paginationPages" :key="index">
              <span 
                v-if="pageNum === '...'" 
                class="px-2 text-zinc-450 dark:text-zinc-600 font-bold"
              >
                ...
              </span>
              <button 
                v-else
                @click="goToPage(pageNum as number)"
                class="w-10 h-10 rounded-2xl text-xs font-bold transition-all shadow-xs cursor-pointer"
                :class="currentPage === pageNum
                  ? 'bg-gradient-to-tr from-purple-500 to-blue-500 text-white font-extrabold border-0'
                  : 'bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white'"
              >
                {{ pageNum }}
              </button>
            </template>

            <!-- Right Arrow Page Control -->
            <button 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === lastPage"
              class="w-10 h-10 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-all disabled:opacity-55 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-zinc-900 cursor-pointer shadow-xs"
              aria-label="Next Page"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>

        </div>

        <!-- Right: Sidebar Widgets (3 Columns xl / 4 Columns lg) -->
        <aside class="lg:col-span-4 xl:col-span-3 space-y-6">
          
          <!-- Widget 1: Search Box -->
          <div class="p-6 rounded-3xl bg-white dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-900/60 shadow-xs">
            <div class="relative w-full">
              <input 
                v-model="searchInput" 
                type="text" 
                placeholder="Cari artikel..." 
                class="w-full !pl-4 !pr-10 !py-3 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-semibold text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none focus:border-brand-purple focus:ring-1 focus:ring-brand-purple/30 transition-all duration-300 shadow-inner"
              />
              <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-zinc-450 dark:text-zinc-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
              </span>
            </div>
          </div>

          <!-- Widget 2: Kategori List -->
          <div class="p-6 rounded-3xl bg-white dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-900/60 shadow-xs space-y-4">
            <h5 class="text-sm font-extrabold text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
              </svg>
              Kategori
            </h5>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-900">
              <button 
                v-for="cat in categories" 
                :key="cat.slug"
                @click="selectCategory(cat.slug)"
                class="w-full py-3.5 flex items-center justify-between text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-brand-purple dark:hover:text-brand-green transition-colors cursor-pointer group"
              >
                <span class="flex items-center gap-2.5">
                  <!-- Folder icon colored dynamically -->
                  <svg class="w-4 h-4 text-brand-purple/70 group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                  </svg>
                  {{ cat.name }}
                </span>
                <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-500 text-[10px] font-bold group-hover:bg-brand-purple/10 group-hover:text-brand-purple dark:group-hover:bg-brand-green/10 dark:group-hover:text-brand-green transition-all">
                  {{ cat.posts_count ?? 0 }}
                </span>
              </button>
            </div>
          </div>

          <!-- Widget 3: Artikel Terbaru -->
          <div class="p-6 rounded-3xl bg-white dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-900/60 shadow-xs space-y-4">
            <h5 class="text-sm font-extrabold text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
              </svg>
              Artikel Terbaru
            </h5>
            <div class="space-y-4">
              <div 
                v-for="recent in recentPosts" 
                :key="recent.id"
                class="flex gap-3 group"
              >
                <!-- Small thumbnail -->
                <div class="flex-shrink-0 w-16 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg overflow-hidden relative">
                  <NuxtImg 
                    v-if="recent.cover_image?.urls?.thumbnail || recent.cover_image?.urls?.medium"
                    :src="recent.cover_image?.urls?.thumbnail || recent.cover_image?.urls?.medium" 
                    :alt="recent.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                  />
                  <div v-else class="w-full h-full bg-gradient-to-br from-brand-navy/10 to-brand-purple/15 flex items-center justify-center text-lg select-none">
                    💡
                  </div>
                </div>
                
                <!-- Text contents -->
                <div class="min-w-0 space-y-0.5">
                  <h6 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors leading-tight line-clamp-2">
                    <NuxtLink :to="`/blog/${recent.slug}`" class="cursor-pointer">
                      {{ recent.title }}
                    </NuxtLink>
                  </h6>
                  <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-semibold">{{ formatDate(recent.published_at) }}</span>
                </div>
              </div>
            </div>
          </div>



        </aside>

      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import UiSelectFilter from '~/components/ui/SelectFilter.vue'

definePageMeta({ layout: 'default' })

const route = useRoute()
const router = useRouter()
const { $gsap } = useNuxtApp()

// Load overall settings for profile pic and author name
const { settings, fetchSettings } = useSettings()
await fetchSettings()

useSeoMeta({
  title: 'Blog & Artikel',
  description: 'Kumpulan artikel, tutorial, tips, dan insight seputar pengembangan web, pemrograman, produktivitas, dan teknologi.',
  ogTitle: 'Blog & Artikel | growthcoder.id',
  ogDescription: 'Kumpulan artikel, tutorial, tips, dan insight seputar pengembangan web, pemrograman, produktivitas, dan teknologi.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/blog' }
  ]
})

// Local state matching query params for shareability
const currentPage = ref(Number(route.query.page) || 1)
const activeCategory = ref((route.query.category as string) || 'all')
const searchInput = ref((route.query.q as string) || '')
const searchQuery = ref((route.query.q as string) || '')
const sortBy = ref((route.query.sort as string) || 'latest')

const sortOptions = [
  { label: 'Terbaru', value: 'latest' },
  { label: 'Terlama', value: 'oldest' },
]

// DOM Refs for animations
const heroSection = ref<HTMLElement | null>(null)
const filterSection = ref<HTMLElement | null>(null)
const postsContainer = ref<HTMLElement | null>(null)

// API data fetches
const { data: categoriesResponse } = await useFetchAPI<any>('/blog-categories')
const categories = computed(() => categoriesResponse.value?.data || [])

const { data: recentPostsResponse } = await useFetchAPI<any>('/posts?per_page=4')
const recentPosts = computed(() => recentPostsResponse.value?.data || [])

// Dynamically construct paginated fetching endpoint
const postsUrl = computed(() => {
  const params = new URLSearchParams()
  params.append('page', currentPage.value.toString())
  params.append('per_page', '6') // Show 6 posts per page (2 rows of 3 columns)
  if (activeCategory.value !== 'all') {
    params.append('category', activeCategory.value)
  }
  if (searchQuery.value) {
    params.append('q', searchQuery.value)
  }
  return `/posts?${params.toString()}`
})

const { data: postsResponse, pending } = await useFetchAPI<any>(() => postsUrl.value)

// Extract variables
const posts = computed(() => postsResponse.value?.data || [])
const paginationMeta = computed(() => postsResponse.value?.meta || null)
const lastPage = computed(() => paginationMeta.value?.last_page || 1)

// Author name computed
const authorName = computed(() => {
  return settings.value?.owner_full_name || 'Muhammad Ihsan'
})

// Sort posts locally (latest/oldest)
const processedPosts = computed(() => {
  let list = [...posts.value]
  if (sortBy.value === 'latest') {
    list.sort((a, b) => {
      const dateA = a.published_at ? new Date(a.published_at).getTime() : 0
      const dateB = b.published_at ? new Date(b.published_at).getTime() : 0
      return dateB - dateA
    })
  } else if (sortBy.value === 'oldest') {
    list.sort((a, b) => {
      const dateA = a.published_at ? new Date(a.published_at).getTime() : 0
      const dateB = b.published_at ? new Date(b.published_at).getTime() : 0
      return dateA - dateB
    })
  }
  return list
})

// Search input debouncer watch
let searchTimeout: any = null
watch(searchInput, (newVal) => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    searchQuery.value = newVal.trim()
    currentPage.value = 1 // Reset back to page 1 on new search
  }, 450)
})

// Sync query variables with router query parameters for full URL shareability
watch([currentPage, activeCategory, searchQuery, sortBy], () => {
  const query: any = {}
  if (currentPage.value > 1) query.page = currentPage.value
  if (activeCategory.value !== 'all') query.category = activeCategory.value
  if (searchQuery.value) query.q = searchQuery.value
  if (sortBy.value !== 'latest') query.sort = sortBy.value

  router.push({ query })
})

// Support Back/Forward browser navigation by watching route query modifications
watch(() => route.query, (newQuery) => {
  currentPage.value = Number(newQuery.page) || 1
  activeCategory.value = (newQuery.category as string) || 'all'
  searchInput.value = (newQuery.q as string) || ''
  searchQuery.value = (newQuery.q as string) || ''
  sortBy.value = (newQuery.sort as string) || 'latest'
}, { deep: true })

// Helper category color generator mapping
const getCategoryColorClass = (slug: string) => {
  const colorMap: Record<string, string> = {
    'web-development': '!bg-purple-600 !text-white',
    'laravel': '!bg-emerald-600 !text-white',
    'javascript': '!bg-amber-500 !text-black',
    'ui-ux': '!bg-pink-600 !text-white',
    'productivity': '!bg-cyan-600 !text-white',
    'tutorial': '!bg-indigo-600 !text-white'
  }
  return colorMap[slug] || '!bg-zinc-700 !text-white'
}

// Format Date in standard Indonesian format
const formatDate = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(date)
}

// Generate array lists for Pagination display logic
const paginationPages = computed(() => {
  const total = lastPage.value
  const current = currentPage.value
  const pages: (number | string)[] = []

  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)

    if (current > 3) {
      pages.push('...')
    }

    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)

    for (let i = start; i <= end; i++) {
      if (!pages.includes(i)) pages.push(i)
    }

    if (current < total - 2) {
      pages.push('...')
    }

    if (!pages.includes(total)) pages.push(total)
  }
  return pages
})

// Trigger page shifts
const goToPage = (page: number) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  scrollToTop()
}

// Selection triggers category parameter shifts
const selectCategory = (categorySlug: string) => {
  if (activeCategory.value === categorySlug) return

  if (!$gsap) {
    activeCategory.value = categorySlug
    currentPage.value = 1
    return
  }

  // Fade out cards before category switch
  $gsap.to(".blog-card", {
    opacity: 0,
    y: 16,
    duration: 0.2,
    stagger: 0.04,
    onComplete: () => {
      activeCategory.value = categorySlug
      currentPage.value = 1
      nextTick(() => {
        $gsap.fromTo(".blog-card", 
          { opacity: 0, y: 16 },
          { opacity: 1, y: 0, duration: 0.35, stagger: 0.05, ease: "power2.out" }
        )
      })
    }
  })
}

// Scroll layout viewport back to top on page adjustments
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}



// Entrance Animations GSAP config
const initAnimations = () => {
  if (!$gsap) return

  if (heroSection.value) {
    $gsap.to(heroSection.value, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' })
  }

  if (filterSection.value) {
    $gsap.to(filterSection.value, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out', delay: 0.12 })
  }

  if (postsContainer.value) {
    $gsap.fromTo(".blog-card", 
      { opacity: 0, y: 32 },
      {
        opacity: 1,
        y: 0,
        duration: 0.5,
        stagger: 0.08,
        ease: "power2.out",
        scrollTrigger: {
          trigger: postsContainer.value,
          start: "top 85%",
          toggleActions: "play none none none"
        }
      }
    )
  }
}

onMounted(() => {
  nextTick(() => initAnimations())
})

watch(pending, (isPending) => {
  if (!isPending) {
    nextTick(() => initAnimations())
  }
})
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
