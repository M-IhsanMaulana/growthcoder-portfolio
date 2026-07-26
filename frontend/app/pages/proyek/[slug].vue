<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 pb-20">
    <div v-if="project" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 space-y-12">

      <!-- ─── BREADCRUMB & BACK ACTION ─────────────────────────────────── -->
      <div ref="breadcrumb"
        class="opacity-0 -translate-y-2 flex items-center space-x-2 text-xs sm:text-sm font-semibold">
        <NuxtLink to="/proyek"
          class="flex items-center space-x-1 text-zinc-500 hover:text-brand-purple dark:hover:text-white transition-colors cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
            stroke-width="2.5" viewBox="0 0 24 24" class="mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Projects
        </NuxtLink>
        <span class="text-zinc-300 dark:text-zinc-800">/</span>
        <span class="text-zinc-800 dark:text-zinc-300 font-bold truncate max-w-[200px] sm:max-w-none">{{ project.title
          }}</span>
      </div>

      <!-- ─── HEADER DETAIL SECTION ───────────────────────────────────── -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Text detail information -->
        <div ref="headerText" class="lg:col-span-7 space-y-6 opacity-0 -translate-x-4">
          <!-- Category Badge -->
          <div v-if="project.category?.name"
            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-purple/5 dark:bg-brand-purple/10 border border-brand-purple/20">
            <span class="text-[10px] font-bold tracking-widest uppercase text-brand-purple dark:text-indigo-400">
              {{ project.category.name }}
            </span>
          </div>

          <!-- Title -->
          <h1 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white leading-tight">
            {{ project.title }}
          </h1>

          <!-- Subtitle / Short description -->
          <p class="text-sm md:text-base text-zinc-500 dark:text-zinc-400 font-light leading-relaxed">
            {{ project.short_description }}
          </p>

          <!-- Action buttons (Live Demo, GitHub, Telegram) -->
          <div class="flex flex-wrap items-center gap-3 pt-2">
            <!-- Live Preview -->
            <Button v-if="project.live_url" as="a" :href="project.live_url" target="_blank" label="Live Preview"
              icon="pi pi-external-link"
              class="!px-5 !py-3.5 !font-bold !rounded-xl !text-xs !bg-brand-purple hover:!bg-brand-navy-hover !text-white shadow-md shadow-brand-purple/10 cursor-pointer transition-all duration-300" />

            <!-- GitHub Link -->
            <Button v-if="project.github_url" as="a" :href="project.github_url" target="_blank" label="View on GitHub"
              icon="pi pi-github" severity="secondary" variant="outlined"
              class="!px-5 !py-3.5 !font-bold !rounded-xl !text-xs !border-zinc-200 dark:!border-zinc-800 hover:!bg-gray-100 dark:hover:!bg-zinc-900 cursor-pointer transition-all duration-300" />

            <!-- Telegram Bot Link -->
            <Button v-if="project.telegram_url" as="a" :href="project.telegram_url" target="_blank" label="Telegram Bot"
              icon="pi pi-send"
              class="!px-5 !py-3.5 !font-bold !rounded-xl !text-xs !bg-sky-500 hover:!bg-sky-600 !text-white shadow-md shadow-sky-500/10 cursor-pointer transition-all duration-300 border-0" />
          </div>

          <!-- Published at info -->
          <div class="flex items-center gap-2 text-xs text-zinc-400 dark:text-zinc-500 pt-2 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2.5">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
              <line x1="16" y1="2" x2="16" y2="6" />
              <line x1="8" y1="2" x2="8" y2="6" />
              <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
            <span>Dipublikasikan pada {{ formattedPublishDate }}</span>
          </div>
        </div>

        <!-- Right: Cover Mockup Image -->
        <div ref="headerImage" class="lg:col-span-5 flex justify-center opacity-0 translate-x-4">
          <div
            class="relative w-full aspect-video rounded-[2rem] overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 shadow-2xl group flex flex-col">
            <!-- Glowing border effect -->
            <div
              class="absolute -inset-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-[2rem] blur opacity-15 group-hover:opacity-25 transition duration-700">
            </div>

            <div class="relative w-full h-full rounded-[1.95rem] overflow-hidden bg-zinc-100 dark:bg-zinc-950 z-10">
              <NuxtImg v-if="project.cover_image?.urls?.original || project.cover_image?.urls?.medium"
                :src="project.cover_image.urls.original || project.cover_image.urls.medium" :alt="project.title"
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-103"
                preload fetchpriority="high" />
              <div v-else class="absolute inset-0 flex items-center justify-center text-5xl bg-zinc-900">
                💻
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ─── MAIN CONTENT BODY SECTION ────────────────────────────────── -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-6">

        <!-- Left: Overview Text content -->
        <div ref="mainContent" class="lg:col-span-8 space-y-8 opacity-0 translate-y-4">
          <div
            class="bg-white dark:bg-zinc-900/60 rounded-3xl p-6 sm:p-8 border border-zinc-200/50 dark:border-zinc-800/40 shadow-xs space-y-4 relative overflow-hidden">
            <h2
              class="text-xl sm:text-2xl font-extrabold text-zinc-900 dark:text-white flex items-center gap-2 pb-3 border-b border-zinc-100 dark:border-zinc-800">
              <span class="w-1.5 h-6 bg-brand-purple rounded-full"></span>
              Overview
            </h2>
            <div v-if="project.full_description"
              class="rich-text-content text-zinc-600 dark:text-zinc-300 leading-relaxed text-sm font-light max-w-none space-y-4"
              v-html="sanitizeHtml(project.full_description)"></div>
            <p v-else class="text-sm text-zinc-400 italic font-light">
              Belum ada deskripsi mendalam yang ditambahkan untuk proyek ini.
            </p>
          </div>
        </div>

        <!-- Right: Information Details Panel -->
        <div ref="sidePanel" class="lg:col-span-4 opacity-0 translate-y-4">
          <Card class="shadow-xs border border-zinc-200/50 dark:border-zinc-800/40 relative overflow-hidden" :pt="{
            root: { class: '!bg-white dark:!bg-zinc-950 !rounded-3xl !p-0 shadow-sm relative overflow-hidden' },
            body: { class: '!p-6 sm:!p-8' }
          }">
            <template #content>
              <h3
                class="text-sm font-extrabold text-zinc-900 dark:text-white pb-3.5 border-b border-zinc-150 dark:border-zinc-800/80 mb-5 flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-brand-purple/10 text-brand-purple dark:text-indigo-400 flex items-center justify-center shrink-0">
                  <Info class="w-4 h-4" />
                </div>
                <span>Detail Informasi</span>
              </h3>

              <div class="space-y-5">
                <!-- Category -->
                <div class="flex items-start space-x-3.5">
                  <div
                    class="shrink-0 w-9 h-9 rounded-xl bg-indigo-500/10 dark:bg-indigo-400/10 flex items-center justify-center text-brand-purple dark:text-indigo-400">
                    <FolderKanban class="w-4.5 h-4.5" />
                  </div>
                  <div>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider leading-none">
                      Kategori
                    </p>
                    <p class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 mt-1 leading-snug">
                      {{ project.category?.name || '-' }}
                    </p>
                  </div>
                </div>

                <!-- Role -->
                <div class="flex items-start space-x-3.5">
                  <div
                    class="shrink-0 w-9 h-9 rounded-xl bg-emerald-500/10 dark:bg-emerald-400/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <UserCheck class="w-4.5 h-4.5" />
                  </div>
                  <div>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider leading-none">
                      Peran
                    </p>
                    <p class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 mt-1 leading-snug">
                      {{ project.role || '-' }}
                    </p>
                  </div>
                </div>

                <!-- Year -->
                <div class="flex items-start space-x-3.5">
                  <div
                    class="shrink-0 w-9 h-9 rounded-xl bg-purple-500/10 dark:bg-purple-400/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <Calendar class="w-4.5 h-4.5" />
                  </div>
                  <div>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider leading-none">
                      Tahun
                    </p>
                    <p class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 mt-1 leading-snug">
                      {{ projectYear }}
                    </p>
                  </div>
                </div>

                <!-- Live link details -->
                <div v-if="project.live_url || project.github_url || project.telegram_url"
                  class="flex items-start space-x-3.5 pt-1">
                  <div
                    class="shrink-0 w-9 h-9 rounded-xl bg-blue-500/10 dark:bg-blue-400/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <Globe class="w-4.5 h-4.5" />
                  </div>
                  <div class="space-y-2 flex-1 min-w-0">
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider leading-none">
                      Tautan Resmi
                    </p>

                    <div class="flex flex-col gap-2 pt-0.5">
                      <a v-if="project.live_url" :href="project.live_url" target="_blank"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-purple dark:text-indigo-400 hover:text-brand-purple/80 dark:hover:text-indigo-300 transition-colors group">
                        <Globe class="w-3.5 h-3.5 text-brand-purple/70 dark:text-indigo-400/70" />
                        <span>Demo Website</span>
                        <ExternalLink class="w-3 h-3 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                      </a>

                      <a v-if="project.github_url" :href="project.github_url" target="_blank"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 transition-colors group">
                        <Github class="w-3.5 h-3.5 text-zinc-500 dark:text-zinc-400" />
                        <span>Source Code GitHub</span>
                        <ExternalLink class="w-3 h-3 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                      </a>

                      <a v-if="project.telegram_url" :href="project.telegram_url" target="_blank"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-500 transition-colors group">
                        <Send class="w-3.5 h-3.5 text-sky-500" />
                        <span>Telegram Bot Link</span>
                        <ExternalLink class="w-3 h-3 transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </div>

      <!-- ─── TECH STACK SECTION ────────────────────────────────────────── -->
      <section v-if="project.technologies && project.technologies.length" ref="techSection"
        class="opacity-0 translate-y-4 py-4">
        <h3
          class="text-xs font-bold uppercase tracking-widest text-zinc-550 dark:text-zinc-400 mb-6 flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-brand-purple"></span>
          Tech Stack
        </h3>

        <div class="flex flex-wrap gap-3.5">
          <div v-for="tech in project.technologies" :key="tech.id"
            class="flex items-center space-x-3 px-5 py-3 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/60 shadow-xs group">
            <!-- Render dynamic logo/icon if available -->
            <div class="w-6 h-6 flex items-center justify-center shrink-0">
              <NuxtImg
                v-if="tech.logo?.urls?.medium || tech.logo?.urls?.thumbnail || tech.logo?.urls?.original"
                :src="tech.logo?.urls?.medium || tech.logo?.urls?.thumbnail || tech.logo?.urls?.original"
                :alt="tech.name"
                class="w-full h-full object-contain"
              />
              <div v-else-if="tech.icon"
                class="w-full h-full flex items-center justify-center text-zinc-500 [&_svg]:w-4.5 [&_svg]:h-4.5"
                v-html="tech.icon"></div>
              <div v-else class="text-base">🛠️</div>
            </div>
            <span class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 pr-1">{{ tech.name }}</span>
          </div>
        </div>
      </section>

      <!-- ─── PROJECT GALLERY SECTION ───────────────────────────────────── -->
      <section v-if="project.gallery && project.gallery.length" ref="gallerySection"
        class="opacity-0 translate-y-4 py-4">
        <h3
          class="text-xs font-bold uppercase tracking-widest text-zinc-550 dark:text-zinc-400 mb-6 flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-brand-purple"></span>
          Project Gallery
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(image, idx) in project.gallery" :key="image.id" @click="openLightbox(idx)"
            class="relative aspect-video rounded-3xl overflow-hidden border border-zinc-200/50 dark:border-zinc-800/80 bg-white dark:bg-zinc-900 shadow-sm cursor-zoom-in group">
            <NuxtImg :src="image.urls.medium || image.urls.original" :alt="image.alt_text || 'Gallery Image'"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103"
              loading="lazy" />
            <!-- Overlay and caption details -->
            <div
              class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-zinc-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-5">
              <div class="text-white min-w-0">
                <p class="text-[10px] font-semibold text-brand-green uppercase tracking-wide">Screenshot #{{ idx + 1 }}
                </p>
                <h4 v-if="image.caption" class="text-xs font-bold mt-1 truncate leading-normal">{{ image.caption }}</h4>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ─── KEY FEATURES SECTION ──────────────────────────────────────── -->
      <section v-if="project.key_features && project.key_features.length" ref="featuresSection"
        class="opacity-0 translate-y-4 py-4">
        <h3
          class="text-xs font-bold uppercase tracking-widest text-zinc-550 dark:text-zinc-400 mb-6 flex items-center gap-3">
          <span class="w-2 h-2 rounded-full bg-brand-purple"></span>
          Key Features
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card v-for="feature in project.key_features" :key="feature.title" :pt="{
            root: { class: '!bg-white dark:!bg-zinc-900/60 !border !border-zinc-150 dark:!border-zinc-800 !rounded-3xl shadow-xs hover:shadow-md transition-all duration-300' },
            body: { class: '!p-6' }
          }">
            <template #content>
              <div class="space-y-4">
                <!-- Feature Icon wrapper -->
                <div
                  class="w-10 h-10 rounded-2xl bg-brand-purple/10 flex items-center justify-center text-brand-purple dark:text-indigo-400 shadow-inner">
                  <!-- Load dynamic Lucide icon matching database -->
                  <component :is="getLucideIcon(feature.icon)" class="w-5 h-5" />
                </div>
                <div class="space-y-1.5">
                  <h4 class="text-sm font-extrabold text-zinc-900 dark:text-white leading-tight">
                    {{ feature.title }}
                  </h4>
                  <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-light leading-relaxed">
                    {{ feature.description }}
                  </p>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </section>

      <!-- ─── CALL TO ACTION (CTA) BANNER ──────────────────────────────── -->
      <section ref="ctaBanner" class="opacity-0 translate-y-4 mt-6">
        <div
          class="rounded-3xl p-[1.5px] bg-gradient-to-r from-brand-purple/20 via-indigo-500/20 to-brand-green/20 dark:from-brand-purple/40 dark:via-indigo-500/35 dark:to-brand-green/40 hover:from-brand-purple hover:via-indigo-500 hover:to-brand-green transition-all duration-300 shadow-xs">
          <div class="relative overflow-hidden rounded-[1.4rem] bg-white dark:bg-zinc-950 p-8 group">
            <div
              class="absolute inset-0 bg-gradient-to-r from-brand-purple/5 via-transparent to-brand-green/5 opacity-50 dark:opacity-30">
            </div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center gap-6 justify-between">

              <!-- Left content -->
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1">
                <div
                  class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-brand-green to-emerald-500 flex items-center justify-center text-white shadow-md">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-send">
                    <line x1="22" y1="2" x2="11" y2="13" />
                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                  </svg>
                </div>
                <div class="space-y-1">
                  <h4 class="text-base sm:text-lg font-extrabold text-zinc-900 dark:text-white leading-tight">Tertarik
                    dengan
                    proyek ini?</h4>
                  <p class="text-xs sm:text-sm text-zinc-550 dark:text-zinc-400 leading-relaxed font-light">Saya terbuka
                    untuk
                    diskusi, kolaborasi, atau peluang kerja baru yang menantang.</p>
                </div>
              </div>

              <!-- Right action button -->
              <div class="flex-shrink-0">
                <Button as="router-link" to="/contact" label="Hubungi Saya" icon="pi pi-arrow-up-right" iconPos="right"
                  class="!px-6 !py-3.5 !font-bold !rounded-xl !text-xs shadow-md shadow-brand-purple/20 transition-all duration-300 cursor-pointer" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ─── ULTRA-MODERN LIGHTBOX MODAL DIALOG ───────────────────────── -->
      <Dialog v-model:visible="isLightboxOpen" modal :dismissableMask="true"
        class="!bg-white/95 dark:!bg-zinc-950/95 !border-0 !shadow-none !w-full !max-w-7xl !m-0" :pt="{
          header: { class: '!hidden' },
          content: { class: '!p-0 !bg-transparent flex flex-col h-screen max-h-screen relative overflow-hidden select-none' },
          mask: { class: '!backdrop-blur-xl !bg-zinc-900/40 dark:!bg-zinc-950/90 !p-0' }
        }">
        <div v-if="activeImage" class="flex flex-col h-full w-full relative">
          <!-- Top Header Bar (Glassmorphism) -->
          <div class="h-16 px-4 sm:px-6 flex items-center justify-between z-50 bg-white/80 dark:bg-zinc-900/60 backdrop-blur-md border-b border-zinc-200/80 dark:border-white/10 shrink-0">
            <!-- Counter Pill & Caption -->
            <div class="flex items-center gap-3 min-w-0">
              <div class="px-3 py-1 rounded-full bg-zinc-100 dark:bg-white/10 border border-zinc-200/80 dark:border-white/10 text-zinc-800 dark:text-white text-xs font-semibold flex items-center gap-1.5 shrink-0">
                <ImageIcon class="w-3.5 h-3.5 text-brand-purple dark:text-brand-green" />
                <span>{{ activeImageIndex + 1 }} / {{ project.gallery.length }}</span>
              </div>
              <span v-if="activeImage.caption" class="hidden md:inline-block text-xs text-zinc-600 dark:text-zinc-300 font-medium truncate max-w-md">
                {{ activeImage.caption }}
              </span>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-2 shrink-0">
              <!-- Zoom Toggle Button -->
              <button @click="toggleZoom"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-white/10 hover:bg-zinc-200 dark:hover:bg-white/20 border border-zinc-200/80 dark:border-white/10 flex items-center justify-center text-zinc-700 dark:text-white transition-all cursor-pointer"
                :title="isZoomed ? 'Zoom Out' : 'Zoom In'">
                <ZoomOut v-if="isZoomed" class="w-4 h-4" />
                <ZoomIn v-else class="w-4 h-4" />
              </button>

              <!-- Open Original Image -->
              <a :href="activeImage.urls.original" target="_blank"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-white/10 hover:bg-zinc-200 dark:hover:bg-white/20 border border-zinc-200/80 dark:border-white/10 flex items-center justify-center text-zinc-700 dark:text-white transition-all cursor-pointer"
                title="Buka Gambar Asli">
                <Maximize2 class="w-4 h-4" />
              </a>

              <!-- Close Button -->
              <button @click="isLightboxOpen = false"
                class="w-9 h-9 rounded-full bg-zinc-100 dark:bg-white/10 hover:bg-red-500/80 hover:text-white border border-zinc-200/80 dark:border-white/10 hover:border-red-500/80 flex items-center justify-center text-zinc-700 dark:text-white transition-all cursor-pointer ml-1"
                title="Tutup (Esc)">
                <X class="w-4.5 h-4.5" />
              </button>
            </div>
          </div>

          <!-- Main Image Canvas Viewport -->
          <div class="flex-1 relative flex items-center justify-center p-4 sm:p-8 overflow-hidden">
            <!-- Floating Previous Image Button -->
            <button v-if="project.gallery.length > 1" @click="prevImage"
              class="absolute left-4 sm:left-6 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-white/80 dark:bg-zinc-900/80 hover:bg-brand-purple dark:hover:bg-brand-purple backdrop-blur-md text-zinc-800 dark:text-white hover:text-white dark:hover:text-white border border-zinc-200/80 dark:border-white/20 flex items-center justify-center shadow-xl hover:scale-110 active:scale-95 transition-all cursor-pointer z-50 group"
              title="Sebelumnya (Kiri)">
              <ChevronLeft class="w-6 h-6 transform group-hover:-translate-x-0.5 transition-transform" />
            </button>

            <!-- Active Image Container -->
            <div class="relative max-h-full max-w-full flex items-center justify-center transition-all duration-300"
              :class="isZoomed ? 'scale-125 cursor-zoom-out' : 'cursor-zoom-in'"
              @click="toggleZoom">
              <NuxtImg :src="activeImage.urls.original" :alt="activeImage.caption || 'Gallery Image Preview'"
                class="max-h-[72vh] max-w-[85vw] object-contain rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] dark:shadow-[0_25px_60px_rgba(0,0,0,0.9)] border border-zinc-200/80 dark:border-white/10 transition-all duration-300 bg-white dark:bg-zinc-900" />
            </div>

            <!-- Floating Next Image Button -->
            <button v-if="project.gallery.length > 1" @click="nextImage"
              class="absolute right-4 sm:right-6 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-white/80 dark:bg-zinc-900/80 hover:bg-brand-purple dark:hover:bg-brand-purple backdrop-blur-md text-zinc-800 dark:text-white hover:text-white dark:hover:text-white border border-zinc-200/80 dark:border-white/20 flex items-center justify-center shadow-xl hover:scale-110 active:scale-95 transition-all cursor-pointer z-50 group"
              title="Berikutnya (Kanan)">
              <ChevronRight class="w-6 h-6 transform group-hover:translate-x-0.5 transition-transform" />
            </button>
          </div>

          <!-- Bottom Footer Bar & Thumbnails Row -->
          <div class="px-4 py-3 bg-white/80 dark:bg-zinc-900/70 backdrop-blur-md border-t border-zinc-200/80 dark:border-white/10 flex flex-col items-center gap-2 shrink-0 z-50">
            <!-- Active Image Caption -->
            <p v-if="activeImage.caption" class="text-xs text-zinc-700 dark:text-zinc-300 font-medium text-center max-w-xl truncate">
              {{ activeImage.caption }}
            </p>

            <!-- Thumbnails Strip (Carousel) -->
            <div v-if="project.gallery.length > 1" class="flex items-center justify-center gap-2.5 max-w-full overflow-x-auto py-1 px-2 scrollbar-none">
              <button v-for="(img, idx) in project.gallery" :key="img.id"
                @click="openLightbox(idx)"
                class="w-12 h-9 rounded-lg overflow-hidden border-2 transition-all cursor-pointer shrink-0 relative group"
                :class="idx === activeImageIndex ? 'border-brand-purple scale-105 shadow-md shadow-brand-purple/30 opacity-100' : 'border-transparent opacity-60 hover:opacity-100 hover:scale-100'">
                <NuxtImg :src="img.urls.thumbnail || img.urls.medium" :alt="img.caption || 'Thumbnail'"
                  class="w-full h-full object-cover" />
              </button>
            </div>
          </div>
        </div>
      </Dialog>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import {
  Zap,
  Search,
  Code2,
  Smartphone,
  ShieldCheck,
  Layers,
  Sparkles,
  Rocket,
  Info,
  FolderKanban,
  UserCheck,
  Calendar,
  Globe,
  Github,
  Send,
  ExternalLink,
  ChevronLeft,
  ChevronRight,
  ZoomIn,
  ZoomOut,
  Maximize2,
  ImageIcon,
  X
} from 'lucide-vue-next'

definePageMeta({ layout: 'default' })

const route = useRoute()

// ── DATA FETCHING ─────────────────────────────────────────────────────
const { data: response, error } = await useFetchAPI<any>(`/projects/${route.params.slug}`)

// Handle 404 project not found
if (error.value || !response.value?.data) {
  showError({
    statusCode: 404,
    statusMessage: 'Studi kasus proyek tidak ditemukan.'
  })
}

const project = computed(() => response.value?.data || null)

// ── DYNAMIC SEO METADATA ──────────────────────────────────────────────
useSeoMeta({
  title: () => project.value?.title || 'Detail Proyek',
  description: () => project.value?.short_description || 'Detail Proyek',
  ogTitle: () => project.value?.title || 'Detail Proyek',
  ogDescription: () => project.value?.short_description || 'Detail Proyek',
  ogImage: () => project.value?.cover_image?.urls?.medium || project.value?.cover_image?.urls?.original || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'article',
  twitterCard: 'summary_large_image',
})

useHead(() => {
  if (!project.value) return {}
  return {
    link: [
      { rel: 'canonical', href: `https://growthcoder.id/proyek/${project.value.slug}` }
    ],
    script: [
      {
        type: 'application/ld+json',
        innerHTML: JSON.stringify({
          '@context': 'https://schema.org',
          '@type': 'CreativeWork',
          'name': project.value.title,
          'description': project.value.short_description,
          'image': project.value.cover_image?.urls?.original || project.value.cover_image?.urls?.medium,
          'dateCreated': project.value.published_at,
          'author': {
            '@type': 'Person',
            'name': 'Muhammad Ihsan Maulana'
          },
          'url': `https://growthcoder.id/proyek/${project.value.slug}`,
          'creator': {
            '@type': 'Person',
            'name': 'Muhammad Ihsan Maulana'
          }
        })
      }
    ]
  }
})

// ── FORMAT DATE & DETAILS ─────────────────────────────────────────────
const formattedPublishDate = computed(() => {
  if (!project.value?.published_at) return '-'

  const date = new Date(project.value.published_at)
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ]

  const day = date.getDate()
  const month = months[date.getMonth()]
  const year = date.getFullYear()

  return `${day} ${month} ${year}`
})

const projectYear = computed(() => {
  if (!project.value?.published_at) return '-'
  return new Date(project.value.published_at).getFullYear().toString()
})

// ── LIGHTBOX SCREENSHOTS VIEWER ───────────────────────────────────────
const isLightboxOpen = ref(false)
const activeImageIndex = ref(0)
const isZoomed = ref(false)

const activeImage = computed(() => {
  if (project.value?.gallery && project.value.gallery.length > 0) {
    return project.value.gallery[activeImageIndex.value]
  }
  return null
})

const openLightbox = (index: number) => {
  activeImageIndex.value = index
  isZoomed.value = false
  isLightboxOpen.value = true
}

const toggleZoom = () => {
  isZoomed.value = !isZoomed.value
}

const nextImage = () => {
  if (project.value?.gallery) {
    isZoomed.value = false
    activeImageIndex.value = (activeImageIndex.value + 1) % project.value.gallery.length
  }
}

const prevImage = () => {
  if (project.value?.gallery) {
    isZoomed.value = false
    activeImageIndex.value = (activeImageIndex.value - 1 + project.value.gallery.length) % project.value.gallery.length
  }
}

// Keyboard shortcuts for Lightbox
const handleKeyDown = (e: KeyboardEvent) => {
  if (!isLightboxOpen.value) return
  if (e.key === 'ArrowRight') nextImage()
  if (e.key === 'ArrowLeft') prevImage()
  if (e.key === 'Escape') isLightboxOpen.value = false
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeyDown)
})

// ── LUCIDE DYNAMIC ICONS MAPPING ──────────────────────────────────────
const getLucideIcon = (iconName: string | null) => {
  const iconComponents: Record<string, any> = {
    Zap,
    Search,
    Code2,
    Smartphone,
    ShieldCheck,
    Layers,
    Sparkles,
    Rocket
  }

  return iconComponents[iconName || ''] ?? Code2
}

// ── GSAP ENTRANCE ANIMATIONS ──────────────────────────────────────────
const { $gsap } = useNuxtApp()

const breadcrumb = ref<HTMLElement | null>(null)
const headerText = ref<HTMLElement | null>(null)
const headerImage = ref<HTMLElement | null>(null)
const mainContent = ref<HTMLElement | null>(null)
const sidePanel = ref<HTMLElement | null>(null)
const techSection = ref<HTMLElement | null>(null)
const gallerySection = ref<HTMLElement | null>(null)
const featuresSection = ref<HTMLElement | null>(null)
const ctaBanner = ref<HTMLElement | null>(null)

const initAnimations = () => {
  if (!$gsap) return

  const tl = $gsap.timeline({ delay: 0.05 })

  if (breadcrumb.value) {
    tl.to(breadcrumb.value, { opacity: 1, y: 0, duration: 0.45, ease: 'power3.out' })
  }

  if (headerText.value) {
    tl.to(headerText.value, { opacity: 1, x: 0, duration: 0.55, ease: 'power3.out' }, '-=0.25')
  }

  if (headerImage.value) {
    tl.to(headerImage.value, { opacity: 1, x: 0, duration: 0.55, ease: 'power3.out' }, '-=0.45')
  }

  if (mainContent.value) {
    tl.to(mainContent.value, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' }, '-=0.3')
  }

  if (sidePanel.value) {
    tl.to(sidePanel.value, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' }, '-=0.45')
  }

  // Scroll triggers for lower sections
  const scrollSections = [
    { el: techSection, trigger: techSection },
    { el: gallerySection, trigger: gallerySection },
    { el: featuresSection, trigger: featuresSection },
    { el: ctaBanner, trigger: ctaBanner }
  ]

  scrollSections.forEach(({ el, trigger }) => {
    if (el.value) {
      $gsap.to(el.value, {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: trigger.value,
          start: 'top 85%',
          toggleActions: 'play none none none'
        }
      })
    }
  })
}

onMounted(() => {
  nextTick(() => {
    initAnimations()
  })
})
</script>
<!-- Trigger rebuild -->

<style scoped>
@reference "../../assets/css/main.css";

/* CKEditor / Rich Text content styling overrides */
:deep(.rich-text-content) h1 {
  @apply text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white mt-8 mb-4 tracking-tight leading-tight;
}

:deep(.rich-text-content) h2 {
  @apply text-xl sm:text-2xl font-bold text-zinc-900 dark:text-white mt-7 mb-3.5 tracking-tight leading-snug flex items-center gap-2;
}

:deep(.rich-text-content) h3 {
  @apply text-lg sm:text-xl font-bold text-zinc-900 dark:text-white mt-6 mb-3 leading-snug;
}

:deep(.rich-text-content) h4 {
  @apply text-base font-bold text-zinc-900 dark:text-white mt-5 mb-2.5;
}

:deep(.rich-text-content) p {
  @apply mb-4 leading-relaxed text-zinc-600 dark:text-zinc-300 font-light;
}

:deep(.rich-text-content) p:last-child {
  @apply mb-0;
}

:deep(.rich-text-content) ul {
  @apply list-disc pl-5 mb-4 space-y-1.5 text-zinc-600 dark:text-zinc-300;
}

:deep(.rich-text-content) ol {
  @apply list-decimal pl-5 mb-4 space-y-1.5 text-zinc-600 dark:text-zinc-300;
}

:deep(.rich-text-content) strong,
:deep(.rich-text-content) b {
  @apply font-bold text-zinc-900 dark:text-white;
}

:deep(.rich-text-content) a {
  @apply text-brand-purple dark:text-indigo-400 hover:underline font-semibold;
}

:deep(.rich-text-content) figure,
:deep(.rich-text-content) .image {
  @apply my-6 flex flex-col items-center justify-center text-center w-full;
}

:deep(.rich-text-content) img {
  @apply rounded-2xl max-w-full h-auto shadow-sm border border-zinc-200/60 dark:border-zinc-800/60 mx-auto block object-cover;
}

:deep(.rich-text-content) figcaption,
:deep(.rich-text-content) figure caption,
:deep(.rich-text-content) .image-caption {
  @apply text-xs font-medium text-zinc-500 dark:text-zinc-400 mt-3 text-center italic block max-w-md mx-auto leading-normal;
}
</style>
