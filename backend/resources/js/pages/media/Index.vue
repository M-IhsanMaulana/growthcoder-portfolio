<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MediaManager from '@/components/MediaManager.vue';
import { index as mediaIndex } from '@/routes/media';

defineProps<{
    media: {
        data: any[];
        next_page_url: string | null;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Media Library',
                href: mediaIndex(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Media Library" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <div class="flex flex-col gap-1 border-b border-border pb-4">
            <h1 class="text-2xl font-bold tracking-tight">Media Library</h1>
            <p class="text-sm text-muted-foreground">
                Manage your portfolio image assets, edit Alt texts, and optimize asset references.
            </p>
        </div>

        <MediaManager mode="page" :initial-media="media" />
    </div>
</template>
