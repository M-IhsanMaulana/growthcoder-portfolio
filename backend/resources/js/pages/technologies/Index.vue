<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from '@/components/ui/sheet';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import InputError from '@/components/InputError.vue';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import {
    Plus,
    Search,
    Grid,
    List,
    Code,
    Server,
    Cpu,
    Database,
    Wrench,
    ExternalLink,
    Star,
    Edit2,
    Trash2,
    AlertTriangle,
    X,
    Folder,
    HelpCircle
} from '@lucide/vue';
import { index as technologiesIndex } from '@/routes/technologies';

const props = defineProps<{
    technologies: any[];
    filters: {
        q?: string;
        category?: string;
        featured?: boolean;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tech Stack Management',
                href: technologiesIndex(),
            },
        ],
    },
});

// Category definition and mapping
const categories = [
    { value: 'frontend', label: 'Frontend', color: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-300 dark:border-blue-900/50', icon: Code },
    { value: 'backend', label: 'Backend', color: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-900/50', icon: Server },
    { value: 'devops', label: 'DevOps', color: 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/30 dark:text-purple-300 dark:border-purple-900/50', icon: Cpu },
    { value: 'database', label: 'Database', color: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-900/50', icon: Database },
    { value: 'tools', label: 'Tools', color: 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/50', icon: Wrench },
];

const getCategoryLabel = (catValue: string) => {
    return categories.find(c => c.value === catValue)?.label || catValue;
};

const getCategoryColor = (catValue: string) => {
    return categories.find(c => c.value === catValue)?.color || 'bg-neutral-100 text-neutral-800 border-neutral-200';
};

const getCategoryIcon = (catValue: string) => {
    return categories.find(c => c.value === catValue)?.icon || HelpCircle;
};

// UI Local State
const viewMode = ref<'grid' | 'table'>('grid');
const listSearch = ref(props.filters.q || '');
const selectedCategoryFilter = ref<string>(props.filters.category || 'all');
const featuredFilter = ref<boolean>(props.filters.featured || false);

// Sheet Form State
const sheetOpen = ref(false);
const isEditing = ref(false);
const editingTechnologyId = ref<number | null>(null);
const isSlugManuallyEdited = ref(false);
const mediaModalOpen = ref(false);
const selectedLogo = ref<any>(null);

const form = useForm({
    name: '',
    slug: '',
    category: 'frontend',
    logo_media_id: null as number | null,
    description: '',
    url: '',
    is_featured: false,
});

// Filters Computed
const filteredTechnologies = computed(() => {
    let list = props.technologies;

    // Local filter just in case, but usually synced from controller
    if (listSearch.value) {
        const q = listSearch.value.toLowerCase();
        list = list.filter(t => t.name.toLowerCase().includes(q) || (t.description && t.description.toLowerCase().includes(q)));
    }

    if (selectedCategoryFilter.value !== 'all') {
        list = list.filter(t => t.category === selectedCategoryFilter.value);
    }

    if (featuredFilter.value) {
        list = list.filter(t => t.is_featured);
    }

    return list;
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

watch(() => form.name, (newName) => {
    if (!isEditing.value && !isSlugManuallyEdited.value) {
        form.slug = slugify(newName);
    }
});

// Open Form actions
const openCreate = () => {
    isEditing.value = false;
    editingTechnologyId.value = null;
    selectedLogo.value = null;
    form.reset();
    form.clearErrors();
    isSlugManuallyEdited.value = false;
    sheetOpen.value = true;
};

const openEdit = (technology: any) => {
    isEditing.value = true;
    editingTechnologyId.value = technology.id;
    form.name = technology.name;
    form.slug = technology.slug;
    form.category = technology.category;
    form.logo_media_id = technology.logo_media_id;
    form.description = technology.description || '';
    form.url = technology.url || '';
    form.is_featured = technology.is_featured;
    selectedLogo.value = technology.logo;
    form.clearErrors();
    isSlugManuallyEdited.value = true;
    sheetOpen.value = true;
};

// Form Submission
const submit = () => {
    if (isEditing.value && editingTechnologyId.value) {
        form.put(`/admin-cms/technologies/${editingTechnologyId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/admin-cms/technologies', {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    }
};

// Handle Select Logo from Media Library
const handleLogoSelect = (media: any) => {
    form.logo_media_id = media.id;
    selectedLogo.value = media;
};

const removeLogo = () => {
    form.logo_media_id = null;
    selectedLogo.value = null;
};

// Delete Confirmation State
const deleteDialogOpen = ref(false);
const techToDelete = ref<any | null>(null);

const confirmDelete = (tech: any) => {
    techToDelete.value = tech;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!techToDelete.value) return;
    router.delete(`/admin-cms/technologies/${techToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            techToDelete.value = null;
        }
    });
};

const showSlugWarning = computed(() => {
    if (!isEditing.value || !editingTechnologyId.value) return false;
    const original = props.technologies.find(t => t.id === editingTechnologyId.value);
    return original && form.slug !== original.slug;
});

// Toggle Featured Quick Action
const toggleFeatured = (tech: any) => {
    router.put(`/admin-cms/technologies/${tech.id}`, {
        name: tech.name,
        slug: tech.slug,
        category: tech.category,
        logo_media_id: tech.logo_media_id,
        description: tech.description || '',
        url: tech.url || '',
        is_featured: !tech.is_featured
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Manajemen Tech Stack" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                    Manajemen Tech Stack
                </h1>
                <p class="text-sm text-muted-foreground">
                    Kelola data master teknologi, framework, dan pustaka kode yang digunakan dalam proyek portofolio Anda.
                </p>
            </div>
            <div>
                <Button @click="openCreate" class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Teknologi
                </Button>
            </div>
        </div>

        <!-- Controls (Search, Categories, Featured, Layout Toggle) -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-card p-4 rounded-xl border border-border/60 shadow-xs">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center flex-1">
                <!-- Search -->
                <div class="relative w-full sm:max-w-xs">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="listSearch"
                        placeholder="Cari teknologi..."
                        class="pl-9 pr-8 bg-card border-border/80 h-9 text-xs"
                    />
                    <button
                        v-if="listSearch"
                        @click="listSearch = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>

                <!-- Category Pills Filter -->
                <div class="flex flex-wrap items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button
                        @click="selectedCategoryFilter = 'all'"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-150 cursor-pointer"
                        :class="selectedCategoryFilter === 'all'
                            ? 'bg-primary text-white border-primary shadow-xs'
                            : 'bg-card text-muted-foreground border-border/80 hover:bg-muted/40'"
                    >
                        Semua
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat.value"
                        @click="selectedCategoryFilter = cat.value"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition-all duration-150 cursor-pointer"
                        :class="selectedCategoryFilter === cat.value
                            ? 'bg-primary text-white border-primary shadow-xs'
                            : 'bg-card text-muted-foreground border-border/80 hover:bg-muted/40'"
                    >
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <!-- Featured Checkbox & Layout Mode Switcher -->
            <div class="flex items-center justify-between sm:justify-start gap-4 border-t pt-3 md:border-t-0 md:pt-0 border-border/50">
                <!-- Featured Toggle -->
                <label class="flex items-center gap-2 text-sm font-medium text-muted-foreground cursor-pointer select-none">
                    <input
                        type="checkbox"
                        v-model="featuredFilter"
                        class="rounded-sm border-input bg-card text-primary focus:ring-primary h-4 w-4 transition duration-150 cursor-pointer"
                    />
                    Featured Only
                </label>

                <!-- Divider -->
                <div class="hidden sm:block h-6 w-px bg-border/80"></div>

                <!-- Toggle Grid / Table -->
                <div class="flex items-center bg-muted/60 p-0.5 rounded-lg border border-border/40">
                    <button
                        type="button"
                        @click="viewMode = 'grid'"
                        class="p-1.5 rounded-md cursor-pointer transition-colors duration-150"
                        :class="viewMode === 'grid' ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                        title="Tampilan Grid Cards"
                    >
                        <Grid class="h-4 w-4" />
                    </button>
                    <button
                        type="button"
                        @click="viewMode = 'table'"
                        class="p-1.5 rounded-md cursor-pointer transition-colors duration-150"
                        :class="viewMode === 'table' ? 'bg-card text-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                        title="Tampilan Tabel"
                    >
                        <List class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Technologies Display -->
        <div v-if="filteredTechnologies.length > 0">
            <!-- Grid Card Mode -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div
                    v-for="tech in filteredTechnologies"
                    :key="tech.id"
                    @click="openEdit(tech)"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-border/70 bg-card p-5 shadow-2xs hover:shadow-md hover:border-primary/40 transition-all duration-200 cursor-pointer"
                >
                    <div>
                        <!-- Header inside Card: Logo & Action Float -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <!-- Logo / Placeholder -->
                            <div class="h-12 w-12 shrink-0 rounded-xl overflow-hidden border border-border/60 bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                                <img
                                    v-if="tech.logo?.urls?.thumbnail"
                                    :src="tech.logo.urls.thumbnail"
                                    :alt="tech.name"
                                    class="h-full w-full object-contain p-1"
                                />
                                <component
                                    v-else
                                    :is="getCategoryIcon(tech.category)"
                                    class="h-6 w-6 text-muted-foreground/80"
                                />
                            </div>

                            <!-- Star Featured and Actions -->
                            <div class="flex items-center gap-1" @click.stop>
                                <button
                                    type="button"
                                    @click="toggleFeatured(tech)"
                                    class="p-1.5 rounded-lg text-muted-foreground hover:text-amber-500 transition-colors cursor-pointer"
                                    :title="tech.is_featured ? 'Hapus dari Unggulan' : 'Jadikan Unggulan'"
                                >
                                    <Star class="h-4 w-4" :class="{ 'fill-amber-400 text-amber-400': tech.is_featured }" />
                                </button>
                                <button
                                    type="button"
                                    @click="openEdit(tech)"
                                    class="p-1.5 rounded-lg text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors cursor-pointer"
                                    title="Edit Teknologi"
                                >
                                    <Edit2 class="h-3.5 w-3.5" />
                                </button>
                                <button
                                    type="button"
                                    @click="confirmDelete(tech)"
                                    class="p-1.5 rounded-lg text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors cursor-pointer"
                                    title="Hapus Teknologi"
                                >
                                    <Trash2 class="h-3.5 w-3.5" />
                                </button>
                            </div>
                        </div>

                        <!-- Tech Meta Details -->
                        <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors flex items-center gap-1.5">
                            {{ tech.name }}
                        </h3>
                        <p class="text-xs text-muted-foreground font-mono mt-0.5">{{ tech.slug }}</p>

                        <!-- Category Badge -->
                        <Badge
                            variant="outline"
                            class="mt-2 text-[10px] font-bold px-2 py-0.5 border rounded-md uppercase tracking-wider"
                            :class="getCategoryColor(tech.category)"
                        >
                            {{ getCategoryLabel(tech.category) }}
                        </Badge>

                        <!-- Description -->
                        <p class="text-xs text-muted-foreground line-clamp-3 mt-3 min-h-[48px]" :title="tech.description">
                            {{ tech.description || 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    <!-- Footer Details: Relations & External Link -->
                    <div class="flex items-center justify-between border-t border-border/50 pt-3 mt-4 text-[11px] text-muted-foreground" @click.stop>
                        <div class="flex gap-2">
                            <span>Proyek: <strong class="text-foreground">{{ tech.projects_count }}</strong></span>
                            <span>•</span>
                            <span>Skill: <strong class="text-foreground">{{ tech.skills_count }}</strong></span>
                        </div>
                        <a
                            v-if="tech.url"
                            :href="tech.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-0.5 text-primary hover:underline font-medium"
                        >
                            Docs <ExternalLink class="h-3 w-3" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table Mode -->
            <div v-else class="overflow-hidden rounded-2xl border border-border/70 bg-card shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border/70 bg-muted/20 text-[11px] font-bold uppercase tracking-wider text-muted-foreground">
                                <th class="p-4 w-[80px] text-center">Logo</th>
                                <th class="p-4">Nama</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Featured</th>
                                <th class="p-4">Deskripsi</th>
                                <th class="p-4 w-[120px] text-center">Link</th>
                                <th class="p-4 w-[100px] text-center">Penggunaan</th>
                                <th class="p-4 w-[120px] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60 text-sm">
                            <tr
                                v-for="tech in filteredTechnologies"
                                :key="tech.id"
                                @click="openEdit(tech)"
                                class="hover:bg-muted/20 transition-colors duration-150 cursor-pointer"
                            >
                                <!-- Logo -->
                                <td class="p-4 text-center">
                                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-border bg-neutral-50 dark:bg-neutral-900 overflow-hidden">
                                        <img
                                            v-if="tech.logo?.urls?.thumbnail"
                                            :src="tech.logo.urls.thumbnail"
                                            :alt="tech.name"
                                            class="h-full w-full object-contain p-0.5"
                                        />
                                        <component
                                            v-else
                                            :is="getCategoryIcon(tech.category)"
                                            class="h-4.5 w-4.5 text-neutral-400"
                                        />
                                    </div>
                                </td>

                                <!-- Name & Slug -->
                                <td class="p-4 font-semibold text-foreground">
                                    <div class="font-bold">{{ tech.name }}</div>
                                    <div class="text-[11px] text-muted-foreground font-mono">{{ tech.slug }}</div>
                                </td>

                                <!-- Category Badge -->
                                <td class="p-4">
                                    <Badge
                                        variant="outline"
                                        class="text-[10px] font-bold px-2 py-0.5 border rounded-md uppercase tracking-wider"
                                        :class="getCategoryColor(tech.category)"
                                    >
                                        {{ getCategoryLabel(tech.category) }}
                                    </Badge>
                                </td>

                                <!-- Featured Status -->
                                <td class="p-4">
                                    <button
                                        type="button"
                                        @click="toggleFeatured(tech)"
                                        class="p-1 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-amber-500 cursor-pointer transition-colors"
                                    >
                                        <Star class="h-4.5 w-4.5" :class="{ 'fill-amber-400 text-amber-400': tech.is_featured }" />
                                    </button>
                                </td>

                                <!-- Description -->
                                <td class="p-4 text-muted-foreground max-w-xs truncate" :title="tech.description">
                                    {{ tech.description || '-' }}
                                </td>

                                <!-- Docs Link -->
                                <td class="p-4 text-center">
                                    <a
                                        v-if="tech.url"
                                        :href="tech.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1 text-primary hover:underline text-xs font-semibold"
                                    >
                                        Docs <ExternalLink class="h-3 w-3" />
                                    </a>
                                    <span v-else class="text-neutral-300">-</span>
                                </td>

                                <!-- Usages Count -->
                                <td class="p-4 text-center text-xs text-muted-foreground">
                                    <div class="font-medium text-foreground">P: {{ tech.projects_count }}</div>
                                    <div>S: {{ tech.skills_count }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-8 w-8 text-neutral-500 hover:text-primary hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                            @click="openEdit(tech)"
                                            title="Edit Teknologi"
                                        >
                                            <Edit2 class="h-4 w-4" />
                                        </Button>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-8 w-8 text-neutral-500 hover:text-destructive hover:bg-red-50 dark:hover:bg-red-950/20"
                                            @click="confirmDelete(tech)"
                                            title="Hapus Teknologi"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center p-12 text-center border border-border border-dashed rounded-2xl bg-card/50">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 mb-4">
                <Folder class="h-6 w-6" />
            </div>
            <h3 class="text-md font-semibold text-foreground">Tidak ada teknologi ditemukan</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                {{ listSearch ? 'Tidak ada teknologi yang cocok dengan pencarian Anda.' : 'Silakan tambahkan data teknologi baru untuk memulai.' }}
            </p>
            <Button v-if="!listSearch" @click="openCreate" class="mt-4 bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Teknologi
            </Button>
        </div>

        <!-- Slide-over Form Sheet -->
        <Sheet v-slot="{ close }" v-model:open="sheetOpen">
            <SheetContent class="w-full sm:max-w-md md:max-w-lg overflow-y-auto flex flex-col h-full bg-card border-border p-6">
                <SheetHeader>
                    <SheetTitle class="text-lg font-bold">
                        {{ isEditing ? 'Edit Data Teknologi' : 'Tambah Teknologi Baru' }}
                    </SheetTitle>
                    <SheetDescription>
                        {{ isEditing ? 'Perbarui detail data master teknologi Anda di bawah ini.' : 'Isi formulir untuk menambahkan teknologi baru ke repositori portofolio Anda.' }}
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submit" class="flex-1 flex flex-col justify-between gap-6 py-4">
                    <div class="space-y-5">
                        <!-- Name Field -->
                        <div class="grid gap-2">
                            <Label for="name" class="font-semibold text-sm">Nama Teknologi <span class="text-red-500">*</span></Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                required
                                placeholder="Contoh: Laravel, Vue.js, Docker"
                                class="w-full"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Slug Field -->
                        <div class="grid gap-2">
                            <Label for="slug" class="font-semibold text-sm">Slug <span class="text-red-500">*</span></Label>
                            <Input
                                id="slug"
                                v-model="form.slug"
                                required
                                @input="isSlugManuallyEdited = true"
                                placeholder="Contoh: laravel, vue-js, docker"
                                class="w-full font-mono text-xs"
                            />
                            <InputError :message="form.errors.slug" />
                            <div v-if="showSlugWarning" class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-lg text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2 mt-1">
                                <AlertTriangle class="h-4 w-4 shrink-0 mt-0.5" />
                                <div>
                                    Mengubah slug akan mengubah url rujukan publik di masa depan. Gunakan dengan hati-hati.
                                </div>
                            </div>
                        </div>

                        <!-- Category Selector (Visual Radio Cards) -->
                        <div class="grid gap-2">
                            <Label class="font-semibold text-sm flex items-center justify-between">
                                <span>Kategori (Enum) <span class="text-red-500">*</span></span>
                                <span class="text-[11px] font-normal text-muted-foreground">Pilih klasifikasi teknologi</span>
                            </Label>
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                <button
                                    v-for="cat in categories"
                                    :key="cat.value"
                                    type="button"
                                    @click="form.category = cat.value"
                                    class="flex flex-col items-center justify-center p-3 rounded-xl border bg-card hover:bg-muted/15 transition-all duration-200 gap-2 cursor-pointer relative text-center group"
                                    :class="form.category === cat.value
                                        ? 'border-primary ring-2 ring-primary/10 shadow-xs'
                                        : 'border-border/60 text-muted-foreground hover:text-foreground'"
                                >
                                    <!-- Indicator dot -->
                                    <div
                                        class="absolute top-2 right-2 h-1.5 w-1.5 rounded-full transition-colors duration-150"
                                        :class="form.category === cat.value ? 'bg-primary' : 'bg-transparent'"
                                    ></div>

                                    <div
                                        class="p-2 rounded-lg transition-colors duration-150"
                                        :class="form.category === cat.value
                                            ? 'bg-primary/15 text-primary'
                                            : 'bg-muted/50 text-muted-foreground group-hover:text-foreground'"
                                    >
                                        <component :is="cat.icon" class="h-5 w-5" />
                                    </div>
                                    <span class="text-xs font-bold">{{ cat.label }}</span>
                                </button>
                            </div>
                            <InputError :message="form.errors.category" />
                        </div>

                        <!-- Logo Picker (Centralized Media Library) -->
                        <div class="grid gap-2">
                            <Label class="font-semibold text-sm">Logo Teknologi</Label>
                            <div class="flex items-center gap-3">
                                <!-- Preview image or default category icon -->
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-border bg-neutral-50 dark:bg-neutral-900 overflow-hidden">
                                    <img
                                        v-if="selectedLogo?.urls?.thumbnail"
                                        :src="selectedLogo.urls.thumbnail"
                                        alt="Logo Preview"
                                        class="h-full w-full object-contain p-1"
                                    />
                                    <component
                                        v-else
                                        :is="getCategoryIcon(form.category)"
                                        class="h-8 w-8 text-muted-foreground/60"
                                    />
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="font-semibold text-xs cursor-pointer"
                                        @click="mediaModalOpen = true"
                                    >
                                        Pilih dari Media Library
                                    </Button>
                                    <Button
                                        v-if="form.logo_media_id"
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="text-xs text-muted-foreground hover:text-destructive hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer w-fit p-1"
                                        @click="removeLogo"
                                    >
                                        Hapus Logo
                                    </Button>
                                </div>
                            </div>
                            <InputError :message="form.errors.logo_media_id" />
                        </div>

                        <!-- Documentation URL -->
                        <div class="grid gap-2">
                            <Label for="url" class="font-semibold text-sm">URL Dokumentasi Resmi</Label>
                            <Input
                                id="url"
                                type="url"
                                v-model="form.url"
                                placeholder="Contoh: https://laravel.com"
                                class="w-full"
                            />
                            <InputError :message="form.errors.url" />
                        </div>

                        <!-- Featured Toggle -->
                        <div class="flex items-center justify-between p-3 rounded-lg border border-border bg-muted/20">
                            <div class="space-y-0.5">
                                <Label for="is_featured" class="font-semibold text-sm cursor-pointer">Jadikan Unggulan (Featured)</Label>
                                <p class="text-xs text-muted-foreground">Tampilkan teknologi ini menonjol di halaman Beranda profil portofolio.</p>
                            </div>
                            <input
                                id="is_featured"
                                type="checkbox"
                                v-model="form.is_featured"
                                class="rounded-sm border-input bg-card text-primary focus:ring-primary h-5 w-5 transition duration-150 cursor-pointer"
                            />
                        </div>
                        <InputError :message="form.errors.is_featured" />

                        <!-- Description Field -->
                        <div class="grid gap-2">
                            <Label for="description" class="font-semibold text-sm">Deskripsi Singkat</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                placeholder="Tuliskan deskripsi singkat mengenai pengalaman Anda menggunakan teknologi ini..."
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            ></textarea>
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer inside form -->
                    <SheetFooter class="flex items-center gap-2 border-t border-border pt-4 sm:space-x-0 mt-auto">
                        <Button type="button" variant="outline" class="w-full sm:w-auto" @click="sheetOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="w-full sm:w-auto bg-primary text-white font-medium">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Teknologi' }}
                        </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Media Library Modal Selector -->
        <MediaLibraryModal
            v-model:open="mediaModalOpen"
            @select="handleLogoSelect"
        />

        <!-- Delete Confirmation Dialog -->
        <Dialog v-slot="{ close }" v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="text-destructive flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5" />
                        Hapus Master Data Teknologi
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus data teknologi <strong>{{ techToDelete?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <!-- Warning usage counts if present -->
                <div
                    v-if="techToDelete && (techToDelete.projects_count > 0 || techToDelete.skills_count > 0)"
                    class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2 mt-2"
                >
                    <AlertTriangle class="h-5 w-5 shrink-0 mt-0.5" />
                    <div>
                        <strong class="font-bold text-amber-900 dark:text-amber-200 block mb-1">Perhatian Relasi Data!</strong>
                        Teknologi ini masih digunakan oleh <strong class="underline font-bold">{{ techToDelete.projects_count }} Proyek</strong> dan <strong class="underline font-bold">{{ techToDelete.skills_count }} Skill</strong>.
                        Menghapusnya akan menghilangkan semua relasi tersebut secara otomatis (Cascade).
                    </div>
                </div>

                <DialogFooter class="flex gap-2 sm:gap-0 mt-4">
                    <Button variant="outline" @click="deleteDialogOpen = false">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="executeDelete">
                        Ya, Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
