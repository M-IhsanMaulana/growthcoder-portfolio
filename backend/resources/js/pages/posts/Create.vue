<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    ArrowLeft,
    FileText,
    ImageIcon,
    Search,
    X,
    Sparkles,
    Check,
    Calendar,
    Globe,
    BookOpen,
    Trash2,
    Settings,
    Layers,
    Link as LinkIcon
} from '@lucide/vue';
import { index as postsIndex } from '@/routes/posts';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import CKEditor from '@/components/CKEditor.vue';

const props = defineProps<{
    categories: any[];
    posts: any[]; // Existing posts for related posts selection
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Artikel',
                href: postsIndex(),
            },
            {
                title: 'Tulis Artikel Baru',
                href: '#',
            },
        ],
    },
});

// Tab Management
const activeTab = ref('content');

const form = useForm({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    status: 'draft',
    scheduled_at: '',
    cover_image_id: null as number | null,
    meta_title: '',
    meta_description: '',
    category_ids: [] as number[],
    related_post_ids: [] as number[],
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
};

const removeCoverImage = () => {
    selectedCoverMedia.value = null;
    form.cover_image_id = null;
};

// Category Selection Helper
const toggleCategory = (catId: number) => {
    const idx = form.category_ids.indexOf(catId);
    if (idx > -1) {
        form.category_ids.splice(idx, 1);
    } else {
        form.category_ids.push(catId);
    }
};

// Manual Related Posts Search
const relatedSearch = ref('');
const filteredPostsForRelated = computed(() => {
    const q = relatedSearch.value.toLowerCase();
    // Exclude posts that are already selected
    return props.posts.filter(p => 
        !form.related_post_ids.includes(p.id) &&
        p.title.toLowerCase().includes(q)
    );
});

const selectRelatedPost = (post: any) => {
    form.related_post_ids.push(post.id);
    relatedSearch.value = '';
};

const removeRelatedPost = (postId: number) => {
    form.related_post_ids = form.related_post_ids.filter(id => id !== postId);
};

const getPostTitleById = (postId: number) => {
    const found = props.posts.find(p => p.id === postId);
    return found ? found.title : '';
};

// Google SEO Snippet Preview helper
const seoTitlePreview = computed(() => {
    return form.meta_title || form.title || 'Judul Artikel Anda';
});

const seoDescPreview = computed(() => {
    if (form.meta_description) return form.meta_description;
    if (form.excerpt) return form.excerpt;
    if (form.content) {
        const text = form.content.replace(/<[^>]*>/g, '');
        return text.length > 155 ? text.substring(0, 155) + '...' : text;
    }
    return 'Tulis konten artikel atau masukkan kustom deskripsi meta SEO di sini...';
});

const submit = () => {
    form.post('/admin-cms/posts', {
        onSuccess: () => {
            // Handled
        }
    });
};
</script>

<template>
    <Head title="Tulis Artikel Baru" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto w-full">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border/70 pb-4">
            <div class="flex items-center gap-3">
                <Link :href="postsIndex()">
                    <Button variant="ghost" size="icon" class="h-9 w-9 rounded-xl border border-border/80 cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Tulis Artikel Baru</h1>
                    <p class="text-xs text-muted-foreground">
                        Buat postingan blog baru dengan editor rich text, jadwalkan tayang, dan optimasi SEO.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Link :href="postsIndex()">
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
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Artikel' }}
                </Button>
            </div>
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <!-- Navigation Tabs -->
            <div class="flex border-b border-border/70 gap-2 bg-card p-1 rounded-2xl border border-border/60">
                <button
                    type="button"
                    @click="activeTab = 'content'"
                    class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'content' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <FileText class="h-4 w-4" />
                    Konten Utama
                </button>
                <button
                    type="button"
                    @click="activeTab = 'media'"
                    class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'media' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <ImageIcon class="h-4 w-4" />
                    Kategori & Media
                </button>
                <button
                    type="button"
                    @click="activeTab = 'publishing'"
                    class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'publishing' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <Calendar class="h-4 w-4" />
                    Status & Jadwal
                </button>
                <button
                    type="button"
                    @click="activeTab = 'related'"
                    class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'related' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <LinkIcon class="h-4 w-4" />
                    Artikel Terkait
                </button>
                <button
                    type="button"
                    @click="activeTab = 'seo'"
                    class="py-2.5 px-4 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'seo' ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary' : 'text-muted-foreground hover:text-foreground'"
                >
                    <Globe class="h-4 w-4" />
                    Pengaturan SEO
                </button>
            </div>

            <!-- Tab Content Panel -->
            <div class="bg-card border border-border/70 rounded-2xl p-6 md:p-8 shadow-2xs min-h-[400px]">
                
                <!-- Tab 1: Konten Utama -->
                <div v-show="activeTab === 'content'" class="space-y-6">
                    <!-- Title -->
                    <div class="grid gap-2">
                        <Label for="post-title" class="text-xs font-bold text-foreground">Judul Artikel <span class="text-destructive">*</span></Label>
                        <Input
                            id="post-title"
                            v-model="form.title"
                            placeholder="Contoh: Menguasai Dependency Injection di Laravel 13"
                            class="bg-transparent border-sidebar-border font-medium text-base h-11"
                            required
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
                    <div class="grid gap-2">
                        <Label for="post-slug" class="text-xs font-bold text-foreground">Slug URL</Label>
                        <div class="relative">
                            <Input
                                id="post-slug"
                                v-model="form.slug"
                                @input="isSlugManuallyEdited = true"
                                placeholder="menguasai-dependency-injection-di-laravel-13"
                                class="font-mono bg-transparent border-sidebar-border pl-14"
                                required
                            />
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground font-mono">
                                /blog/
                            </div>
                        </div>
                        <InputError :message="form.errors.slug" />
                    </div>

                    <!-- Content (CKEditor) -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Isi Artikel <span class="text-destructive">*</span></Label>
                        <CKEditor
                            v-model="form.content"
                            placeholder="Tulis artikel teknis Anda di sini secara mendalam..."
                        />
                        <InputError :message="form.errors.content" />
                    </div>
                </div>

                <!-- Tab 2: Kategori & Media -->
                <div v-show="activeTab === 'media'" class="space-y-6">
                    <!-- Cover Image -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Gambar Cover</Label>
                        <div class="flex flex-col sm:flex-row items-start gap-4">
                            <!-- Image Box -->
                            <div class="h-36 w-60 rounded-xl border border-sidebar-border/70 overflow-hidden bg-neutral-50 dark:bg-neutral-900/50 flex items-center justify-center relative shadow-inner">
                                <img
                                    v-if="selectedCoverMedia"
                                    :src="selectedCoverMedia.urls.large || selectedCoverMedia.urls.original"
                                    alt="Cover preview"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex flex-col items-center justify-center p-4 text-center">
                                    <ImageIcon class="h-8 w-8 text-muted-foreground/60 mb-2" />
                                    <span class="text-xs text-muted-foreground">Belum ada cover terpilih</span>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex flex-col gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="coverMediaOpen = true"
                                    class="cursor-pointer font-medium text-xs rounded-lg border-sidebar-border"
                                >
                                    Pilih Gambar Cover
                                </Button>
                                <Button
                                    v-if="selectedCoverMedia"
                                    type="button"
                                    variant="destructive"
                                    @click="removeCoverImage"
                                    class="cursor-pointer font-medium text-xs rounded-lg bg-destructive text-white hover:bg-destructive/90"
                                >
                                    Hapus Cover
                                </Button>
                                <p class="text-[10px] text-muted-foreground max-w-xs mt-1">
                                    Format gambar direkomendasikan berukuran lanskap (16:9) dengan resolusi minimal 1200x630 piksel untuk open graph yang optimal.
                                </p>
                            </div>
                        </div>
                        <InputError :message="form.errors.cover_image_id" />
                    </div>

                    <!-- Excerpt -->
                    <div class="grid gap-2">
                        <Label for="post-excerpt" class="text-xs font-bold text-foreground">Ringkasan Artikel (Excerpt) - Opsional</Label>
                        <textarea
                            id="post-excerpt"
                            v-model="form.excerpt"
                            rows="3"
                            placeholder="Tulis ringkasan singkat artikel di sini. Jika dikosongkan, sistem akan otomatis memotong 150 karakter pertama dari konten artikel."
                            class="flex w-full rounded-md border border-sidebar-border bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring"
                        ></textarea>
                        <InputError :message="form.errors.excerpt" />
                    </div>

                    <!-- Categories Selector -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Kategori Artikel <span class="text-destructive">*</span></Label>
                        <div v-if="categories.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 bg-neutral-50/50 dark:bg-neutral-900/20 border border-sidebar-border/55 rounded-xl p-4">
                            <label
                                v-for="cat in categories"
                                :key="cat.id"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg border border-sidebar-border bg-card hover:bg-neutral-50 dark:hover:bg-neutral-900 cursor-pointer select-none transition-colors"
                                :class="form.category_ids.includes(cat.id) ? 'border-primary/60 ring-1 ring-primary/60 bg-primary/[0.03]' : ''"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.category_ids.includes(cat.id)"
                                    @change="toggleCategory(cat.id)"
                                    class="rounded-sm border-input text-primary focus:ring-primary h-3.5 w-3.5"
                                />
                                <span class="text-xs font-medium text-foreground">{{ cat.name }}</span>
                            </label>
                        </div>
                        <div v-else class="text-xs text-muted-foreground p-3 border border-dashed border-sidebar-border rounded-lg text-center bg-card">
                            Belum ada kategori yang dibuat. Silakan tambahkan kategori terlebih dahulu di halaman manajemen kategori.
                        </div>
                        <InputError :message="form.errors.category_ids" />
                    </div>
                </div>

                <!-- Tab 3: Status & Jadwal -->
                <div v-show="activeTab === 'publishing'" class="space-y-6 max-w-md">
                    <!-- Status selector -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Status Penerbitan</Label>
                        <div class="grid grid-cols-3 gap-2">
                            <label
                                class="flex flex-col items-center justify-center p-4 border rounded-xl cursor-pointer text-center select-none bg-card transition-all"
                                :class="form.status === 'draft' ? 'border-primary ring-1 ring-primary bg-primary/[0.02]' : 'border-sidebar-border hover:bg-neutral-50 dark:hover:bg-neutral-900'"
                            >
                                <input
                                    type="radio"
                                    value="draft"
                                    v-model="form.status"
                                    class="sr-only"
                                />
                                <span class="text-sm font-bold text-foreground mb-0.5">Draft</span>
                                <span class="text-[10px] text-muted-foreground">Simpan internal</span>
                            </label>

                            <label
                                class="flex flex-col items-center justify-center p-4 border rounded-xl cursor-pointer text-center select-none bg-card transition-all"
                                :class="form.status === 'published' ? 'border-primary ring-1 ring-primary bg-primary/[0.02]' : 'border-sidebar-border hover:bg-neutral-50 dark:hover:bg-neutral-900'"
                            >
                                <input
                                    type="radio"
                                    value="published"
                                    v-model="form.status"
                                    class="sr-only"
                                />
                                <span class="text-sm font-bold text-foreground mb-0.5">Publish</span>
                                <span class="text-[10px] text-muted-foreground">Langsung tayang</span>
                            </label>

                            <label
                                class="flex flex-col items-center justify-center p-4 border rounded-xl cursor-pointer text-center select-none bg-card transition-all"
                                :class="form.status === 'scheduled' ? 'border-primary ring-1 ring-primary bg-primary/[0.02]' : 'border-sidebar-border hover:bg-neutral-50 dark:hover:bg-neutral-900'"
                            >
                                <input
                                    type="radio"
                                    value="scheduled"
                                    v-model="form.status"
                                    class="sr-only"
                                />
                                <span class="text-sm font-bold text-foreground mb-0.5">Scheduled</span>
                                <span class="text-[10px] text-muted-foreground">Jadwal tanggal</span>
                            </label>
                        </div>
                        <InputError :message="form.errors.status" />
                    </div>

                    <!-- Scheduled DateTime Picker -->
                    <div v-if="form.status === 'scheduled'" class="grid gap-2 border border-sidebar-border bg-neutral-50/30 dark:bg-neutral-900/10 rounded-xl p-4 transition-all">
                        <Label for="post-scheduled-at" class="text-xs font-bold text-foreground flex items-center gap-1.5">
                            <Calendar class="h-3.5 w-3.5 text-primary" />
                            Tanggal & Waktu Tayang <span class="text-destructive">*</span>
                        </Label>
                        <input
                            id="post-scheduled-at"
                            type="datetime-local"
                            v-model="form.scheduled_at"
                            class="flex h-9 w-full rounded-md border border-sidebar-border bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            required
                        />
                        <p class="text-[10px] text-muted-foreground">
                            Artikel akan otomatis beralih status ke "published" setelah waktu penjadwalan terlewati melalui cron scheduler backend.
                        </p>
                        <InputError :message="form.errors.scheduled_at" />
                    </div>
                </div>

                <!-- Tab 4: Artikel Terkait (Manual Related Posts) -->
                <div v-show="activeTab === 'related'" class="space-y-6">
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Pilih Rekomendasi Artikel Terkait (Manual Override)</Label>
                        <p class="text-xs text-muted-foreground">
                            Tentukan manual artikel yang akan direkomendasikan di bawah tulisan ini. Jika kosong, sistem otomatis mencari 3 artikel sekategori.
                        </p>
                        
                        <!-- Search Box for Related -->
                        <div class="relative max-w-md mt-2">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="relatedSearch"
                                placeholder="Cari artikel untuk ditautkan..."
                                class="pl-9 bg-transparent border-sidebar-border w-full"
                            />
                            
                            <!-- Search Autocomplete Dropdown -->
                            <div v-if="relatedSearch && filteredPostsForRelated.length > 0" class="absolute left-0 right-0 top-full mt-1 border border-sidebar-border rounded-lg bg-card shadow-lg max-h-48 overflow-y-auto z-20 divide-y divide-sidebar-border/40">
                                <button
                                    v-for="post in filteredPostsForRelated"
                                    :key="post.id"
                                    type="button"
                                    @click="selectRelatedPost(post)"
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-neutral-50 dark:hover:bg-neutral-900 transition-colors font-medium flex items-center justify-between"
                                >
                                    <span>{{ post.title }}</span>
                                    <Plus class="h-3 w-3 text-muted-foreground" />
                                </button>
                            </div>
                            <div v-else-if="relatedSearch" class="absolute left-0 right-0 top-full mt-1 border border-sidebar-border rounded-lg bg-card shadow-lg p-3 text-center text-xs text-muted-foreground z-20">
                                Tidak ada artikel lain ditemukan yang sesuai
                            </div>
                        </div>
                    </div>

                    <!-- Selected Related Posts List -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Artikel Terkait Terpilih</Label>
                        <div v-if="form.related_post_ids.length > 0" class="divide-y divide-sidebar-border border border-sidebar-border rounded-xl bg-card overflow-hidden max-w-xl">
                            <div
                                v-for="postId in form.related_post_ids"
                                :key="postId"
                                class="flex items-center justify-between p-3 hover:bg-neutral-50/40 dark:hover:bg-neutral-900/10 transition-colors"
                            >
                                <div class="flex items-center gap-2 text-xs font-semibold text-foreground">
                                    <BookOpen class="h-3.5 w-3.5 text-primary shrink-0" />
                                    <span>{{ getPostTitleById(postId) }}</span>
                                </div>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    @click="removeRelatedPost(postId)"
                                    class="h-7 w-7 text-muted-foreground hover:text-destructive cursor-pointer hover:bg-destructive/10 rounded-lg"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                </Button>
                            </div>
                        </div>
                        <div v-else class="text-xs text-muted-foreground p-4 border border-dashed border-sidebar-border rounded-xl text-center max-w-xl bg-card">
                            Belum ada artikel terkait yang dipilih manual. Rekomendasi otomatis akan dijalankan.
                        </div>
                        <InputError :message="form.errors.related_post_ids" />
                    </div>
                </div>

                <!-- Tab 5: Pengaturan SEO -->
                <div v-show="activeTab === 'seo'" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Inputs -->
                        <div class="space-y-5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground pb-2 border-b border-sidebar-border/40">Custom Meta SEO Overrides</h4>

                            <!-- Meta Title -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="post-meta-title" class="text-xs font-bold text-foreground">Meta Title</Label>
                                    <span class="text-[10px] text-muted-foreground" :class="{'text-destructive': form.meta_title.length > 60}">
                                        {{ form.meta_title.length }}/60
                                    </span>
                                </div>
                                <Input
                                    id="post-meta-title"
                                    v-model="form.meta_title"
                                    placeholder="Masukkan meta title kustom..."
                                    class="bg-transparent border-sidebar-border"
                                    maxlength="60"
                                />
                                <p class="text-[9px] text-muted-foreground">Panjang maksimal yang disarankan Google adalah 60 karakter agar tidak terpotong di hasil pencarian.</p>
                                <InputError :message="form.errors.meta_title" />
                            </div>

                            <!-- Meta Description -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="post-meta-desc" class="text-xs font-bold text-foreground">Meta Description</Label>
                                    <span class="text-[10px] text-muted-foreground" :class="{'text-destructive': form.meta_description.length > 160}">
                                        {{ form.meta_description.length }}/160
                                    </span>
                                </div>
                                <textarea
                                    id="post-meta-desc"
                                    v-model="form.meta_description"
                                    rows="4"
                                    placeholder="Masukkan meta description kustom..."
                                    class="flex w-full rounded-md border border-sidebar-border bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring"
                                    maxlength="160"
                                ></textarea>
                                <p class="text-[9px] text-muted-foreground">Panjang maksimal yang disarankan Google adalah 160 karakter agar penjelasan deskripsi terlihat penuh.</p>
                                <InputError :message="form.errors.meta_description" />
                            </div>
                        </div>

                        <!-- Google Preview Simulator -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground pb-2 border-b border-sidebar-border/40 flex items-center gap-1.5">
                                <Sparkles class="h-3.5 w-3.5 text-primary" />
                                Google Search Snippet Simulator
                            </h4>

                            <!-- Snippet Card (Desktop Google View) -->
                            <div class="border border-sidebar-border/70 rounded-xl p-4 bg-white dark:bg-[#202124] shadow-xs text-left">
                                <!-- Heading URL -->
                                <div class="flex items-center gap-1 text-xs text-[#202124] dark:text-[#bdc1c6] font-sans">
                                    <span class="font-normal text-xs">growthcoder.id</span>
                                    <span class="text-[9px]">›</span>
                                    <span class="text-muted-foreground text-xs truncate max-w-[200px]">blog › {{ form.slug || 'slug-artikel' }}</span>
                                </div>

                                <!-- Google Blue Title Link -->
                                <h3 class="text-lg font-medium text-[#1a0dab] dark:text-[#8ab4f8] hover:underline font-sans cursor-pointer leading-tight mt-1 line-clamp-2">
                                    {{ seoTitlePreview }}
                                </h3>

                                <!-- Google Snippet Description text -->
                                <p class="text-xs text-[#4d5156] dark:text-[#bdc1c6] font-sans mt-1.5 leading-relaxed line-clamp-3">
                                    <span class="text-[#70757a] dark:text-[#9aa0a6] text-xs font-normal">
                                        {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }} — 
                                    </span>
                                    {{ seoDescPreview }}
                                </p>
                            </div>
                            <p class="text-[10px] text-muted-foreground">
                                * Simulai di atas memperkirakan tampilan pencarian desktop Google. Hasil sebenarnya dapat bervariasi bergantung pada algoritma indexing mesin pencari.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Save buttons -->
            <div class="flex items-center justify-end gap-3 border-t border-sidebar-border/70 pt-5">
                <Link :href="postsIndex()">
                    <Button type="button" variant="outline" class="cursor-pointer font-medium rounded-lg">
                        Batal
                    </Button>
                </Link>
                <Button type="submit" :disabled="form.processing" class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer rounded-lg px-6">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Artikel' }}
                </Button>
            </div>
        </form>

        <!-- Media Library Modal for Cover Image selection -->
        <MediaLibraryModal
            :open="coverMediaOpen"
            @update:open="coverMediaOpen = $event"
            @select="selectCoverImage"
        />
    </div>
</template>
