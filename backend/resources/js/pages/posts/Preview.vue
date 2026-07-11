<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    Edit2,
    Calendar,
    BookOpen,
    FileText
} from '@lucide/vue';
import { index as postsIndex, edit as postsEdit } from '@/routes/posts';

const props = defineProps<{
    post: any;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Artikel',
                href: postsIndex(),
            },
            {
                title: 'Preview Artikel',
                href: '#',
            },
        ],
    },
});

// Format Date Utility
const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Preview - ${props.post.title}`" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Sticky Action Bar -->
        <div class="flex items-center justify-between border-b border-border pb-4 mb-2">
            <div class="flex items-center gap-3">
                <Link :href="postsIndex()">
                    <Button variant="ghost" size="icon" class="h-9 w-9 rounded-lg border border-border cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <span class="text-[10px] uppercase font-bold text-primary tracking-wider">Mode Pratinjau (Simulasi)</span>
                    <h1 class="text-base font-bold text-foreground line-clamp-1 max-w-md md:max-w-xl">{{ props.post.title }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Link :href="postsEdit(props.post.id)">
                    <Button class="bg-primary text-white hover:bg-primary/90 cursor-pointer font-medium text-xs flex items-center gap-1.5 rounded-lg">
                        <Edit2 class="h-4 w-4" />
                        Edit Artikel
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Simulated Frontend Blog Page -->
        <div class="bg-card border border-border rounded-2xl shadow-sm p-6 md:p-10 max-w-4xl mx-auto w-full mt-2">
            <!-- Article Header -->
            <header class="mb-8 space-y-4">
                <!-- Categories -->
                <div class="flex flex-wrap gap-1.5">
                    <span
                        v-for="cat in props.post.categories"
                        :key="cat.id"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20"
                    >
                        {{ cat.name }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-foreground font-sans leading-tight">
                    {{ props.post.title }}
                </h1>

                <!-- Meta Metadata -->
                <div class="flex items-center flex-wrap gap-x-4 gap-y-2 text-xs text-muted-foreground font-medium pt-1">
                    <div class="flex items-center gap-1">
                        <Calendar class="h-4 w-4 text-muted-foreground/80" />
                        <span>{{ props.post.status === 'published' ? formatDate(props.post.published_at) : (props.post.status === 'scheduled' ? 'Scheduled for ' + formatDate(props.post.scheduled_at) : 'Draft (Belum Rilis)') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <BookOpen class="h-4 w-4 text-muted-foreground/80" />
                        <span>{{ props.post.reading_time || 1 }} menit membaca</span>
                    </div>
                </div>
            </header>

            <!-- Cover Image -->
            <div v-if="props.post.cover_image" class="w-full aspect-[21/9] rounded-2xl overflow-hidden border border-border bg-neutral-100 dark:bg-neutral-800 mb-8">
                <img
                    :src="props.post.cover_image.urls.large || props.post.cover_image.urls.original"
                    :alt="props.post.title"
                    class="w-full h-full object-cover"
                />
            </div>

            <!-- Excerpt (if custom) -->
            <div v-if="props.post.excerpt" class="border-l-4 border-primary/60 pl-4 py-1 italic text-muted-foreground text-sm leading-relaxed mb-8 bg-neutral-50/50 dark:bg-neutral-900/10 pr-2 rounded-r-lg">
                {{ props.post.excerpt }}
            </div>

            <!-- Article Content (Render HTML with Tailwind Typography) -->
            <article class="prose prose-neutral dark:prose-invert max-w-none prose-headings:font-bold prose-a:text-primary prose-img:rounded-xl prose-pre:bg-neutral-950 prose-pre:text-neutral-50 prose-pre:rounded-xl">
                <div v-html="props.post.content"></div>
            </article>
        </div>
    </div>
</template>
