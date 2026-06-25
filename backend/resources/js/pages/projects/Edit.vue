<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    ArrowLeft,
    ImageIcon,
    Search,
    Star,
    X,
    Sparkles,
    Check,
    FileText,
    Images,
    Plus,
    Trash2,
    GripVertical,
    ExternalLink
} from '@lucide/vue';
import { index as projectsIndex, show as projectsShow } from '@/routes/projects';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import CKEditor from '@/components/CKEditor.vue';

const props = defineProps<{
    project: any;
    categories: any[];
    technologies: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Proyek',
                href: projectsIndex(),
            },
            {
                title: 'Edit Proyek',
                href: '#',
            },
        ],
    },
});

// Tab Management
const activeTab = ref('general');

const form = useForm({
    title: props.project.title,
    slug: props.project.slug,
    short_description: props.project.short_description,
    full_description: props.project.full_description || '',
    category_id: props.project.category_id,
    cover_image_id: props.project.cover_image_id,
    cover_image_caption: props.project.cover_image_caption || '',
    status: props.project.status,
    is_featured: props.project.is_featured,
    order: props.project.order,
    live_url: props.project.live_url || '',
    github_url: props.project.github_url || '',
    telegram_url: props.project.telegram_url || '',
    technology_ids: props.project.technologies.map((t: any) => t.id),
    gallery: props.project.gallery_images.map((img: any) => ({
        media_id: img.id,
        order: img.pivot.order,
        caption: img.pivot.caption || '',
        urls: img.urls,
        original_filename: img.original_filename
    })) as Array<{
        media_id: number;
        order: number;
        caption: string;
        urls: any;
        original_filename: string;
    }>
});

// Slugify Utility
const slugify = (text: string) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
};

const isSlugManuallyEdited = ref(true);

watch(() => form.title, (newTitle) => {
    if (!isSlugManuallyEdited.value) {
        form.slug = slugify(newTitle);
    }
});

// Cover Image Picker
const coverMediaOpen = ref(false);
const selectedCoverMedia = ref<any | null>(props.project.cover_image);

const selectCoverImage = (media: any) => {
    selectedCoverMedia.value = media;
    form.cover_image_id = media.id;
    if (media.alt_text && !form.cover_image_caption) {
        form.cover_image_caption = media.alt_text;
    }
};

const removeCoverImage = () => {
    selectedCoverMedia.value = null;
    form.cover_image_id = null;
    form.cover_image_caption = '';
};

// Technologies Search
const techSearch = ref('');
const filteredTechnologies = computed(() => {
    if (!techSearch.value) return props.technologies;
    const q = techSearch.value.toLowerCase();
    return props.technologies.filter(t => t.name.toLowerCase().includes(q));
});

// Gallery Picker Modal
const galleryMediaOpen = ref(false);

const addGalleryImage = (media: any) => {
    // Avoid duplicates
    if (form.gallery.some(img => img.media_id === media.id)) {
        return;
    }
    
    form.gallery.push({
        media_id: media.id,
        order: form.gallery.length,
        caption: media.alt_text || '',
        urls: media.urls,
        original_filename: media.original_filename
    });
};

const removeGalleryImage = (index: number) => {
    form.gallery.splice(index, 1);
    // Re-sequence order fields
    form.gallery.forEach((item, idx) => {
        item.order = idx;
    });
};

// Gallery Drag and Drop Reordering
const draggedGalIndex = ref<number | null>(null);
const dragOverGalIndex = ref<number | null>(null);

const onGalDragStart = (index: number, e: DragEvent) => {
    draggedGalIndex.value = index;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', index.toString());
    }
};

const onGalDragOver = (index: number) => {
    dragOverGalIndex.value = index;
};

const onGalDragEnd = () => {
    if (draggedGalIndex.value !== null && dragOverGalIndex.value !== null && draggedGalIndex.value !== dragOverGalIndex.value) {
        const draggedItem = form.gallery[draggedGalIndex.value];
        form.gallery.splice(draggedGalIndex.value, 1);
        form.gallery.splice(dragOverGalIndex.value, 0, draggedItem);

        // Re-index orders
        form.gallery.forEach((item, idx) => {
            item.order = idx;
        });
    }
    draggedGalIndex.value = null;
    dragOverGalIndex.value = null;
};

const submit = () => {
    form.put(`/admin-cms/projects/${props.project.id}`, {
        onSuccess: () => {
            // Success
        }
    });
};
</script>

<template>
    <Head :title="`Edit Proyek: ${project.title}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto max-w-5xl mx-auto w-full">
        <!-- Header -->
        <div class="flex items-center gap-3 border-b border-sidebar-border/70 pb-4">
            <Link :href="projectsIndex()">
                <Button variant="ghost" size="icon" class="h-9 w-9 rounded-lg border border-sidebar-border/70 cursor-pointer">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
            </Link>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold tracking-tight">Edit Proyek</h1>
                    <span
                        class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase"
                        :class="project.status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300'"
                    >
                        {{ project.status }}
                    </span>
                </div>
                <p class="text-xs text-muted-foreground">
                    Perbarui pengaturan detail, tulis studi kasus visual, dan urutkan gambar galeri Anda.
                </p>
            </div>
            <div>
                <a :href="projectsShow({ project: project.id }).url" target="_blank">
                    <Button type="button" variant="outline" class="h-9 text-xs flex items-center gap-1.5 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-800 border-sidebar-border/80">
                        <ExternalLink class="h-3.5 w-3.5" />
                        Preview Studi Kasus
                    </Button>
                </a>
            </div>
        </div>

        <!-- Custom tabs navigation -->
        <div class="flex border-b border-sidebar-border/70 gap-2">
            <button
                type="button"
                @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <Sparkles class="h-4 w-4" />
                Informasi Utama
            </button>
            <button
                type="button"
                @click="activeTab = 'narrative'"
                :class="activeTab === 'narrative' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <FileText class="h-4 w-4" />
                Studi Kasus (Detail)
            </button>
            <button
                type="button"
                @click="activeTab = 'gallery'"
                :class="activeTab === 'gallery' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <Images class="h-4 w-4" />
                Galeri Visual
            </button>
        </div>

        <!-- Form content -->
        <form @submit.prevent="submit">
            <!-- TAB 1: GENERAL INFO -->
            <div v-show="activeTab === 'general'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left panel: Form fields -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                        <h2 class="text-sm font-bold text-foreground">Informasi Utama</h2>
                        
                        <!-- Title -->
                        <div class="grid gap-2">
                            <Label for="title" class="font-semibold text-xs text-foreground">Judul Proyek <span class="text-red-500">*</span></Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                required
                            />
                            <InputError :message="form.errors.title" />
                        </div>

                        <!-- Slug -->
                        <div class="grid gap-2">
                            <Label for="slug" class="font-semibold text-xs text-foreground">Slug Proyek <span class="text-red-500">*</span></Label>
                            <Input
                                id="slug"
                                v-model="form.slug"
                                required
                                placeholder="Contoh: ecommerce-microservices"
                                class="font-mono text-xs"
                            />
                            <p class="text-[10px] text-muted-foreground">URL unik: domain.com/proyek/<strong>{{ form.slug }}</strong></p>
                            <InputError :message="form.errors.slug" />
                        </div>

                        <!-- Category & Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="category" class="font-semibold text-xs text-foreground">Kategori <span class="text-red-500">*</span></Label>
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    required
                                    class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                >
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        {{ cat.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.category_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="order" class="font-semibold text-xs text-foreground">Urutan Tampil</Label>
                                <Input
                                    id="order"
                                    type="number"
                                    v-model.number="form.order"
                                    min="0"
                                    required
                                />
                                <InputError :message="form.errors.order" />
                            </div>
                        </div>

                        <!-- Short Description -->
                        <div class="grid gap-2">
                            <Label for="short_description" class="font-semibold text-xs text-foreground">Deskripsi Singkat <span class="text-red-500">*</span></Label>
                            <textarea
                                id="short_description"
                                v-model="form.short_description"
                                rows="3"
                                required
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                            <p class="text-[10px] text-muted-foreground text-right">{{ form.short_description.length }}/1000 karakter</p>
                            <InputError :message="form.errors.short_description" />
                        </div>
                    </div>

                    <!-- External Links -->
                    <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                        <h2 class="text-sm font-bold text-foreground">Link Eksternal (Opsional)</h2>
                        
                        <div class="space-y-3">
                            <div class="grid gap-2">
                                <Label for="live_url" class="font-semibold text-xs text-foreground">Live Demo URL</Label>
                                <Input
                                    id="live_url"
                                    v-model="form.live_url"
                                    type="url"
                                />
                                <InputError :message="form.errors.live_url" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="github_url" class="font-semibold text-xs text-foreground">GitHub Repo URL</Label>
                                <Input
                                    id="github_url"
                                    v-model="form.github_url"
                                    type="url"
                                />
                                <InputError :message="form.errors.github_url" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="telegram_url" class="font-semibold text-xs text-foreground">Telegram Bot URL</Label>
                                <Input
                                    id="telegram_url"
                                    v-model="form.telegram_url"
                                    type="url"
                                />
                                <InputError :message="form.errors.telegram_url" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right panel: Cover, Status, Technologies -->
                <div class="space-y-6">
                    <!-- Cover Image Picker -->
                    <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs flex flex-col justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-foreground">Gambar Cover</h2>
                            
                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-sidebar-border rounded-xl p-3 min-h-[160px] bg-neutral-50/50 dark:bg-neutral-900/10 mt-3">
                                <div v-if="selectedCoverMedia" class="w-full relative group rounded-lg overflow-hidden border border-sidebar-border">
                                    <img
                                        :src="selectedCoverMedia.urls.medium"
                                        alt="Cover preview"
                                        class="w-full h-32 object-cover"
                                    />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center justify-center gap-2">
                                        <Button type="button" size="sm" variant="secondary" @click="coverMediaOpen = true" class="cursor-pointer">
                                            Ganti
                                        </Button>
                                        <Button type="button" size="icon" variant="destructive" @click="removeCoverImage" class="h-8 w-8 cursor-pointer">
                                            <X class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 flex flex-col items-center gap-2">
                                    <ImageIcon class="h-8 w-8 text-neutral-400" />
                                    <p class="text-xs text-muted-foreground">Belum ada cover terpilih</p>
                                    <Button type="button" size="sm" variant="outline" @click="coverMediaOpen = true" class="mt-1 cursor-pointer">
                                        Pilih dari Media Library
                                    </Button>
                                </div>
                            </div>
                            <InputError :message="form.errors.cover_image_id" />
                        </div>

                        <!-- Cover Image Caption Input -->
                        <div class="grid gap-1.5 mt-4 border-t border-sidebar-border/50 pt-3" v-if="selectedCoverMedia">
                            <Label for="cover_image_caption" class="text-[11px] font-bold text-muted-foreground uppercase">Keterangan Cover (Caption)</Label>
                            <Input
                                id="cover_image_caption"
                                v-model="form.cover_image_caption"
                                placeholder="Contoh: Tampilan dashboard analitik platform."
                                class="h-8 text-xs bg-card"
                            />
                            <InputError :message="form.errors.cover_image_caption" />
                        </div>
                    </div>

                    <!-- Publication & settings -->
                    <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                        <h2 class="text-sm font-bold text-foreground">Status & Pengaturan</h2>
                        
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="status" class="font-semibold text-xs text-foreground">Status Publikasi</Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                >
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <!-- Featured Toggle -->
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-sidebar-border bg-neutral-50/30 dark:bg-neutral-900/10 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors">
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="rounded border-input text-primary focus:ring-primary h-4.5 w-4.5 cursor-pointer"
                                />
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold flex items-center gap-1.5 text-foreground">
                                        <Star class="h-3.5 w-3.5 text-amber-500 fill-amber-500" v-if="form.is_featured" />
                                        Proyek Unggulan
                                    </span>
                                    <span class="text-[10px] text-muted-foreground">Tampilkan prioritas di Homepage</span>
                                </div>
                            </label>
                            <InputError :message="form.errors.is_featured" />
                        </div>
                    </div>

                    <!-- Tech Stack tags -->
                    <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-foreground">Tech Stack Tags</h2>
                            <span class="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold">
                                {{ form.technology_ids.length }} terpilih
                            </span>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <Search class="absolute top-1/2 left-2.5 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="techSearch"
                                placeholder="Cari teknologi..."
                                class="pl-8 h-8 text-xs bg-muted/40"
                            />
                        </div>

                        <!-- Checklist -->
                        <div class="border border-sidebar-border rounded-lg max-h-[180px] overflow-y-auto p-2 bg-neutral-50/30 dark:bg-neutral-900/10 space-y-1">
                            <label
                                v-for="tech in filteredTechnologies"
                                :key="tech.id"
                                class="flex items-center justify-between p-1.5 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md cursor-pointer text-xs"
                            >
                                <div class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        :value="tech.id"
                                        v-model="form.technology_ids"
                                        class="rounded border-input text-primary focus:ring-primary h-3.5 w-3.5 cursor-pointer"
                                    />
                                    <span class="font-medium text-foreground">{{ tech.name }}</span>
                                </div>
                                <span class="text-[9px] text-muted-foreground uppercase font-semibold bg-neutral-100 dark:bg-neutral-800 px-1.5 py-0.5 rounded">
                                    {{ tech.category }}
                                </span>
                            </label>
                            <div v-if="filteredTechnologies.length === 0" class="text-center py-4 text-[11px] text-muted-foreground">
                                Teknologi tidak ditemukan.
                            </div>
                        </div>
                        <InputError :message="form.errors.technology_ids" />
                    </div>
                </div>
            </div>

            <!-- TAB 2: TIPTAP RICH TEXT EDITOR -->
            <div v-show="activeTab === 'narrative'" class="space-y-4">
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-foreground">Studi Kasus Naratif</h2>
                            <p class="text-[11px] text-muted-foreground mt-0.5">Tulis detail lengkap mengenai problem, solusi teknis, dan arsitektur proyek. Anda dapat menyisipkan gambar langsung.</p>
                        </div>
                    </div>
                    
                    <CKEditor
                        v-model="form.full_description"
                        placeholder="Tulis narrative studi kasus proyek Anda..."
                    />
                    <InputError :message="form.errors.full_description" />
                </div>
            </div>

            <!-- TAB 3: GALLERY BUILDER -->
            <div v-show="activeTab === 'gallery'" class="space-y-4">
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-5 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-sidebar-border/70">
                        <div>
                            <h2 class="text-sm font-bold text-foreground">Galeri Visual Proyek</h2>
                            <p class="text-[11px] text-muted-foreground mt-0.5">Urutkan visual dengan cara men-drag baris gambar, lalu isi keterangan/caption visual tersebut.</p>
                        </div>
                        <Button type="button" size="sm" variant="outline" @click="galleryMediaOpen = true" class="cursor-pointer">
                            <Plus class="mr-2 h-3.5 w-3.5" />
                            Tambah Gambar Ke Galeri
                        </Button>
                    </div>

                    <!-- Images Grid (Drag to sort) -->
                    <div v-if="form.gallery.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="(item, index) in form.gallery"
                            :key="item.media_id"
                            draggable="true"
                            @dragstart="onGalDragStart(index, $event)"
                            @dragover.prevent="onGalDragOver(index)"
                            @dragend="onGalDragEnd"
                            class="group rounded-xl border border-sidebar-border bg-neutral-50/50 dark:bg-neutral-900/10 overflow-hidden relative flex flex-col transition-all duration-150 border-l-2"
                            :class="{
                                'opacity-40': index === draggedGalIndex,
                                'border-l-primary bg-primary/5': index === dragOverGalIndex && index !== draggedGalIndex,
                                'border-l-transparent border-sidebar-border': index !== dragOverGalIndex || index === draggedGalIndex
                            }"
                        >
                            <!-- Thumbnail & drag handle overlay -->
                            <div class="h-36 w-full relative bg-neutral-100 dark:bg-neutral-800 overflow-hidden flex items-center justify-center border-b border-sidebar-border">
                                <img
                                    :src="item.urls.medium"
                                    :alt="item.caption || item.original_filename"
                                    class="h-full w-full object-cover"
                                />
                                
                                <!-- Drag Handle & Remove icons -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center justify-between p-2">
                                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-white/10 text-white backdrop-blur-xs cursor-grab active:cursor-grabbing hover:bg-white/20 transition-colors">
                                        <GripVertical class="h-4 w-4" />
                                    </div>
                                    <Button
                                        type="button"
                                        size="icon"
                                        variant="destructive"
                                        class="h-8 w-8 cursor-pointer rounded-lg bg-red-600/95 text-white hover:bg-red-600"
                                        @click="removeGalleryImage(index)"
                                        title="Hapus dari galeri"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>

                                <!-- Index badge -->
                                <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/50 backdrop-blur-xs text-[10px] text-white font-bold">
                                    Slide #{{ index + 1 }}
                                </div>
                            </div>

                            <!-- Caption input -->
                            <div class="p-3 bg-card space-y-1.5 flex-1">
                                <Label :for="`caption-${item.media_id}`" class="text-[10px] font-bold text-muted-foreground uppercase">Keterangan Gambar (Caption)</Label>
                                <textarea
                                    :id="`caption-${item.media_id}`"
                                    v-model="item.caption"
                                    rows="2"
                                    placeholder="Contoh: Diagram arsitektur database proyek."
                                    class="flex min-h-[50px] w-full rounded-md border border-input bg-transparent px-2.5 py-1.5 text-xs ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Empty gallery state -->
                    <div v-else class="text-center py-12 flex flex-col items-center justify-center gap-2 border border-dashed border-sidebar-border rounded-xl bg-neutral-50/10">
                        <ImageIcon class="h-8 w-8 text-neutral-400" />
                        <p class="text-xs text-muted-foreground">Galeri visual masih kosong</p>
                        <Button type="button" size="sm" variant="outline" @click="galleryMediaOpen = true" class="mt-1 cursor-pointer">
                            Pilih dari Media Library
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Floating / Action Save Bar -->
            <div class="flex items-center justify-between border-t border-sidebar-border/70 pt-4 mt-6">
                <span class="text-xs text-muted-foreground italic">
                    {{ form.isDirty ? '* Ada perubahan belum disimpan' : 'Semua perubahan tersimpan' }}
                </span>
                
                <div class="flex gap-2">
                    <Link :href="projectsIndex()">
                        <Button type="button" variant="outline" class="cursor-pointer h-10 px-4">
                            Kembali ke Daftar
                        </Button>
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer h-10 px-6 shadow-sm flex items-center gap-2"
                    >
                        <Check class="h-4 w-4" v-if="!form.processing" />
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Proyek' }}
                    </Button>
                </div>
            </div>
        </form>

        <!-- Media Library Modal for Cover Selection -->
        <MediaLibraryModal
            :open="coverMediaOpen"
            @update:open="coverMediaOpen = $event"
            @select="selectCoverImage"
        />

        <!-- Media Library Modal for Gallery Selection -->
        <MediaLibraryModal
            :open="galleryMediaOpen"
            @update:open="galleryMediaOpen = $event"
            @select="addGalleryImage"
        />
    </div>
</template>
