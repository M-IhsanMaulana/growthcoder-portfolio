<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import {
    ArrowLeft,
    Folder,
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
    GripVertical
} from '@lucide/vue';
import { index as projectsIndex } from '@/routes/projects';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import CKEditor from '@/components/CKEditor.vue';

const props = defineProps<{
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
                title: 'Tambah Proyek Baru',
                href: '#',
            },
        ],
    },
});

// Tab Management
const activeTab = ref('general');

const form = useForm({
    title: '',
    slug: '',
    role: '',
    short_description: '',
    full_description: '',
    category_id: '' as string | number,
    cover_image_id: null as number | null,
    cover_image_caption: '',
    status: 'draft',
    is_featured: false,
    order: 0,
    live_url: '',
    github_url: '',
    telegram_url: '',
    technology_ids: [] as number[],
    key_features: [] as Array<{
        title: string;
        description: string;
        icon: string;
    }>,
    gallery: [] as Array<{
        media_id: number;
        order: number;
        caption: string;
        urls: any;
        original_filename: string;
    }>
});

const addKeyFeature = () => {
    form.key_features.push({
        title: '',
        description: '',
        icon: 'Zap'
    });
};

const removeKeyFeature = (index: number) => {
    form.key_features.splice(index, 1);
};

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

const isSlugManuallyEdited = ref(false);

watch(() => form.title, (newTitle) => {
    if (!isSlugManuallyEdited.value) {
        form.slug = slugify(newTitle);
    }
});

// Cover Image Selector
const coverMediaOpen = ref(false);
const selectedCoverMedia = ref<any | null>(null);

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

// Technologies List Search Filter
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
    form.post('/admin-cms/projects', {
        onSuccess: () => {
            // Success
        }
    });
};
</script>

<template>
    <Head title="Tambah Proyek Baru" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto w-full">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border/70 pb-4">
            <div class="flex items-center gap-3">
                <Link :href="projectsIndex()">
                    <Button variant="ghost" size="icon" class="h-9 w-9 rounded-xl border border-border/80 cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Tambah Proyek Baru</h1>
                    <p class="text-xs text-muted-foreground">
                        Buat entri portofolio baru dengan narasi lengkap dan galeri visual dalam satu langkah.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Link :href="projectsIndex()">
                    <Button type="button" variant="outline" class="cursor-pointer h-9 text-xs px-4">
                        Batal
                    </Button>
                </Link>
                <Button
                    type="button"
                    @click="submit"
                    :disabled="form.processing"
                    class="bg-primary text-white hover:bg-primary/90 font-semibold cursor-pointer h-9 text-xs px-5 shadow-xs flex items-center gap-2"
                >
                    <Check class="h-4 w-4" v-if="!form.processing" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Proyek' }}
                </Button>
            </div>
        </div>

        <!-- Custom tabs navigation -->
        <div class="flex border-b border-border/70 gap-2 bg-card p-1 rounded-2xl border border-border/60">
            <button
                type="button"
                @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <Sparkles class="h-4 w-4" />
                Informasi Utama
            </button>
            <button
                type="button"
                @click="activeTab = 'features'"
                :class="activeTab === 'features' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <Star class="h-4 w-4" />
                Fitur Utama (Key Features)
            </button>
            <button
                type="button"
                @click="activeTab = 'narrative'"
                :class="activeTab === 'narrative' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
            >
                <FileText class="h-4 w-4" />
                Studi Kasus (Detail)
            </button>
            <button
                type="button"
                @click="activeTab = 'gallery'"
                :class="activeTab === 'gallery' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
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
                    <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-5 shadow-2xs">
                        <h2 class="text-sm font-bold text-foreground">Informasi Utama</h2>
                        
                        <!-- Title -->
                        <div class="grid gap-2">
                            <Label for="title" class="font-semibold text-xs text-foreground">Judul Proyek <span class="text-red-500">*</span></Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                required
                                placeholder="Contoh: E-Commerce Microservices Architecture"
                                class="bg-card border-border/80"
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
                                @input="isSlugManuallyEdited = true"
                                placeholder="Contoh: ecommerce-microservices"
                                class="font-mono text-xs bg-card border-border/80"
                            />
                            <p class="text-[10px] text-muted-foreground">URL unik: domain.com/proyek/<strong>{{ form.slug || 'slug-otomatis' }}</strong></p>
                            <InputError :message="form.errors.slug" />
                        </div>

                        <!-- Role -->
                        <div class="grid gap-2">
                            <Label for="role" class="font-semibold text-xs text-foreground">Role / Tanggung Jawab Pengembang</Label>
                            <Input
                                id="role"
                                v-model="form.role"
                                placeholder="Contoh: Full-Stack Developer, Lead Backend Engineer"
                                class="text-xs bg-card border-border/80"
                            />
                            <InputError :message="form.errors.role" />
                        </div>

                        <!-- Category & Order -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="category" class="font-semibold text-xs text-foreground">Kategori <span class="text-red-500">*</span></Label>
                                <Select :model-value="String(form.category_id)" @update:model-value="(v) => form.category_id = String(v)">
                                    <SelectTrigger class="h-9 w-full text-xs bg-card border-border/80 font-medium">
                                        <SelectValue placeholder="Pilih Kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                            {{ cat.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
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
                                    class="bg-card border-border/80"
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
                                placeholder="Tuliskan rangkuman ringkas proyek ini untuk preview card portofolio..."
                                class="flex min-h-[80px] w-full rounded-md border border-border/80 bg-card px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                            <p class="text-[10px] text-muted-foreground text-right">{{ form.short_description.length }}/1000 karakter</p>
                            <InputError :message="form.errors.short_description" />
                        </div>
                    </div>

                    <!-- External Links -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-5 shadow-2xs">
                        <h2 class="text-sm font-bold text-foreground">Link Eksternal (Opsional)</h2>
                        
                        <div class="space-y-3">
                            <div class="grid gap-2">
                                <Label for="live_url" class="font-semibold text-xs text-foreground">Live Demo URL</Label>
                                <Input
                                    id="live_url"
                                    v-model="form.live_url"
                                    type="url"
                                    placeholder="https://example.com"
                                    class="bg-card border-border/80"
                                />
                                <InputError :message="form.errors.live_url" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="github_url" class="font-semibold text-xs text-foreground">GitHub Repo URL</Label>
                                <Input
                                    id="github_url"
                                    v-model="form.github_url"
                                    type="url"
                                    placeholder="https://github.com/username/project"
                                    class="bg-card border-border/80"
                                />
                                <InputError :message="form.errors.github_url" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="telegram_url" class="font-semibold text-xs text-foreground">Telegram Bot URL</Label>
                                <Input
                                    id="telegram_url"
                                    v-model="form.telegram_url"
                                    type="url"
                                    placeholder="https://t.me/your_bot"
                                    class="bg-card border-border/80"
                                />
                                <InputError :message="form.errors.telegram_url" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right panel: Cover, Status, Technologies -->
                <div class="space-y-6">
                    <!-- Cover Image Selector -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-5 shadow-2xs flex flex-col justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-foreground">Gambar Cover</h2>
                            
                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-border/80 rounded-xl p-3 min-h-[160px] bg-muted/20 mt-3">
                                <div v-if="selectedCoverMedia" class="w-full relative group rounded-lg overflow-hidden border border-border/80">
                                    <img
                                        :src="selectedCoverMedia.urls.medium"
                                        alt="Cover preview"
                                        class="w-full h-36 object-cover"
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
                                    <ImageIcon class="h-8 w-8 text-muted-foreground/60" />
                                    <p class="text-xs text-muted-foreground">Belum ada cover terpilih</p>
                                    <Button type="button" size="sm" variant="outline" @click="coverMediaOpen = true" class="mt-1 cursor-pointer">
                                        Pilih dari Media Library
                                    </Button>
                                </div>
                            </div>
                            <InputError :message="form.errors.cover_image_id" />
                        </div>

                        <!-- Cover Image Caption Input -->
                        <div class="grid gap-1.5 mt-4 border-t border-border/50 pt-3" v-if="selectedCoverMedia">
                            <Label for="cover_image_caption" class="text-[11px] font-bold text-muted-foreground uppercase">Keterangan Cover (Caption)</Label>
                            <Input
                                id="cover_image_caption"
                                v-model="form.cover_image_caption"
                                placeholder="Contoh: Tampilan dashboard analitik platform."
                                class="h-8 text-xs bg-card border-border/80"
                            />
                            <InputError :message="form.errors.cover_image_caption" />
                        </div>
                    </div>

                    <!-- Publish Status & Featured Settings -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-5 shadow-2xs">
                        <h2 class="text-sm font-bold text-foreground">Status & Pengaturan</h2>
                        
                        <div class="space-y-4">
                            <div class="grid gap-2">
                                <Label for="status" class="font-semibold text-xs text-foreground">Status Publikasi</Label>
                                <Select :model-value="form.status" @update:model-value="(v) => form.status = String(v)">
                                    <SelectTrigger class="h-9 w-full text-xs bg-card border-border/80 font-medium">
                                        <SelectValue placeholder="Pilih Status">
                                            <span class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full" :class="form.status === 'published' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                                {{ form.status === 'published' ? 'Published' : 'Draft' }}
                                            </span>
                                        </SelectValue>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">
                                            <span class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                                Draft
                                            </span>
                                        </SelectItem>
                                        <SelectItem value="published">
                                            <span class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                Published
                                            </span>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.status" />
                            </div>

                            <!-- Featured Project Toggle Box -->
                            <label
                                class="flex items-center justify-between p-3.5 rounded-xl border transition-all cursor-pointer select-none"
                                :class="form.is_featured
                                    ? 'border-amber-500/40 bg-amber-500/10 dark:bg-amber-500/15 shadow-2xs'
                                    : 'border-border/70 bg-card hover:bg-muted/30'"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-9 w-9 rounded-xl flex items-center justify-center transition-colors"
                                        :class="form.is_featured ? 'bg-amber-500 text-white shadow-xs' : 'bg-muted text-muted-foreground'"
                                    >
                                        <Star class="h-4.5 w-4.5" :class="{ 'fill-white': form.is_featured }" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-foreground">Proyek Unggulan</span>
                                        <span class="text-[10px] text-muted-foreground">Tampilkan prioritas di Homepage</span>
                                    </div>
                                </div>
                                <input
                                    type="checkbox"
                                    v-model="form.is_featured"
                                    class="h-4 w-4 rounded border-border text-amber-500 focus:ring-amber-500 cursor-pointer"
                                />
                            </label>
                            <InputError :message="form.errors.is_featured" />
                        </div>
                    </div>

                    <!-- Tech Stack Tagging -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-4 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-bold text-foreground">Tech Stack Tags</h2>
                            <Badge variant="secondary" class="text-[10px] font-bold">
                                {{ form.technology_ids.length }} terpilih
                            </Badge>
                        </div>

                        <!-- Selected Badges Quick Remove View -->
                        <div v-if="form.technology_ids.length > 0" class="flex flex-wrap gap-1.5 p-2 rounded-xl bg-muted/20 border border-border/50 max-h-24 overflow-y-auto">
                            <Badge
                                v-for="id in form.technology_ids"
                                :key="id"
                                variant="secondary"
                                class="text-[10px] gap-1 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors pr-1 cursor-pointer"
                                @click="form.technology_ids = form.technology_ids.filter(tId => tId !== id)"
                            >
                                <span>{{ technologies.find(t => t.id === id)?.name || id }}</span>
                                <X class="h-3 w-3 hover:text-destructive" />
                            </Badge>
                        </div>

                        <!-- Search Tech -->
                        <div class="relative">
                            <Search class="absolute top-1/2 left-2.5 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="techSearch"
                                placeholder="Cari teknologi..."
                                class="pl-8 pr-7 h-8 text-xs bg-card border-border/80"
                            />
                            <button
                                v-if="techSearch"
                                @click="techSearch = ''"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                            >
                                <X class="h-3 w-3" />
                            </button>
                        </div>

                        <!-- Scrollable checklist grid -->
                        <div class="border border-border/70 rounded-xl max-h-[220px] overflow-y-auto p-1.5 bg-card space-y-1">
                            <label
                                v-for="tech in filteredTechnologies"
                                :key="tech.id"
                                class="flex items-center justify-between p-2 px-2.5 rounded-lg cursor-pointer text-xs transition-all border"
                                :class="form.technology_ids.includes(tech.id)
                                    ? 'bg-primary/10 border-primary/30 text-primary font-semibold'
                                    : 'border-transparent hover:bg-muted/40 text-foreground'"
                            >
                                <div class="flex items-center gap-2.5">
                                    <input
                                        type="checkbox"
                                        :value="tech.id"
                                        v-model="form.technology_ids"
                                        class="rounded border-border text-primary focus:ring-primary h-3.5 w-3.5 cursor-pointer"
                                    />
                                    <span>{{ tech.name }}</span>
                                </div>
                                <span
                                    class="text-[9px] uppercase font-bold px-2 py-0.5 rounded-full border"
                                    :class="{
                                        'bg-sky-500/10 text-sky-600 border-sky-500/20 dark:text-sky-400': (tech.category || '').toLowerCase().includes('front'),
                                        'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 dark:text-emerald-400': (tech.category || '').toLowerCase().includes('back'),
                                        'bg-amber-500/10 text-amber-600 border-amber-500/20 dark:text-amber-400': (tech.category || '').toLowerCase().includes('data'),
                                        'bg-purple-500/10 text-purple-600 border-purple-500/20 dark:text-purple-400': (tech.category || '').toLowerCase().includes('devops') || (tech.category || '').toLowerCase().includes('tool'),
                                        'bg-slate-500/10 text-slate-600 border-slate-500/20 dark:text-slate-400': !(tech.category || '').toLowerCase().match(/front|back|data|devops|tool/)
                                    }"
                                >
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

            <!-- TAB: KEY FEATURES -->
            <div v-show="activeTab === 'features'" class="space-y-4">
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-6 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-sidebar-border/70">
                        <div>
                            <h2 class="text-sm font-bold text-foreground">Fitur Utama (Key Features)</h2>
                            <p class="text-[11px] text-muted-foreground mt-0.5">Kelola poin-poin fitur unggulan proyek yang ditampilkan di halaman detail.</p>
                        </div>
                        <Button type="button" size="sm" @click="addKeyFeature" class="flex items-center gap-1.5 cursor-pointer">
                            <Plus class="h-4 w-4" />
                            Tambah Fitur
                        </Button>
                    </div>

                    <!-- List of Key Features -->
                    <div v-if="form.key_features.length > 0" class="space-y-4">
                        <div
                            v-for="(feature, index) in form.key_features"
                            :key="index"
                            class="p-4 rounded-lg border border-sidebar-border/80 bg-neutral-50/50 dark:bg-neutral-900/40 space-y-4"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs font-bold text-muted-foreground">Fitur #{{ index + 1 }}</span>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    @click="removeKeyFeature(index)"
                                    class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 h-8 text-xs cursor-pointer"
                                >
                                    <Trash2 class="h-3.5 w-3.5 mr-1" />
                                    Hapus Fitur
                                </Button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2 space-y-1.5">
                                    <Label class="text-xs font-semibold">Judul Fitur <span class="text-red-500">*</span></Label>
                                    <Input
                                        v-model="feature.title"
                                        placeholder="misal: High Performance"
                                        class="h-9 text-xs"
                                        required
                                    />
                                </div>
                                <div class="space-y-1.5">
                                    <Label class="text-xs font-semibold">Nama Ikon (Lucide Icon)</Label>
                                    <Input
                                        v-model="feature.icon"
                                        placeholder="misal: Zap, Search, Code2, Smartphone"
                                        class="h-9 text-xs"
                                    />
                                </div>
                                <div class="md:col-span-3 space-y-1.5">
                                    <Label class="text-xs font-semibold">Deskripsi Singkat Fitur</Label>
                                    <textarea
                                        v-model="feature.description"
                                        rows="2"
                                        placeholder="Penjelasan ringkas mengenai keunggulan fitur ini..."
                                        class="w-full px-3 py-2 rounded-md border border-input bg-background text-xs focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-8 text-center border border-dashed border-sidebar-border rounded-lg">
                        <Star class="h-8 w-8 mx-auto text-muted-foreground/50 mb-2" />
                        <p class="text-xs font-medium text-muted-foreground">Belum ada fitur utama yang ditambahkan.</p>
                        <Button type="button" variant="outline" size="sm" @click="addKeyFeature" class="mt-3 text-xs cursor-pointer">
                            <Plus class="h-3.5 w-3.5 mr-1" />
                            Tambah Fitur Pertama
                        </Button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: TIPTAP RICH TEXT EDITOR -->
            <div v-show="activeTab === 'narrative'" class="space-y-4">
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <div>
                        <h2 class="text-sm font-bold text-foreground">Studi Kasus Naratif</h2>
                        <p class="text-[11px] text-muted-foreground mt-0.5">Tulis detail lengkap mengenai problem, solusi teknis, dan arsitektur proyek. Anda dapat menyisipkan gambar langsung.</p>
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
                                        class="h-8 w-8 cursor-pointer rounded-lg bg-red-600/90 text-white hover:bg-red-600"
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
                    {{ form.isDirty ? '* Ada input belum disimpan' : 'Semua perubahan terinput' }}
                </span>
                
                <div class="flex gap-2">
                    <Link :href="projectsIndex()">
                        <Button type="button" variant="outline" class="cursor-pointer h-10 px-4">
                            Batal
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
