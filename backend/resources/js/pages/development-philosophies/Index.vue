<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
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
import {
    Plus,
    Search,
    Edit2,
    Trash2,
    GripVertical,
    AlertTriangle,
    Award,
    ToggleLeft,
    ToggleRight,
    X,
    Loader2,
    Eye,
} from '@lucide/vue';
import * as LucideIcons from '@lucide/vue';
import { index as developmentPhilosophiesIndex } from '@/routes/development-philosophies';
import { getLucideSvgString, isSvgString } from '@/utils/icon';

/**
 * Resolve a Lucide icon component by its string name.
 * Returns null if the name is not found or is an SVG.
 */
const getIcon = (name: string | null): any => {
    if (!name || isSvgString(name)) return null;
    return (LucideIcons as Record<string, any>)[name] ?? null;
};

// -----------------------------------------------------------------------
// Types
// -----------------------------------------------------------------------
interface DevelopmentPhilosophy {
    id: number;
    title: string;
    slug: string;
    description: string;
    icon: string | null;
    is_active: boolean;
    order: number;
    created_at: string;
    updated_at: string;
}

// -----------------------------------------------------------------------
// Props & Page Layout
// -----------------------------------------------------------------------
const props = defineProps<{
    philosophies: DevelopmentPhilosophy[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Development Philosophy Management',
                href: developmentPhilosophiesIndex(),
            },
        ],
    },
});

// -----------------------------------------------------------------------
// Lucide Icon Picker
// -----------------------------------------------------------------------
// A curated set of commonly used Lucide icons for philosophy items
const lucideIconNames = [
    'Award', 'Shield', 'ShieldCheck', 'Sparkles', 'Star', 'Heart',
    'Code', 'Code2', 'Terminal', 'Globe', 'Globe2',
    'Zap', 'Rocket', 'Bot', 'BrainCircuit', 'Cpu',
    'Server', 'Database', 'Cloud', 'Layers', 'Layers2',
    'Wrench', 'Settings', 'Settings2', 'Sliders',
    'Gauge', 'Activity', 'BarChart', 'LineChart',
    'Smartphone', 'Monitor', 'Laptop',
    'Lock', 'Key', 'Fingerprint', 'Users', 'UserCheck',
    'Mail', 'MessageSquare', 'Bell', 'Send', 'FileCode', 'FileText',
    'Search', 'Filter', 'Link', 'Link2', 'Plug', 'Plug2', 'Boxes',
    'Pen', 'PenTool', 'Palette', 'Image', 'Map',
];

const iconSearchQuery = ref('');
const iconPickerOpen = ref(false);
const activeIconTab = ref<'picker' | 'custom'>('picker');

const filteredIcons = computed(() => {
    if (!iconSearchQuery.value) {
        return lucideIconNames;
    }
    const q = iconSearchQuery.value.toLowerCase();
    return lucideIconNames.filter(name => name.toLowerCase().includes(q));
});

// -----------------------------------------------------------------------
// Philosophies List (local copy for drag-and-drop)
// -----------------------------------------------------------------------
const philosophiesList = ref<DevelopmentPhilosophy[]>([...props.philosophies]);

watch(() => props.philosophies, (newVal) => {
    philosophiesList.value = [...newVal];
});

// -----------------------------------------------------------------------
// Search
// -----------------------------------------------------------------------
const searchQuery = ref('');

const filteredPhilosophies = computed(() => {
    if (!searchQuery.value) {
        return philosophiesList.value;
    }
    const q = searchQuery.value.toLowerCase();
    return philosophiesList.value.filter(p =>
        p.title.toLowerCase().includes(q) ||
        p.description.toLowerCase().includes(q)
    );
});

// -----------------------------------------------------------------------
// Stats
// -----------------------------------------------------------------------
const activeCount = computed(() => philosophiesList.value.filter(p => p.is_active).length);

// -----------------------------------------------------------------------
// Drag & Drop Reorder
// -----------------------------------------------------------------------
const draggedIndex = ref<number | null>(null);
const dragOverIndex = ref<number | null>(null);
const isDragging = ref(false);

const onDragStart = (index: number, e: DragEvent) => {
    if (searchQuery.value) return;
    draggedIndex.value = index;
    isDragging.value = true;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', String(index));
    }
};

const onDragOver = (index: number, e: DragEvent) => {
    e.preventDefault();
    if (searchQuery.value || draggedIndex.value === null) return;
    dragOverIndex.value = index;
};

const onDragEnd = () => {
    if (searchQuery.value) return;
    if (
        draggedIndex.value !== null &&
        dragOverIndex.value !== null &&
        draggedIndex.value !== dragOverIndex.value
    ) {
        const items = [...philosophiesList.value];
        const dragged = items[draggedIndex.value];
        items.splice(draggedIndex.value, 1);
        items.splice(dragOverIndex.value, 0, dragged);
        philosophiesList.value = items;

        router.post('/admin-cms/development-philosophies/reorder', {
            ids: items.map(p => p.id),
        }, { preserveScroll: true });
    }
    draggedIndex.value = null;
    dragOverIndex.value = null;
    isDragging.value = false;
};

const onDragLeave = () => {
    dragOverIndex.value = null;
};

// -----------------------------------------------------------------------
// Toggle Active (inline)
// -----------------------------------------------------------------------
const togglingId = ref<number | null>(null);

const toggleActive = (philosophy: DevelopmentPhilosophy) => {
    togglingId.value = philosophy.id;
    router.post(`/admin-cms/development-philosophies/${philosophy.id}/toggle-active`, {}, {
        preserveScroll: true,
        onFinish: () => {
            togglingId.value = null;
        },
    });
};

// -----------------------------------------------------------------------
// Sheet (Create / Edit)
// -----------------------------------------------------------------------
const sheetOpen = ref(false);
const isEditing = ref(false);
const editingPhilosophyId = ref<number | null>(null);

const form = useForm({
    title: '',
    slug: '',
    description: '',
    icon: null as string | null,
    is_active: true,
    order: 0,
});

// Auto-generate slug from title (only when creating)
const isSlugManuallyEdited = ref(false);

watch(() => form.title, (newTitle) => {
    if (!isEditing.value && !isSlugManuallyEdited.value) {
        form.slug = newTitle
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
});

const onSlugInput = () => {
    isSlugManuallyEdited.value = true;
};

const openCreate = () => {
    isEditing.value = false;
    editingPhilosophyId.value = null;
    isSlugManuallyEdited.value = false;
    form.reset();
    form.clearErrors();
    form.order = philosophiesList.value.length > 0
        ? Math.max(...philosophiesList.value.map(p => p.order)) + 1
        : 0;
    form.is_active = true;
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = 'picker';
    sheetOpen.value = true;
};

const openEdit = (philosophy: DevelopmentPhilosophy) => {
    isEditing.value = true;
    editingPhilosophyId.value = philosophy.id;
    isSlugManuallyEdited.value = true;
    form.title = philosophy.title;
    form.slug = philosophy.slug;
    form.description = philosophy.description;
    form.icon = philosophy.icon;
    form.is_active = philosophy.is_active;
    form.order = philosophy.order;
    form.clearErrors();
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = isSvgString(philosophy.icon) ? 'custom' : 'picker';
    sheetOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingPhilosophyId.value) {
        form.put(`/admin-cms/development-philosophies/${editingPhilosophyId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin-cms/development-philosophies', {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            },
        });
    }
};

const selectIcon = (iconName: string) => {
    form.icon = getLucideSvgString(iconName);
    iconPickerOpen.value = false;
    iconSearchQuery.value = '';
};

const clearIcon = () => {
    form.icon = null;
};

// Description character count
const descLength = computed(() => form.description.length);
const isDescOverLimit = computed(() => descLength.value > 300);

// -----------------------------------------------------------------------
// Delete Dialog
// -----------------------------------------------------------------------
const deleteDialogOpen = ref(false);
const deletingPhilosophy = ref<DevelopmentPhilosophy | null>(null);
const deleteForm = useForm({});

const openDelete = (philosophy: DevelopmentPhilosophy) => {
    deletingPhilosophy.value = philosophy;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deletingPhilosophy.value) return;
    deleteForm.delete(`/admin-cms/development-philosophies/${deletingPhilosophy.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            deletingPhilosophy.value = null;
        },
    });
};
</script>

<template>
    <Head title="Development Philosophy Management" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Development Philosophy Management
            </h1>
            <p class="text-sm text-muted-foreground">
                Kelola prinsip dan filosofi pengembangan software Anda.
            </p>
        </div>

        <!-- ================================================================
             TOOLBAR
        ================================================================ -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search -->
            <div class="relative max-w-sm flex-1">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    id="philosophies-search"
                    v-model="searchQuery"
                    placeholder="Cari filosofi..."
                    class="pl-9"
                />
            </div>

            <!-- Right side: Stats + Add button -->
            <div class="flex items-center gap-3">
                <!-- Stats badges -->
                <div class="flex items-center gap-2">
                    <Badge variant="secondary" class="gap-1.5">
                        <Eye class="h-3 w-3" />
                        {{ activeCount }} Aktif
                    </Badge>
                    <Badge variant="outline" class="gap-1.5 text-muted-foreground">
                        {{ philosophiesList.length }} Total
                    </Badge>
                </div>

                <!-- Add button -->
                <Button id="btn-add-philosophy" @click="openCreate" class="gap-2 shrink-0">
                    <Plus class="h-4 w-4" />
                    Tambah Filosofi
                </Button>
            </div>
        </div>

        <!-- ================================================================
             PHILOSOPHIES TABLE
        ================================================================ -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden shadow-sm">
            <!-- Table header -->
            <div class="grid grid-cols-[auto_1fr_120px_100px_80px] items-center gap-4 border-b border-sidebar-border/50 bg-muted/30 px-4 py-3">
                <div class="w-8"></div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Filosofi</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center">Status</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center">Urutan</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
            </div>

            <!-- Empty state -->
            <div
                v-if="filteredPhilosophies.length === 0"
                class="flex flex-col items-center justify-center gap-4 py-16 text-center"
            >
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-muted/50">
                    <Award class="h-7 w-7 text-muted-foreground/60" />
                </div>
                <div>
                    <p class="font-semibold text-foreground">
                        {{ searchQuery ? 'Tidak ada hasil' : 'Belum ada filosofi' }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ searchQuery
                            ? 'Coba kata kunci berbeda atau hapus filter.'
                            : 'Klik "Tambah Filosofi" untuk menambahkan filosofi pertama Anda.' }}
                    </p>
                </div>
                <Button v-if="!searchQuery" @click="openCreate" variant="outline" size="sm" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Tambah Filosofi
                </Button>
            </div>

            <!-- Rows -->
            <div
                v-for="(philosophy, index) in filteredPhilosophies"
                :key="philosophy.id"
                :draggable="!searchQuery"
                @dragstart="onDragStart(index, $event)"
                @dragover="onDragOver(index, $event)"
                @dragend="onDragEnd"
                @dragleave="onDragLeave"
                class="grid grid-cols-[auto_1fr_120px_100px_80px] items-center gap-4 border-b border-sidebar-border/30 px-4 py-4 transition-all duration-150 last:border-b-0"
                :class="{
                    'bg-primary/5 scale-[1.01] shadow-md': dragOverIndex === index && draggedIndex !== index,
                    'opacity-40': draggedIndex === index,
                    'hover:bg-muted/30': dragOverIndex !== index || draggedIndex === index,
                }"
            >
                <!-- Drag Handle -->
                <div 
                    class="flex h-8 w-8 cursor-grab items-center justify-center rounded-lg text-muted-foreground/40 hover:bg-muted hover:text-muted-foreground active:cursor-grabbing"
                    :class="{ 'pointer-events-none opacity-20': searchQuery }"
                >
                    <GripVertical class="h-4 w-4" />
                </div>

                <!-- Philosophy Details -->
                <div class="flex items-start gap-4 min-w-0">
                    <!-- Icon Display -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary border border-primary/10">
                        <div v-if="isSvgString(philosophy.icon)" v-html="philosophy.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-current" />
                        <component v-else :is="getIcon(philosophy.icon) || Award" class="h-5 w-5" />
                    </div>

                    <!-- Meta -->
                    <div class="flex flex-col gap-0.5 min-w-0">
                        <span class="font-semibold text-foreground text-sm truncate">
                            {{ philosophy.title }}
                        </span>
                        <span class="text-xs text-muted-foreground truncate max-w-xl">
                            {{ philosophy.description }}
                        </span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex justify-center">
                    <button
                        @click="toggleActive(philosophy)"
                        :disabled="togglingId === philosophy.id"
                        class="inline-flex rounded-full transition-opacity disabled:opacity-50"
                        title="Klik untuk mengubah status"
                    >
                        <Badge 
                            :variant="philosophy.is_active ? 'default' : 'secondary'"
                            class="gap-1 px-2.5 py-0.5 text-[10px] font-medium"
                        >
                            <Loader2 v-if="togglingId === philosophy.id" class="h-2.5 w-2.5 animate-spin" />
                            <template v-else>
                                <span class="h-1.5 w-1.5 rounded-full" :class="philosophy.is_active ? 'bg-emerald-500' : 'bg-zinc-400'"></span>
                                {{ philosophy.is_active ? 'Aktif' : 'Draft' }}
                            </template>
                        </Badge>
                    </button>
                </div>

                <!-- Order Indicator -->
                <div class="text-center text-xs font-medium text-muted-foreground">
                    {{ philosophy.order }}
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                        @click="openEdit(philosophy)"
                    >
                        <Edit2 class="h-4 w-4" />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="openDelete(philosophy)"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- ================================================================
             CREATE / EDIT SHEET
        ================================================================ -->
        <Sheet v-model:open="sheetOpen">
            <SheetContent class="w-full sm:max-w-lg overflow-y-auto">
                <SheetHeader class="border-b border-sidebar-border/50 pb-4 mb-6">
                    <SheetTitle>
                        {{ isEditing ? 'Edit Filosofi' : 'Tambah Filosofi Baru' }}
                    </SheetTitle>
                    <SheetDescription>
                        {{ isEditing 
                            ? 'Ubah informasi filosofi pengembangan yang sudah ada di sini. Klik Simpan setelah selesai.' 
                            : 'Isi formulir berikut untuk menambahkan prinsip filosofi baru.' }}
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Title -->
                    <div class="space-y-2">
                        <Label for="title">Judul Filosofi</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Contoh: Clean & Maintainable Code"
                            required
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
                    <div class="space-y-2">
                        <Label for="slug">Slug URL</Label>
                        <Input
                            id="slug"
                            v-model="form.slug"
                            @input="onSlugInput"
                            placeholder="auto-generated-slug"
                            required
                        />
                        <InputError :message="form.errors.slug" />
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <Label for="description">Deskripsi Filosofi</Label>
                            <span 
                                class="text-[10px] font-semibold"
                                :class="isDescOverLimit ? 'text-destructive' : 'text-muted-foreground'"
                            >
                                {{ descLength }}/300
                            </span>
                        </div>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Tuliskan penjelasan filosofi pengembangan Anda (maks 300 karakter)..."
                            required
                        ></textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <!-- Icon Picker -->
                    <div class="space-y-2">
                        <Label>Pilih Ikon Pendukung</Label>
                        
                        <div class="flex border-b border-border mb-2">
                            <button 
                                type="button" 
                                @click="activeIconTab = 'picker'" 
                                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors"
                                :class="activeIconTab === 'picker' ? 'border-primary text-primary font-semibold' : 'border-transparent text-muted-foreground'"
                            >
                                Pilih dari Lucide
                            </button>
                            <button 
                                type="button" 
                                @click="activeIconTab = 'custom'" 
                                class="px-4 py-2 text-xs font-medium border-b-2 transition-colors"
                                :class="activeIconTab === 'custom' ? 'border-primary text-primary font-semibold' : 'border-transparent text-muted-foreground'"
                            >
                                Custom SVG
                            </button>
                        </div>

                        <div v-if="activeIconTab === 'picker'" class="flex items-center gap-3">
                            <!-- Current Icon View -->
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-muted border text-foreground">
                                <div v-if="isSvgString(form.icon)" v-html="form.icon" class="h-6 w-6 flex items-center justify-center [&_svg]:h-6 [&_svg]:w-6 [&_svg]:text-current" />
                                <component v-else :is="getIcon(form.icon) || Award" class="h-6 w-6" />
                            </div>

                            <!-- Controls -->
                            <div class="flex items-center gap-2">
                                <Dialog v-model:open="iconPickerOpen">
                                    <Button type="button" variant="outline" size="sm" @click="iconPickerOpen = true">
                                        Pilih Ikon...
                                    </Button>
                                    <DialogContent class="sm:max-w-md">
                                        <DialogHeader>
                                            <DialogTitle>Pilih Ikon Lucide</DialogTitle>
                                            <DialogDescription>
                                                Cari dan pilih ikon yang paling relevan dengan filosofi ini.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <!-- Search input inside picker -->
                                        <div class="relative my-2">
                                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                v-model="iconSearchQuery"
                                                placeholder="Cari ikon..."
                                                class="pl-9"
                                                @keydown.enter.prevent
                                            />
                                        </div>

                                        <!-- Grid of Icons -->
                                        <div class="grid grid-cols-6 gap-2 max-h-[300px] overflow-y-auto p-1 border rounded-lg bg-muted/20">
                                            <button
                                                v-for="name in filteredIcons"
                                                :key="name"
                                                type="button"
                                                @click="selectIcon(name)"
                                                class="flex flex-col items-center justify-center p-2 rounded-lg hover:bg-primary/10 hover:text-primary transition-colors text-muted-foreground"
                                                :title="name"
                                            >
                                                <component :is="getIcon(name)" class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </DialogContent>
                                </Dialog>

                                <Button 
                                    v-if="form.icon" 
                                    type="button" 
                                    variant="ghost" 
                                    size="sm" 
                                    class="text-destructive hover:bg-destructive/10"
                                    @click="clearIcon"
                                >
                                    <X class="h-4 w-4 mr-1" /> Hapus
                                </Button>
                            </div>
                        </div>
                        
                        <div v-else class="space-y-2">
                            <textarea
                                v-model="form.icon"
                                rows="4"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-xs font-mono ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Tempel kode markup SVG Anda di sini (misal: <svg>...</svg>)"
                            ></textarea>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-muted-foreground">Pastikan kode SVG memiliki stroke='currentColor' atau fill='currentColor'.</span>
                                <Button 
                                    v-if="form.icon" 
                                    type="button" 
                                    variant="ghost" 
                                    size="sm" 
                                    class="text-destructive hover:bg-destructive/10 h-7 text-[10px]"
                                    @click="clearIcon"
                                >
                                    Hapus
                                </Button>
                            </div>
                        </div>
                        <InputError :message="form.errors.icon" />
                    </div>

                    <!-- Status (is_active) -->
                    <div class="flex items-center justify-between rounded-lg border p-4 bg-muted/20">
                        <div class="space-y-0.5">
                            <Label for="is_active" class="text-sm font-semibold">Tampilkan ke Publik</Label>
                            <p class="text-xs text-muted-foreground">
                                Aktifkan agar filosofi ini langsung tayang di halaman portfolio Anda.
                            </p>
                        </div>
                        <button
                            id="is_active"
                            type="button"
                            @click="form.is_active = !form.is_active"
                            class="text-primary transition-colors focus:outline-none"
                        >
                            <ToggleRight v-if="form.is_active" class="h-9 w-9 text-emerald-500" />
                            <ToggleLeft v-else class="h-9 w-9 text-zinc-400" />
                        </button>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex justify-end gap-3 border-t border-sidebar-border/50 pt-4">
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="sheetOpen = false"
                            :disabled="form.processing"
                        >
                            Batal
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing || isDescOverLimit"
                            class="gap-1.5"
                        >
                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Simpan Perubahan
                        </Button>
                    </div>
                </form>
            </SheetContent>
        </Sheet>

        <!-- ================================================================
             DELETE DIALOG
        ================================================================ -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader class="gap-2">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                        <AlertTriangle class="h-6 w-6" />
                    </div>
                    <DialogTitle>Hapus Filosofi?</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus filosofi <strong>{{ deletingPhilosophy?.title }}</strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2 sm:gap-0 mt-4">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="deleteDialogOpen = false"
                        :disabled="deleteForm.processing"
                    >
                        Batal
                    </Button>
                    <Button 
                        type="button" 
                        variant="destructive" 
                        @click="confirmDelete"
                        :disabled="deleteForm.processing"
                        class="gap-1.5"
                    >
                        <Loader2 v-if="deleteForm.processing" class="h-4 w-4 animate-spin" />
                        Ya, Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
