<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    ExternalLink,
    Calendar,
    Folder,
    Cpu,
    BookOpen,
    Eye,
    Star,
    ImageIcon
} from '@lucide/vue';
import { index as projectsIndex } from '@/routes/projects';

const props = defineProps<{
    project: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Proyek',
                href: projectsIndex(),
            },
            {
                title: 'Preview Proyek',
                href: '#',
            },
        ],
    },
});

// Interactive Gallery State
const activeGalleryIndex = ref(0);
const hasGallery = computed(() => props.project.gallery_images && props.project.gallery_images.length > 0);
const activeImage = computed(() => {
    if (!hasGallery.value) return null;
    return props.project.gallery_images[activeGalleryIndex.value];
});

const formatDate = (dateString: string) => {
    if (!dateString) return 'Draft';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Preview Proyek: ${project.title}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto max-w-4xl mx-auto w-full pb-16">
        <!-- Floating Preview Notice Badge -->
        <div class="flex items-center justify-between p-3 bg-primary/10 border border-primary/20 dark:bg-primary/20 dark:border-primary/30 rounded-xl text-primary text-xs font-semibold gap-2 mb-2 shadow-xs">
            <span class="flex items-center gap-1.5">
                <Eye class="h-4 w-4" />
                Mode Preview CMS Admin
            </span>
            <span class="text-[10px] uppercase bg-primary text-white px-2 py-0.5 rounded font-bold">
                {{ project.status }}
            </span>
        </div>

        <!-- Navigation header -->
        <div class="flex items-center gap-3">
            <Link :href="projectsIndex()">
                <Button variant="ghost" size="icon" class="h-9 w-9 rounded-lg border border-sidebar-border/70 cursor-pointer">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
            </Link>
            <div class="flex items-center gap-2 text-xs text-muted-foreground font-semibold">
                <Link :href="projectsIndex()" class="hover:text-foreground transition-colors">Proyek</Link>
                <span>/</span>
                <span class="text-foreground max-w-[200px] truncate">{{ project.title }}</span>
            </div>
        </div>

        <!-- Case Study Header Section -->
        <div class="space-y-4">
            <!-- Category & Date Info -->
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-200 border border-neutral-200/50 dark:border-neutral-700/50">
                    <Folder class="h-3.5 w-3.5 text-primary" />
                    {{ project.category ? project.category.name : 'Uncategorized' }}
                </span>
                
                <span class="flex items-center gap-1 text-xs text-muted-foreground font-medium">
                    <Calendar class="h-3.5 w-3.5" />
                    {{ formatDate(project.published_at || project.created_at) }}
                </span>

                <span v-if="project.is_featured" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/50 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30">
                    <Star class="h-3 w-3 fill-amber-500 text-amber-500" />
                    Featured Project
                </span>
            </div>

            <!-- Main Title -->
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-foreground leading-tight">
                {{ project.title }}
            </h1>

            <!-- Short Description -->
            <p class="text-md md:text-lg text-neutral-600 dark:text-neutral-400 leading-relaxed max-w-3xl">
                {{ project.short_description }}
            </p>
        </div>

        <!-- Large Cover Banner -->
        <div class="space-y-2">
            <div class="w-full aspect-[21/9] rounded-2xl overflow-hidden border border-sidebar-border bg-neutral-100 dark:bg-neutral-800/50 shadow-md transition-all duration-300 hover:shadow-lg">
                <img
                    v-if="project.cover_image"
                    :src="project.cover_image.urls.large || project.cover_image.urls.original"
                    :alt="project.title"
                    class="w-full h-full object-cover transition-transform duration-500 hover:scale-[1.015]"
                />
                <div v-else class="w-full h-full flex flex-col items-center justify-center text-muted-foreground gap-2">
                    <ImageIcon class="h-12 w-12 text-neutral-400" />
                    <span class="text-xs">No Cover Image Selected</span>
                </div>
            </div>
            <!-- Cover image caption centered below -->
            <p v-if="project.cover_image_caption" class="text-xs text-center text-neutral-500 dark:text-neutral-400 italic">
                {{ project.cover_image_caption }}
            </p>
        </div>

        <!-- External Link Cards (Glassmorphism design with brand glowing colors) -->
        <div v-if="project.live_url || project.github_url || project.telegram_url" class="space-y-3">
            <h2 class="text-xs font-bold tracking-wider text-muted-foreground uppercase">Tautan Eksternal & Demo</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Live Demo Link -->
                <a
                    v-if="project.live_url"
                    :href="project.live_url"
                    target="_blank"
                    class="group relative flex items-center p-4 rounded-xl border border-sidebar-border/80 bg-white/40 dark:bg-neutral-900/20 backdrop-blur-md shadow-xs transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5 hover:shadow-emerald-500/10 hover:border-emerald-500/40 cursor-pointer overflow-hidden"
                >
                    <!-- Glowing Gradient effect on hover -->
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 to-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="flex items-center gap-3.5 z-10 w-full">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                            <ExternalLink class="h-5 w-5" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-foreground">Live Project Demo</span>
                            <span class="text-[10px] text-muted-foreground mt-0.5">Lihat hasil demo website</span>
                        </div>
                    </div>
                </a>

                <!-- GitHub Link -->
                <a
                    v-if="project.github_url"
                    :href="project.github_url"
                    target="_blank"
                    class="group relative flex items-center p-4 rounded-xl border border-sidebar-border/80 bg-white/40 dark:bg-neutral-900/20 backdrop-blur-md shadow-xs transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5 hover:shadow-violet-500/10 hover:border-violet-500/40 cursor-pointer overflow-hidden"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-500/0 to-violet-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="flex items-center gap-3.5 z-10 w-full">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-950/30 text-violet-600 dark:text-violet-400 group-hover:scale-110 transition-transform">
                            <!-- Custom GitHub Brand SVG -->
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-foreground">GitHub Repository</span>
                            <span class="text-[10px] text-muted-foreground mt-0.5">Buka kode sumber proyek</span>
                        </div>
                    </div>
                </a>

                <!-- Telegram Link -->
                <a
                    v-if="project.telegram_url"
                    :href="project.telegram_url"
                    target="_blank"
                    class="group relative flex items-center p-4 rounded-xl border border-sidebar-border/80 bg-white/40 dark:bg-neutral-900/20 backdrop-blur-md shadow-xs transition-all duration-300 hover:scale-[1.02] hover:-translate-y-0.5 hover:shadow-cyan-500/10 hover:border-cyan-500/40 cursor-pointer overflow-hidden"
                >
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/0 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="flex items-center gap-3.5 z-10 w-full">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-cyan-50 dark:bg-cyan-950/30 text-cyan-600 dark:text-cyan-400 group-hover:scale-110 transition-transform">
                            <!-- Custom Telegram Brand SVG -->
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-1-.65-.35-1 .22-1.62.15-.15 2.7-2.46 2.75-2.67.01-.03.01-.15-.06-.21a.28.28 0 0 0-.2-.04c-.1.02-1.62 1.02-4.57 3.02-.43.3-.82.45-1.18.44-.39-.01-1.15-.22-1.72-.41-.7-.23-1.25-.35-1.2-.74.03-.2.3-.41.82-.62 3.22-1.4 5.37-2.32 6.47-2.77 3.08-1.27 3.72-1.49 4.14-1.5.09 0 .3.02.43.12.11.08.14.2.16.29.02.1.03.36.01.48z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-foreground">Telegram Bot</span>
                            <span class="text-[10px] text-muted-foreground mt-0.5">Uji coba interaksi bot</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Tech Stack Tags Row -->
        <div v-if="project.technologies && project.technologies.length > 0" class="space-y-3">
            <h2 class="text-xs font-bold tracking-wider text-muted-foreground uppercase flex items-center gap-1.5">
                <Cpu class="h-3.5 w-3.5 text-primary" />
                Teknologi yang Digunakan
            </h2>
            <div class="flex flex-wrap gap-2">
                <div
                    v-for="tech in project.technologies"
                    :key="tech.id"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-sidebar-border bg-card shadow-2xs hover:border-primary/50 transition-colors"
                >
                    <div class="h-4.5 w-4.5 overflow-hidden rounded bg-neutral-100 flex items-center justify-center" v-if="tech.logo">
                        <img :src="tech.logo.urls.thumbnail" :alt="tech.name" class="h-full w-full object-cover" />
                    </div>
                    <span class="text-xs font-bold text-neutral-800 dark:text-neutral-200">{{ tech.name }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mt-4 border-t border-sidebar-border/60 pt-6">
            <!-- Narrative (Detailed case study description) -->
            <div class="space-y-3">
                <h2 class="text-xs font-bold tracking-wider text-muted-foreground uppercase flex items-center gap-1.5">
                    <BookOpen class="h-3.5 w-3.5 text-primary" />
                    Studi Kasus Detail
                </h2>
                
                <div class="show-narrative-container prose prose-neutral dark:prose-invert max-w-none bg-card p-6 border border-sidebar-border/70 rounded-2xl shadow-2xs min-h-[150px]">
                    <div v-if="project.full_description" v-html="project.full_description"></div>
                    <p v-else class="text-sm text-muted-foreground italic text-center py-6">Deskripsi studi kasus belum ditulis.</p>
                </div>
            </div>

            <!-- Visual Gallery Slider/Carousel -->
            <div v-if="hasGallery" class="space-y-3">
                <h2 class="text-xs font-bold tracking-wider text-muted-foreground uppercase">Galeri Screenshot Proyek</h2>
                
                <div class="border border-sidebar-border bg-card rounded-2xl overflow-hidden p-4 space-y-4">
                    <!-- Main visual slide -->
                    <div class="w-full aspect-video rounded-xl border border-sidebar-border bg-neutral-50 dark:bg-neutral-900/50 flex flex-col items-center justify-center overflow-hidden relative">
                        <transition name="fade" mode="out-in">
                            <img
                                :key="activeGalleryIndex"
                                :src="activeImage.urls.large || activeImage.urls.original"
                                :alt="activeImage.pivot.caption || 'Gallery Image'"
                                class="h-full w-full object-contain"
                            />
                        </transition>
                    </div>

                    <!-- Slide Caption -->
                    <div v-if="activeImage && activeImage.pivot.caption" class="p-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-xl border border-sidebar-border/50 text-xs text-center text-neutral-600 dark:text-neutral-400 italic">
                        {{ activeImage.pivot.caption }}
                    </div>

                    <!-- Thumbnails Navigation -->
                    <div class="flex items-center gap-2 overflow-x-auto py-1">
                        <button
                            v-for="(img, idx) in project.gallery_images"
                            :key="img.id"
                            type="button"
                            @click="activeGalleryIndex = idx"
                            class="h-16 w-24 rounded-lg overflow-hidden border-2 shrink-0 transition-all duration-150 cursor-pointer"
                            :class="activeGalleryIndex === idx ? 'border-primary ring-2 ring-primary/20 scale-[0.98]' : 'border-sidebar-border hover:border-neutral-400'"
                        >
                            <img :src="img.urls.thumbnail" :alt="img.original_filename" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* CSS styles for narrative preview rendering */
.show-narrative-container p {
    margin-bottom: 1.25rem;
    line-height: 1.7;
    color: var(--color-neutral-700);
}
.dark .show-narrative-container p {
    color: var(--color-neutral-300);
}

.show-narrative-container h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    color: var(--color-neutral-900);
}
.dark .show-narrative-container h2 {
    color: var(--color-neutral-100);
}

.show-narrative-container h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-top: 1.75rem;
    margin-bottom: 0.5rem;
    color: var(--color-neutral-900);
}
.dark .show-narrative-container h3 {
    color: var(--color-neutral-100);
}

.show-narrative-container h4 {
    font-size: 1.125rem;
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--color-neutral-900);
}
.dark .show-narrative-container h4 {
    color: var(--color-neutral-100);
}

.show-narrative-container ul {
    list-style-type: disc;
    padding-left: 1.75rem;
    margin-bottom: 1.25rem;
}

.show-narrative-container ol {
    list-style-type: decimal;
    padding-left: 1.75rem;
    margin-bottom: 1.25rem;
}

.show-narrative-container li {
    margin-bottom: 0.35rem;
    line-height: 1.7;
    color: var(--color-neutral-700);
}
.dark .show-narrative-container li {
    color: var(--color-neutral-300);
}

.show-narrative-container blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: 1.25rem;
    font-style: italic;
    color: var(--color-neutral-600);
    margin: 1.75rem 0;
}
.dark .show-narrative-container blockquote {
    color: var(--color-neutral-400);
}

.show-narrative-container code {
    background-color: var(--color-neutral-100);
    color: var(--color-neutral-800);
    padding: 0.125rem 0.35rem;
    border-radius: 0.25rem;
    font-family: monospace;
    font-size: 0.875em;
}
.dark .show-narrative-container code {
    background-color: var(--color-neutral-800);
    color: var(--color-neutral-200);
}

.show-narrative-container pre {
    background-color: var(--color-neutral-950);
    color: var(--color-neutral-100);
    padding: 1.25rem;
    border-radius: 0.75rem;
    font-family: monospace;
    font-size: 0.875em;
    overflow-x: auto;
    margin: 1.5rem 0;
    border: 1px solid var(--color-neutral-900);
}

.show-narrative-container pre code {
    background-color: transparent;
    color: inherit;
    padding: 0;
    font-size: inherit;
    border-radius: 0;
}

.show-narrative-container figure {
    margin: 2rem auto;
    max-width: 100%;
}

.show-narrative-container figcaption {
    font-size: 0.75rem;
    line-height: 1.4;
    margin-top: 0.75rem;
    font-style: italic;
    color: var(--color-neutral-500);
    text-align: center;
}

/* Slide cross-fade animation */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
