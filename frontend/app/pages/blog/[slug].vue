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
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 640">
                      <path d="M453.2 112L523.8 112L369.6 288.2L551 528L409 528L297.7 382.6L170.5 528L99.8 528L264.7 339.5L90.8 112L236.4 112L336.9 244.9L453.2 112zM428.4 485.8L467.5 485.8L215.1 152L173.1 152L428.4 485.8z"/>
                    </svg>
                  </button>

                  <!-- Share LinkedIn -->
                  <button 
                    @click="shareLinkedIn"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke LinkedIn"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 640">
                      <path d="M512 96L127.9 96C110.3 96 96 110.5 96 128.3L96 511.7C96 529.5 110.3 544 127.9 544L512 544C529.6 544 544 529.5 544 511.7L544 128.3C544 110.5 529.6 96 512 96zM231.4 480L165 480L165 266.2L231.5 266.2L231.5 480L231.4 480zM198.2 160C219.5 160 236.7 177.2 236.7 198.5C236.7 219.8 219.5 237 198.2 237C176.9 237 159.7 219.8 159.7 198.5C159.7 177.2 176.9 160 198.2 160zM480.3 480L413.9 480L413.9 376C413.9 351.2 413.4 319.3 379.4 319.3C344.8 319.3 339.5 346.3 339.5 374.2L339.5 480L273.1 480L273.1 266.2L336.8 266.2L336.8 295.4L337.7 295.4C346.6 278.6 368.3 260.9 400.6 260.9C467.8 260.9 480.3 305.2 480.3 362.8L480.3 480z"/>
                    </svg>
                  </button>

                  <!-- Share Facebook -->
                  <button 
                    @click="shareFacebook"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke Facebook"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 640">
                      <path d="M576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 440 146.7 540.8 258.2 568.5L258.2 398.2L205.4 398.2L205.4 320L258.2 320L258.2 286.3C258.2 199.2 297.6 158.8 383.2 158.8C399.4 158.8 427.4 162 438.9 165.2L438.9 236C432.9 235.4 422.4 235 409.3 235C367.3 235 351.1 250.9 351.1 292.2L351.1 320L434.7 320L420.3 398.2L351 398.2L351 574.1C477.8 558.8 576 450.9 576 320z"/>
                    </svg>
                  </button>

                  <!-- Share Telegram -->
                  <button 
                    @click="shareTelegram"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke Telegram"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 640">
                      <path d="M320 72C183 72 72 183 72 320C72 457 183 568 320 568C457 568 568 457 568 320C568 183 457 72 320 72zM435 240.7C431.3 279.9 415.1 375.1 406.9 419C403.4 437.6 396.6 443.8 390 444.4C375.6 445.7 364.7 434.9 350.7 425.7C328.9 411.4 316.5 402.5 295.4 388.5C270.9 372.4 286.8 363.5 300.7 349C304.4 345.2 367.8 287.5 369 282.3C369.2 281.6 369.3 279.2 367.8 277.9C366.3 276.6 364.2 277.1 362.7 277.4C360.5 277.9 325.6 300.9 258.1 346.5C248.2 353.3 239.2 356.6 231.2 356.4C222.3 356.2 205.3 351.4 192.6 347.3C177.1 342.3 164.7 339.6 165.8 331C166.4 326.5 172.5 322 184.2 317.3C256.5 285.8 304.7 265 328.8 255C397.7 226.4 412 221.4 421.3 221.2C423.4 221.2 427.9 221.7 430.9 224.1C432.9 225.8 434.1 228.2 434.4 230.8C434.9 234 435 237.3 434.8 240.6z"/>
                    </svg>
                  </button>

                  <!-- Share WhatsApp -->
                  <button 
                    @click="shareWhatsApp"
                    class="w-9 h-9 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-400 hover:text-black dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all cursor-pointer shadow-xs hover:scale-105 active:scale-95"
                    title="Bagikan ke WhatsApp"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 640 640">
                      <path d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z"/>
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
              <button @click="shareTwitter" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]" title="Bagikan ke X">
                <svg class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M453.2 112L523.8 112L369.6 288.2L551 528L409 528L297.7 382.6L170.5 528L99.8 528L264.7 339.5L90.8 112L236.4 112L336.9 244.9L453.2 112zM428.4 485.8L467.5 485.8L215.1 152L173.1 152L428.4 485.8z"/>
                </svg>
              </button>

              <!-- Share LinkedIn -->
              <button @click="shareLinkedIn" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M512 96L127.9 96C110.3 96 96 110.5 96 128.3L96 511.7C96 529.5 110.3 544 127.9 544L512 544C529.6 544 544 529.5 544 511.7L544 128.3C544 110.5 529.6 96 512 96zM231.4 480L165 480L165 266.2L231.5 266.2L231.5 480L231.4 480zM198.2 160C219.5 160 236.7 177.2 236.7 198.5C236.7 219.8 219.5 237 198.2 237C176.9 237 159.7 219.8 159.7 198.5C159.7 177.2 176.9 160 198.2 160zM480.3 480L413.9 480L413.9 376C413.9 351.2 413.4 319.3 379.4 319.3C344.8 319.3 339.5 346.3 339.5 374.2L339.5 480L273.1 480L273.1 266.2L336.8 266.2L336.8 295.4L337.7 295.4C346.6 278.6 368.3 260.9 400.6 260.9C467.8 260.9 480.3 305.2 480.3 362.8L480.3 480z"/>
                </svg>
                <span>LinkedIn</span>
              </button>

              <!-- Share Facebook -->
              <button @click="shareFacebook" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 440 146.7 540.8 258.2 568.5L258.2 398.2L205.4 398.2L205.4 320L258.2 320L258.2 286.3C258.2 199.2 297.6 158.8 383.2 158.8C399.4 158.8 427.4 162 438.9 165.2L438.9 236C432.9 235.4 422.4 235 409.3 235C367.3 235 351.1 250.9 351.1 292.2L351.1 320L434.7 320L420.3 398.2L351 398.2L351 574.1C477.8 558.8 576 450.9 576 320z"/>
                </svg>
                <span>Facebook</span>
              </button>

              <!-- Share Telegram -->
              <button @click="shareTelegram" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M320 72C183 72 72 183 72 320C72 457 183 568 320 568C457 568 568 457 568 320C568 183 457 72 320 72zM435 240.7C431.3 279.9 415.1 375.1 406.9 419C403.4 437.6 396.6 443.8 390 444.4C375.6 445.7 364.7 434.9 350.7 425.7C328.9 411.4 316.5 402.5 295.4 388.5C270.9 372.4 286.8 363.5 300.7 349C304.4 345.2 367.8 287.5 369 282.3C369.2 281.6 369.3 279.2 367.8 277.9C366.3 276.6 364.2 277.1 362.7 277.4C360.5 277.9 325.6 300.9 258.1 346.5C248.2 353.3 239.2 356.6 231.2 356.4C222.3 356.2 205.3 351.4 192.6 347.3C177.1 342.3 164.7 339.6 165.8 331C166.4 326.5 172.5 322 184.2 317.3C256.5 285.8 304.7 265 328.8 255C397.7 226.4 412 221.4 421.3 221.2C423.4 221.2 427.9 221.7 430.9 224.1C432.9 225.8 434.1 228.2 434.4 230.8C434.9 234 435 237.3 434.8 240.6z"/>
                </svg>
                <span>Telegram</span>
              </button>

              <!-- Share WhatsApp -->
              <button @click="shareWhatsApp" class="px-3 py-1.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-900/40 dark:hover:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-[11px] font-semibold text-zinc-650 dark:text-zinc-300 hover:text-black dark:hover:text-white hover:border-zinc-350 dark:hover:border-zinc-700 flex items-center gap-1.5 transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-3 h-3 text-zinc-500 dark:text-zinc-400" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z"/>
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
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
                title="Bagikan ke X"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M453.2 112L523.8 112L369.6 288.2L551 528L409 528L297.7 382.6L170.5 528L99.8 528L264.7 339.5L90.8 112L236.4 112L336.9 244.9L453.2 112zM428.4 485.8L467.5 485.8L215.1 152L173.1 152L428.4 485.8z"/>
                </svg>
              </button>

              <!-- LinkedIn -->
              <button 
                @click="shareLinkedIn" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M512 96L127.9 96C110.3 96 96 110.5 96 128.3L96 511.7C96 529.5 110.3 544 127.9 544L512 544C529.6 544 544 529.5 544 511.7L544 128.3C544 110.5 529.6 96 512 96zM231.4 480L165 480L165 266.2L231.5 266.2L231.5 480L231.4 480zM198.2 160C219.5 160 236.7 177.2 236.7 198.5C236.7 219.8 219.5 237 198.2 237C176.9 237 159.7 219.8 159.7 198.5C159.7 177.2 176.9 160 198.2 160zM480.3 480L413.9 480L413.9 376C413.9 351.2 413.4 319.3 379.4 319.3C344.8 319.3 339.5 346.3 339.5 374.2L339.5 480L273.1 480L273.1 266.2L336.8 266.2L336.8 295.4L337.7 295.4C346.6 278.6 368.3 260.9 400.6 260.9C467.8 260.9 480.3 305.2 480.3 362.8L480.3 480z"/>
                </svg>
                <span>LinkedIn</span>
              </button>

              <!-- Facebook -->
              <button 
                @click="shareFacebook" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 440 146.7 540.8 258.2 568.5L258.2 398.2L205.4 398.2L205.4 320L258.2 320L258.2 286.3C258.2 199.2 297.6 158.8 383.2 158.8C399.4 158.8 427.4 162 438.9 165.2L438.9 236C432.9 235.4 422.4 235 409.3 235C367.3 235 351.1 250.9 351.1 292.2L351.1 320L434.7 320L420.3 398.2L351 398.2L351 574.1C477.8 558.8 576 450.9 576 320z"/>
                </svg>
                <span>Facebook</span>
              </button>

              <!-- Telegram -->
              <button 
                @click="shareTelegram" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M320 72C183 72 72 183 72 320C72 457 183 568 320 568C457 568 568 457 568 320C568 183 457 72 320 72zM435 240.7C431.3 279.9 415.1 375.1 406.9 419C403.4 437.6 396.6 443.8 390 444.4C375.6 445.7 364.7 434.9 350.7 425.7C328.9 411.4 316.5 402.5 295.4 388.5C270.9 372.4 286.8 363.5 300.7 349C304.4 345.2 367.8 287.5 369 282.3C369.2 281.6 369.3 279.2 367.8 277.9C366.3 276.6 364.2 277.1 362.7 277.4C360.5 277.9 325.6 300.9 258.1 346.5C248.2 353.3 239.2 356.6 231.2 356.4C222.3 356.2 205.3 351.4 192.6 347.3C177.1 342.3 164.7 339.6 165.8 331C166.4 326.5 172.5 322 184.2 317.3C256.5 285.8 304.7 265 328.8 255C397.7 226.4 412 221.4 421.3 221.2C423.4 221.2 427.9 221.7 430.9 224.1C432.9 225.8 434.1 228.2 434.4 230.8C434.9 234 435 237.3 434.8 240.6z"/>
                </svg>
                <span>Telegram</span>
              </button>

              <!-- WhatsApp -->
              <button 
                @click="shareWhatsApp" 
                class="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]"
              >
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 640 640">
                  <path d="M476.9 161.1C435 119.1 379.2 96 319.9 96C197.5 96 97.9 195.6 97.9 318C97.9 357.1 108.1 395.3 127.5 429L96 544L213.7 513.1C246.1 530.8 282.6 540.1 319.8 540.1L319.9 540.1C442.2 540.1 544 440.5 544 318.1C544 258.8 518.8 203.1 476.9 161.1zM319.9 502.7C286.7 502.7 254.2 493.8 225.9 477L219.2 473L149.4 491.3L168 423.2L163.6 416.2C145.1 386.8 135.4 352.9 135.4 318C135.4 216.3 218.2 133.5 320 133.5C369.3 133.5 415.6 152.7 450.4 187.6C485.2 222.5 506.6 268.8 506.5 318.1C506.5 419.9 421.6 502.7 319.9 502.7zM421.1 364.5C415.6 361.7 388.3 348.3 383.2 346.5C378.1 344.6 374.4 343.7 370.7 349.3C367 354.9 356.4 367.3 353.1 371.1C349.9 374.8 346.6 375.3 341.1 372.5C308.5 356.2 287.1 343.4 265.6 306.5C259.9 296.7 271.3 297.4 281.9 276.2C283.7 272.5 282.8 269.3 281.4 266.5C280 263.7 268.9 236.4 264.3 225.3C259.8 214.5 255.2 216 251.8 215.8C248.6 215.6 244.9 215.6 241.2 215.6C237.5 215.6 231.5 217 226.4 222.5C221.3 228.1 207 241.5 207 268.8C207 296.1 226.9 322.5 229.6 326.2C232.4 329.9 268.7 385.9 324.4 410C359.6 425.2 373.4 426.5 391 423.9C401.7 422.3 423.8 410.5 428.4 397.5C433 384.5 433 373.4 431.6 371.1C430.3 368.6 426.6 367.2 421.1 364.5z"/>
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
  processedContent.value = sanitizeHtml(doc.body.innerHTML)
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
