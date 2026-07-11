<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-[#09090B] pb-20">

    <!-- ─── BREADCRUMBS & NAVIGATION HEADER ─────────────────────────────── -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
      <div class="flex items-center space-x-4">
        <!-- Back Button -->
        <NuxtLink 
          to="/blog" 
          class="w-10 h-10 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-all shadow-xs"
          title="Kembali ke Blog"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
        </NuxtLink>

        <!-- Breadcrumb links -->
        <nav class="flex items-center space-x-2 text-[10px] sm:text-xs font-semibold text-zinc-400 dark:text-zinc-500 whitespace-nowrap overflow-x-auto scrollbar-none py-1">
          <NuxtLink to="/" class="hover:text-brand-purple dark:hover:text-indigo-400">Home</NuxtLink>
          <span>/</span>
          <NuxtLink to="/blog" class="hover:text-brand-purple dark:hover:text-indigo-400">Blog</NuxtLink>
          <template v-if="post?.categories && post.categories.length">
            <span>/</span>
            <NuxtLink :to="`/blog/kategori/${post.categories[0].slug}`" class="hover:text-brand-purple dark:hover:text-indigo-400 truncate max-w-[80px] sm:max-w-none">{{ post.categories[0].name }}</NuxtLink>
          </template>
          <span>/</span>
          <span class="text-zinc-600 dark:text-zinc-350 truncate max-w-[120px] sm:max-w-[200px]">{{ post?.title }}</span>
        </nav>
      </div>
    </div>

    <!-- ─── ARTICLE LAYOUT GRID ────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Article Body Content (8/9 Columns) -->
        <article class="lg:col-span-8 xl:col-span-9 space-y-12">
          <!-- Loaded Content -->
          <div v-if="!pending && post" class="space-y-10">
            <!-- Article Title, Category Badge & Meta Section (Moved inside grid to align Table of Contents at the top) -->
            <div class="space-y-5">
              <!-- Category Badge -->
              <NuxtLink 
                v-if="post?.categories && post.categories.length" 
                :to="`/blog/kategori/${post.categories[0].slug}`"
                class="inline-flex items-center px-3.5 py-1 rounded-lg text-[9px] font-extrabold tracking-wider text-white uppercase shadow-xs hover:opacity-90 transition-opacity cursor-pointer"
                :class="getCategoryColorClass(post.categories[0].slug)"
              >
                {{ post.categories[0].name }}
              </NuxtLink>

              <!-- Main Title -->
              <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight leading-[1.15] text-zinc-900 dark:text-white">
                {{ post?.title }}
              </h1>

              <!-- Excerpt description -->
              <p class="text-sm sm:text-base text-zinc-500 dark:text-zinc-400 font-light leading-relaxed">
                {{ post?.excerpt }}
              </p>

              <!-- Meta Author & Share Line -->
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-b border-zinc-200/50 dark:border-zinc-800/40 py-4 mt-6">
                <!-- Left: Author portrait and details -->
                <div class="flex items-center space-x-3" v-if="post">
                  <NuxtImg 
                    :src="settings?.profile_photo?.urls?.thumbnail || settings?.profile_photo?.urls?.original || '/portrait.png'" 
                    :alt="authorName"
                    class="w-8 h-8 rounded-full object-cover border border-zinc-200 dark:border-zinc-800"
                    width="32"
                    height="32"
                    loading="lazy"
                  />
                  <div class="flex items-center space-x-2 text-[10px] sm:text-xs font-semibold text-zinc-400 dark:text-zinc-500">
                    <span class="text-zinc-800 dark:text-zinc-300 font-bold">{{ authorName }}</span>
                    <span>•</span>
                    <span>{{ formatDate(post.published_at) }}</span>
                    <span>•</span>
                    <span>{{ post.reading_time }} min read</span>
                    <span>•</span>
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                      {{ formatViews(post.views_count) }}
                    </span>
                  </div>
                </div>

                <!-- Right: Share Controls -->
                <div class="flex items-center gap-1.5 flex-wrap">
                  <!-- Share X / Twitter -->
                  <button 
                    @click="shareTwitter"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke X"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                  </button>

                  <!-- Share LinkedIn -->
                  <button 
                    @click="shareLinkedIn"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-[#0A66C2] dark:hover:text-[#0A66C2] hover:border-[#0A66C2]/40 dark:hover:border-[#0A66C2]/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke LinkedIn"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0z"/>
                    </svg>
                  </button>

                  <!-- Share Facebook -->
                  <button 
                    @click="shareFacebook"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-[#1877F2] dark:hover:text-[#1877F2] hover:border-[#1877F2]/40 dark:hover:border-[#1877F2]/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke Facebook"
                  >
                    <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                    </svg>
                  </button>

                  <!-- Share Telegram -->
                  <button 
                    @click="shareTelegram"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-[#26A5E4] dark:hover:text-[#26A5E4] hover:border-[#26A5E4]/40 dark:hover:border-[#26A5E4]/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke Telegram"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/>
                    </svg>
                  </button>

                  <!-- Share WhatsApp -->
                  <button 
                    @click="shareWhatsApp"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-[#25D366] dark:hover:text-[#25D366] hover:border-[#25D366]/40 dark:hover:border-[#25D366]/40 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke WhatsApp"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.725 1.45 5.503 0 9.98-4.47 9.985-9.967.002-2.662-1.032-5.166-2.91-7.046C16.516 1.71 14.02 .675 11.362.675c-5.504 0-9.983 4.471-9.988 9.97-.001 1.953.51 3.856 1.48 5.568L1.87 20.3l4.777-1.253v.107zM17.586 14.5c-.305-.153-1.805-.89-2.083-.99-.278-.103-.48-.153-.68.15-.2.303-.775.99-.95 1.19-.175.2-.35.227-.655.075-1.205-.603-2.03-1.01-2.836-2.395-.21-.362.21-.336.6-.113.35.2.78.9 1.03 1.13.25.22.25.37.12.63-.13.25-.63.99-.77 1.14-.14.15-.28.17-.58.02-3.13-1.56-4.14-5.36-4.18-5.55-.04-.2.13-.37.28-.52.13-.14.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.68-1.64-.93-2.25-.24-.58-.49-.5-.68-.51h-.58c-.2 0-.53.07-.8.37-.28.3-1.07 1.05-1.07 2.56s1.09 2.97 1.24 3.17c.15.2 2.15 3.28 5.21 4.6 3.06 1.32 3.06.88 3.61.83.55-.05 1.8-.73 2.05-1.44.25-.7.25-1.31.18-1.44-.07-.13-.27-.2-.58-.35z"/>
                    </svg>
                  </button>

                  <!-- Copy Link -->
                  <button 
                    @click="copyLink"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center transition-all cursor-pointer shadow-xs"
                    :class="copySuccess 
                      ? 'text-emerald-500 hover:text-emerald-600 dark:text-emerald-400 border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-950/20' 
                      : 'text-zinc-500 dark:text-zinc-400 hover:text-brand-purple dark:hover:text-indigo-400 hover:border-brand-purple/40 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                    title="Salin Tautan"
                  >
                    <svg v-if="!copySuccess" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <!-- Check icon on success -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Featured Cover Image -->
            <div class="w-full aspect-[21/9] rounded-3xl bg-zinc-200 dark:bg-zinc-900 overflow-hidden relative shadow-md">
              <NuxtImg 
                v-if="post?.cover_image?.urls?.original"
                :src="post.cover_image.urls.original" 
                :alt="post?.title"
                class="w-full h-full object-cover"
                preload
                fetchpriority="high"
              />
              <div v-else class="w-full h-full bg-gradient-to-br from-brand-navy/15 to-brand-purple/20 flex items-center justify-center text-6xl">
                💡
              </div>
            </div>

            <!-- Table of Contents (Moved below Cover Image) -->
            <div 
              v-if="tocItems && tocItems.length" 
              class="p-6 sm:p-8 rounded-3xl bg-zinc-50/50 dark:bg-zinc-950/40 border border-zinc-200/60 dark:border-zinc-900/60 shadow-xs space-y-4"
            >
              <h5 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-purple dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                Daftar Isi
              </h5>
              
              <UiTocNode :items="tocItems" :active-id="activeHeadingId" />
            </div>

            <!-- Article Body (v-html rich content) -->
            <div 
              class="rich-text-content prose dark:prose-invert prose-zinc text-zinc-700 dark:text-zinc-350 leading-relaxed text-sm sm:text-base max-w-none space-y-6"
              v-html="processedContent"
            ></div>

            <!-- Share widget block -->
            <div class="flex flex-wrap items-center gap-2 pt-6 border-t border-zinc-200/20 dark:border-zinc-800">
              <span class="text-xs font-extrabold text-zinc-450 dark:text-zinc-500 uppercase tracking-wider mr-1.5">Bagikan:</span>
              
              <!-- Share X / Twitter -->
              <button @click="shareTwitter" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span>X</span>
              </button>

              <!-- Share LinkedIn -->
              <button @click="shareLinkedIn" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-[#0A66C2] hover:border-[#0A66C2]/30 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0z"/>
                </svg>
                <span>LinkedIn</span>
              </button>

              <!-- Share Facebook -->
              <button @click="shareFacebook" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-[#1877F2] hover:border-[#1877F2]/30 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                </svg>
                <span>Facebook</span>
              </button>

              <!-- Share Telegram -->
              <button @click="shareTelegram" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-[#26A5E4] hover:border-[#26A5E4]/30 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-[#26A5E4]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/>
                </svg>
                <span>Telegram</span>
              </button>

              <!-- Share WhatsApp -->
              <button @click="shareWhatsApp" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-[#25D366] hover:border-[#25D366]/30 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.725 1.45 5.503 0 9.98-4.47 9.985-9.967.002-2.662-1.032-5.166-2.91-7.046C16.516 1.71 14.02 .675 11.362.675c-5.504 0-9.983 4.471-9.988 9.97-.001 1.953.51 3.856 1.48 5.568L1.87 20.3l4.777-1.253v.107zM17.586 14.5c-.305-.153-1.805-.89-2.083-.99-.278-.103-.48-.153-.68.15-.2.303-.775.99-.95 1.19-.175.2-.35.227-.655.075-1.205-.603-2.03-1.01-2.836-2.395-.21-.362.21-.336.6-.113.35.2.78.9 1.03 1.13.25.22.25.37.12.63-.13.25-.63.99-.77 1.14-.14.15-.28.17-.58.02-3.13-1.56-4.14-5.36-4.18-5.55-.04-.2.13-.37.28-.52.13-.14.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.68-1.64-.93-2.25-.24-.58-.49-.5-.68-.51h-.58c-.2 0-.53.07-.8.37-.28.3-1.07 1.05-1.07 2.56s1.09 2.97 1.24 3.17c.15.2 2.15 3.28 5.21 4.6 3.06 1.32 3.06.88 3.61.83.55-.05 1.8-.73 2.05-1.44.25-.7.25-1.31.18-1.44-.07-.13-.27-.2-.58-.35z"/>
                </svg>
                <span>WhatsApp</span>
              </button>

              <!-- Salin Tautan -->
              <button @click="copyLink" :class="copySuccess ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' : 'border-zinc-200/80 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:border-brand-purple/40'" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border text-[11px] font-semibold flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg v-if="!copySuccess" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ copySuccess ? 'Disalin' : 'Salin Tautan' }}</span>
              </button>
            </div>

            <!-- ─── PREVIOUS / NEXT ARROW CONTROL BARS ─────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-10 border-t border-zinc-200/50 dark:border-zinc-800/40">
              <!-- Left navigation card (Previous Post) -->
              <NuxtLink 
                v-if="post.previous_post"
                :to="`/blog/${post.previous_post.slug}`"
                class="p-5 rounded-2xl bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-900/80 hover:border-brand-purple dark:hover:border-brand-green flex flex-col items-start gap-1 group transition-all"
              >
                <span class="text-[10px] font-bold text-brand-purple dark:text-indigo-400 uppercase tracking-wide flex items-center gap-1">
                  <svg class="w-3 h-3 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                  </svg>
                  Artikel Sebelumnya
                </span>
                <span class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-white leading-snug group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors truncate max-w-full">
                  {{ post.previous_post.title }}
                </span>
              </NuxtLink>
              <div v-else class="hidden sm:block"></div>

              <!-- Right navigation card (Next Post) -->
              <NuxtLink 
                v-if="post.next_post"
                :to="`/blog/${post.next_post.slug}`"
                class="p-5 rounded-2xl bg-white dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-900/80 hover:border-brand-purple dark:hover:border-brand-green flex flex-col items-end gap-1 group text-right transition-all"
              >
                <span class="text-[10px] font-bold text-brand-purple dark:text-indigo-400 uppercase tracking-wide flex items-center gap-1">
                  Artikel Selanjutnya
                  <svg class="w-3 h-3 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                  </svg>
                </span>
                <span class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-white leading-snug group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors truncate max-w-full">
                  {{ post.next_post.title }}
                </span>
              </NuxtLink>
            </div>

          </div>

          <!-- Pending / Skeleton State -->
          <div v-else class="space-y-6">
            <Skeleton width="100%" height="2rem" />
            <Skeleton width="95%" height="1.25rem" />
            <div class="space-y-2 pt-4">
              <Skeleton width="100%" height="1rem" />
              <Skeleton width="100%" height="1rem" />
              <Skeleton width="90%" height="1rem" />
              <Skeleton width="100%" height="1rem" />
              <Skeleton width="85%" height="1rem" />
            </div>
          </div>
        </article>

        <!-- Right: Sidebar widgets (4 columns lg / 3 columns xl) -->
        <aside class="lg:col-span-4 xl:col-span-3 space-y-6 lg:sticky lg:top-24 self-start">
          
          <!-- Widget 2: Bagikan Card -->
          <div class="p-6 rounded-3xl bg-white dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-900/60 shadow-md space-y-4">
            <h5 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-widest">
              Bagikan Artikel
            </h5>
            <div class="grid grid-cols-2 gap-2">
              <!-- X (Twitter) -->
              <button 
                @click="shareTwitter" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-750 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span>X</span>
              </button>

              <!-- LinkedIn -->
              <button 
                @click="shareLinkedIn" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-750 dark:text-zinc-300 hover:text-[#0A66C2] dark:hover:text-[#0A66C2] transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0z"/>
                </svg>
                <span>LinkedIn</span>
              </button>

              <!-- Facebook -->
              <button 
                @click="shareFacebook" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-750 dark:text-zinc-300 hover:text-[#1877F2] dark:hover:text-[#1877F2] transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c4.56-.93 8-4.96 8-9.75z"/>
                </svg>
                <span>Facebook</span>
              </button>

              <!-- Telegram -->
              <button 
                @click="shareTelegram" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-750 dark:text-zinc-300 hover:text-[#26A5E4] dark:hover:text-[#26A5E4] transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5 text-[#26A5E4]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .24z"/>
                </svg>
                <span>Telegram</span>
              </button>

              <!-- WhatsApp -->
              <button 
                @click="shareWhatsApp" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-750 dark:text-zinc-300 hover:text-[#25D366] dark:hover:text-[#25D366] transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.725 1.45 5.503 0 9.98-4.47 9.985-9.967.002-2.662-1.032-5.166-2.91-7.046C16.516 1.71 14.02 .675 11.362.675c-5.504 0-9.983 4.471-9.988 9.97-.001 1.953.51 3.856 1.48 5.568L1.87 20.3l4.777-1.253v.107zM17.586 14.5c-.305-.153-1.805-.89-2.083-.99-.278-.103-.48-.153-.68.15-.2.303-.775.99-.95 1.19-.175.2-.35.227-.655.075-1.205-.603-2.03-1.01-2.836-2.395-.21-.362.21-.336.6-.113.35.2.78.9 1.03 1.13.25.22.25.37.12.63-.13.25-.63.99-.77 1.14-.14.15-.28.17-.58.02-3.13-1.56-4.14-5.36-4.18-5.55-.04-.2.13-.37.28-.52.13-.14.3-.35.45-.53.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.68-1.64-.93-2.25-.24-.58-.49-.5-.68-.51h-.58c-.2 0-.53.07-.8.37-.28.3-1.07 1.05-1.07 2.56s1.09 2.97 1.24 3.17c.15.2 2.15 3.28 5.21 4.6 3.06 1.32 3.06.88 3.61.83.55-.05 1.8-.73 2.05-1.44.25-.7.25-1.31.18-1.44-.07-.13-.27-.2-.58-.35z"/>
                </svg>
                <span>WhatsApp</span>
              </button>

              <!-- Salin Link -->
              <button 
                @click="copyLink" 
                :class="copySuccess 
                  ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' 
                  : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-zinc-750 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400'"
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border text-xs font-bold transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg v-if="!copySuccess" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ copySuccess ? 'Disalin' : 'Salin Tautan' }}</span>
              </button>
            </div>
          </div>

          <!-- Widget 3: Artikel Terkait -->
          <div 
            v-if="post?.related_posts && post.related_posts.length" 
            class="p-6 rounded-3xl bg-white dark:bg-zinc-950 border border-zinc-200/60 dark:border-zinc-900/60 shadow-xs space-y-4"
          >
            <h5 class="text-sm font-extrabold text-zinc-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4 text-brand-purple" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
              </svg>
              Artikel Terkait
            </h5>
            <div class="space-y-4">
              <div 
                v-for="related in post.related_posts" 
                :key="related.id"
                class="flex gap-3 group"
              >
                <!-- Image -->
                <div class="flex-shrink-0 w-16 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg overflow-hidden relative">
                  <NuxtImg 
                    v-if="related.cover_image?.urls?.thumbnail || related.cover_image?.urls?.medium"
                    :src="related.cover_image?.urls?.thumbnail || related.cover_image?.urls?.medium" 
                    :alt="related.title"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                  />
                  <div v-else class="w-full h-full bg-gradient-to-br from-brand-navy/10 to-brand-purple/15 flex items-center justify-center text-lg select-none">
                    💡
                  </div>
                </div>

                <!-- Title & Date -->
                <div class="min-w-0 space-y-0.5">
                  <h6 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-brand-purple dark:group-hover:text-brand-green transition-colors leading-tight line-clamp-2">
                    <NuxtLink :to="`/blog/${related.slug}`" class="cursor-pointer">
                      {{ related.title }}
                    </NuxtLink>
                  </h6>
                  <span class="text-[9px] text-zinc-400 dark:text-zinc-500 font-semibold">{{ formatDate(related.published_at) }}</span>
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
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'

definePageMeta({ layout: 'default' })

const route = useRoute()
const { $gsap } = useNuxtApp()

// Load overall settings for profile pic and author name
const { settings, fetchSettings } = useSettings()
await fetchSettings()

// Fetch post detail from API using slug parameter
const { data: response, pending } = await useFetchAPI<any>(() => `/posts/${route.params.slug}`)
const post = computed(() => response.value?.data || null)

// Author name
const authorName = computed(() => {
  return settings.value?.owner_full_name || 'Muhammad Ihsan'
})

useSeoMeta({
  title: () => post.value?.meta_title || post.value?.title || 'Detail Artikel',
  description: () => post.value?.meta_description || post.value?.excerpt || 'Detail Artikel',
  ogTitle: () => post.value?.meta_title || post.value?.title || 'Detail Artikel',
  ogDescription: () => post.value?.meta_description || post.value?.excerpt || 'Detail Artikel',
  ogImage: () => post.value?.cover_image?.urls?.medium || post.value?.cover_image?.urls?.original || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'article',
  twitterCard: 'summary_large_image',
})

useHead(() => {
  if (!post.value) return {}
  return {
    link: [
      { rel: 'canonical', href: `https://growthcoder.id/blog/${post.value.slug}` }
    ],
    script: [
      {
        type: 'application/ld+json',
        innerHTML: JSON.stringify({
          '@context': 'https://schema.org',
          '@type': 'BlogPosting',
          'headline': post.value.title,
          'description': post.value.excerpt,
          'image': post.value.cover_image?.urls?.original || post.value.cover_image?.urls?.medium,
          'datePublished': post.value.published_at,
          'dateModified': post.value.updated_at || post.value.published_at,
          'author': {
            '@type': 'Person',
            'name': authorName.value,
            'url': 'https://growthcoder.id'
          },
          'publisher': {
            '@type': 'Organization',
            'name': settings.value?.site_name || 'growthcoder.id',
            'logo': {
              '@type': 'ImageObject',
              'url': 'https://growthcoder.id/logo-gc-dark.png'
            }
          },
          'mainEntityOfPage': {
            '@type': 'WebPage',
            '@id': `https://growthcoder.id/blog/${post.value.slug}`
          }
        })
      }
    ]
  }
})


// Share Copy states
const copySuccess = ref(false)

// Content & TOC processing states
const processedContent = ref('')
const tocItems = ref<any[]>([])
const activeHeadingId = ref('')

// Scrollspy IntersectionObserver reference
let scrollObserver: IntersectionObserver | null = null

// Category class mapping
const getCategoryColorClass = (slug: string) => {
  const colorMap: Record<string, string> = {
    'web-development': '!bg-purple-650 !text-white',
    'laravel': '!bg-emerald-600 !text-white',
    'javascript': '!bg-amber-500 !text-black',
    'ui-ux': '!bg-pink-650 !text-white',
    'productivity': '!bg-cyan-600 !text-white',
    'tutorial': '!bg-indigo-600 !text-white'
  }
  return colorMap[slug] || '!bg-zinc-700 !text-white'
}

// Date formatter
const formatDate = (dateString: string) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }).format(date)
}

// Views count formatter
const formatViews = (views: number) => {
  if (!views) return '0 views'
  if (views >= 1000) {
    return `${(views / 1000).toFixed(1)}K views`
  }
  return `${views} views`
}

// Parse headings from the content and generate structural TOC
// Must only run on client-side because DOMParser is a browser-only API
const parseTocAndContent = () => {
  if (!process.client) return
  if (!post.value?.content) return

  const parser = new DOMParser()
  const doc = parser.parseFromString(post.value.content, 'text/html')
  const headings = doc.querySelectorAll('h2, h3')

  const items: any[] = []
  headings.forEach((heading, idx) => {
    const text = heading.textContent || ''
    // Generate slugified ID
    const slug = text.toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '')
    const id = heading.id || `section-${idx}-${slug}`
    
    heading.setAttribute('id', id)
    heading.classList.add('scroll-mt-24') // Navbar padding scroll offset

    items.push({
      id,
      text,
      level: parseInt(heading.tagName.substring(1)), // h2 -> 2, h3 -> 3
      children: []
    })
  })

  // Structure H3 headings as children of previous H2 heading (Recursive concept)
  const tree: any[] = []
  let currentH2: any = null

  items.forEach(item => {
    if (item.level === 2) {
      currentH2 = { ...item, children: [] }
      tree.push(currentH2)
    } else if (item.level === 3) {
      if (currentH2) {
        currentH2.children.push({ ...item })
      } else {
        tree.push({ ...item }) // h3 without parent fallback
      }
    }
  })

  tocItems.value = tree
  processedContent.value = doc.body.innerHTML
}

// Observe scroll to track active heading (Scrollspy)
const initScrollspy = () => {
  if (scrollObserver) scrollObserver.disconnect()

  scrollObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        activeHeadingId.value = entry.target.id
      }
    })
  }, {
    rootMargin: '-90px 0px -55% 0px' // Offset triggers active state just above center
  })

  const headings = document.querySelectorAll('.rich-text-content h2, .rich-text-content h3')
  headings.forEach(h => scrollObserver?.observe(h))
}

// Watching details change (no immediate: only runs on client after mount)
watch(() => post.value, (newPost) => {
  if (newPost) {
    parseTocAndContent()
  }
})

// Watching parsed html to set scrollspy
watch(processedContent, () => {
  nextTick(() => {
    initScrollspy()
  })
})

// Social Sharing utilities
const shareUrl = computed(() => {
  if (process.client) return window.location.href
  return ''
})

const shareTwitter = () => {
  if (!post.value) return
  const text = encodeURIComponent(`Baca artikel: "${post.value.title}"`)
  const url = encodeURIComponent(shareUrl.value)
  window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank')
}

const shareLinkedIn = () => {
  if (!post.value) return
  const url = encodeURIComponent(shareUrl.value)
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank')
}

const shareFacebook = () => {
  if (!post.value) return
  const url = encodeURIComponent(shareUrl.value)
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
}

const shareTelegram = () => {
  if (!post.value) return
  const text = encodeURIComponent(`Baca artikel: "${post.value.title}"`)
  const url = encodeURIComponent(shareUrl.value)
  window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank')
}

const shareWhatsApp = () => {
  if (!post.value) return
  const text = encodeURIComponent(`Baca artikel: "${post.value.title}"`)
  const url = encodeURIComponent(shareUrl.value)
  window.open(`https://api.whatsapp.com/send?text=${text}%20${url}`, '_blank')
}

const copyLink = () => {
  if (process.client) {
    navigator.clipboard.writeText(shareUrl.value)
    copySuccess.value = true
    setTimeout(() => {
      copySuccess.value = false
    }, 2500)
  }
}



// Record blog post view on client side (using document.referrer to ensure accuracy)
const recordPostView = (slug: string) => {
  if (!process.client) return
  if (!slug) return

  const config = useRuntimeConfig()
  $fetch(`/posts/${slug}/view`, {
    baseURL: config.public.apiBase,
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'X-API-Key': config.public.apiKey,
    },
    body: {
      referrer: document.referrer || '',
    }
  }).then((res: any) => {
    if (res?.success && post.value && post.value.slug === slug) {
      post.value.views_count = res.views_count
    }
  }).catch(err => {
    console.error('Failed to record blog view:', err)
  })
}

// Watch slug to trigger view tracking on client-side SPA navigation
watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    recordPostView(newSlug as string)
  }
})

// Entrance GSAP animation setup + initial TOC parse on client
onMounted(() => {
  // Parse TOC on first mount (handles page refresh / direct URL visit)
  parseTocAndContent()

  // Record view on initial mount
  if (route.params.slug) {
    recordPostView(route.params.slug as string)
  }

  if (!$gsap) return
  $gsap.fromTo('.lucide-arrow-left, nav span, nav a', 
    { opacity: 0, x: -8 },
    { opacity: 1, x: 0, duration: 0.4, stagger: 0.05, ease: 'power2.out' }
  )
})

onUnmounted(() => {
  if (scrollObserver) scrollObserver.disconnect()
})
</script>

<style>
@reference "../../assets/css/main.css";

/* Style adjustments for Prose content */
.rich-text-content h2 {
  @apply text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-10 mb-4 tracking-tight scroll-mt-24;
}
.rich-text-content h3 {
  @apply text-base sm:text-lg font-extrabold text-zinc-900 dark:text-white mt-6 mb-3 tracking-tight scroll-mt-24;
}
.rich-text-content p {
  @apply text-sm sm:text-base text-zinc-600 dark:text-zinc-400 font-light leading-relaxed mb-5;
}
.rich-text-content ul {
  @apply list-disc pl-5 mb-5 space-y-1.5 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 font-light;
}
.rich-text-content ol {
  @apply list-decimal pl-5 mb-5 space-y-1.5 text-sm sm:text-base text-zinc-600 dark:text-zinc-400 font-light;
}
.rich-text-content code {
  @apply text-xs bg-zinc-100 dark:bg-zinc-900 px-1.5 py-0.5 rounded-md font-mono text-pink-600 dark:text-pink-400 border border-zinc-200/50 dark:border-zinc-800/80;
}
.rich-text-content pre {
  @apply p-4 rounded-2xl bg-zinc-950 dark:bg-zinc-900/60 border border-zinc-900 dark:border-zinc-800 text-xs font-mono text-zinc-200 overflow-x-auto mb-6 shadow-inner;
}
.rich-text-content pre code {
  @apply p-0 bg-transparent rounded-none border-0 text-zinc-200 font-normal;
}
.rich-text-content blockquote {
  @apply pl-4 border-l-4 border-brand-purple dark:border-indigo-400 italic text-zinc-500 dark:text-zinc-500 my-6;
}
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
