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
import CKEditor from '@/components/CKEditor.vue';
import {
    Plus,
    Search,
    Edit2,
    Trash2,
    GripVertical,
    AlertTriangle,
    Briefcase,
    ToggleLeft,
    ToggleRight,
    X,
    ChevronRight,
    Loader2,
    Eye,
    EyeOff,
} from '@lucide/vue';
import * as LucideIcons from '@lucide/vue';
import { index as servicesIndex } from '@/routes/services';
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
interface Service {
    id: number;
    title: string;
    slug: string;
    short_description: string;
    long_description: string | null;
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
    services: Service[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Services Management',
                href: servicesIndex(),
            },
        ],
    },
});

// -----------------------------------------------------------------------
// Lucide Icon Picker
// -----------------------------------------------------------------------
// A curated set of commonly used Lucide icons for service categories
const lucideIconNames = [
    'Code', 'Code2', 'Terminal', 'Globe', 'Globe2', 'Wifi',
    'Zap', 'Rocket', 'Bot', 'BrainCircuit', 'Cpu', 'Microchip',
    'Server', 'Database', 'Cloud', 'CloudUpload', 'CloudLightning',
    'Layers', 'Layers2', 'Layout', 'LayoutGrid', 'Package',
    'Wrench', 'Settings', 'Settings2', 'Sliders', 'SlidersHorizontal',
    'Gauge', 'Activity', 'BarChart', 'LineChart', 'TrendingUp',
    'Smartphone', 'Monitor', 'Tablet', 'Laptop',
    'ShoppingCart', 'Store', 'CreditCard', 'Wallet',
    'Lock', 'Shield', 'ShieldCheck', 'Key', 'Fingerprint',
    'Users', 'UserCheck', 'UserPlus', 'Handshake',
    'Mail', 'MessageSquare', 'Bell', 'Send',
    'FileCode', 'FileText', 'File', 'FolderOpen',
    'Search', 'Filter', 'Sparkles', 'Star', 'Heart',
    'Link', 'Link2', 'ExternalLink', 'Share2',
    'Plug', 'Plug2', 'Cable', 'Boxes',
    'Pen', 'PenTool', 'Palette', 'Image', 'Video',
    'Map', 'Navigation', 'Compass',
    'Truck', 'Package2', 'Archive',
    'ChartPie', 'ChartBar', 'ChartLine',
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

// Dynamic icon rendering helper
const getIconComponent = (name: string | null) => {
    if (!name) return null;
    // We'll return the name and render via dynamic component
    return name;
};

// -----------------------------------------------------------------------
// Services List (local copy for drag-and-drop)
// -----------------------------------------------------------------------
const servicesList = ref<Service[]>([...props.services]);

watch(() => props.services, (newVal) => {
    servicesList.value = [...newVal];
});

// -----------------------------------------------------------------------
// Search
// -----------------------------------------------------------------------
const searchQuery = ref('');

const filteredServices = computed(() => {
    if (!searchQuery.value) {
        return servicesList.value;
    }
    const q = searchQuery.value.toLowerCase();
    return servicesList.value.filter(s =>
        s.title.toLowerCase().includes(q) ||
        s.short_description.toLowerCase().includes(q)
    );
});

// -----------------------------------------------------------------------
// Stats
// -----------------------------------------------------------------------
const activeCount = computed(() => servicesList.value.filter(s => s.is_active).length);

// -----------------------------------------------------------------------
// Drag & Drop Reorder
// -----------------------------------------------------------------------
const draggedIndex = ref<number | null>(null);
const dragOverIndex = ref<number | null>(null);
const isDragging = ref(false);

const onDragStart = (index: number, e: DragEvent) => {
    if (searchQuery.value) return; // Disable drag when filtering
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
        const items = [...servicesList.value];
        const dragged = items[draggedIndex.value];
        items.splice(draggedIndex.value, 1);
        items.splice(dragOverIndex.value, 0, dragged);
        servicesList.value = items;

        router.post('/admin-cms/services/reorder', {
            ids: items.map(s => s.id),
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

const toggleActive = (service: Service) => {
    togglingId.value = service.id;
    router.post(`/admin-cms/services/${service.id}/toggle-active`, {}, {
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
const editingServiceId = ref<number | null>(null);

const form = useForm({
    title: '',
    slug: '',
    short_description: '',
    long_description: '',
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
    editingServiceId.value = null;
    isSlugManuallyEdited.value = false;
    form.reset();
    form.clearErrors();
    form.order = servicesList.value.length > 0
        ? Math.max(...servicesList.value.map(s => s.order)) + 1
        : 0;
    form.is_active = true;
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = 'picker';
    sheetOpen.value = true;
};

const openEdit = (service: Service) => {
    isEditing.value = true;
    editingServiceId.value = service.id;
    isSlugManuallyEdited.value = true; // Don't auto-generate when editing
    form.title = service.title;
    form.slug = service.slug;
    form.short_description = service.short_description;
    form.long_description = service.long_description || '';
    form.icon = service.icon;
    form.is_active = service.is_active;
    form.order = service.order;
    form.clearErrors();
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = isSvgString(service.icon) ? 'custom' : 'picker';
    sheetOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingServiceId.value) {
        form.put(`/admin-cms/services/${editingServiceId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin-cms/services', {
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

// Short description character count
const shortDescLength = computed(() => form.short_description.length);
const isShortDescOverLimit = computed(() => shortDescLength.value > 200);

// -----------------------------------------------------------------------
// Delete Dialog
// -----------------------------------------------------------------------
const deleteDialogOpen = ref(false);
const deletingService = ref<Service | null>(null);
const deleteForm = useForm({});

const openDelete = (service: Service) => {
    deletingService.value = service;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deletingService.value) return;
    deleteForm.delete(`/admin-cms/services/${deletingService.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            deletingService.value = null;
        },
    });
};
</script>

<template>
    <Head title="Services Management" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Services Management
            </h1>
            <p class="text-sm text-muted-foreground">
                Kelola daftar layanan profesional yang Anda tawarkan kepada calon klien.
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
                    id="services-search"
                    v-model="searchQuery"
                    placeholder="Cari layanan..."
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
                        {{ servicesList.length }} Total
                    </Badge>
                </div>

                <!-- Add button -->
                <Button id="btn-add-service" @click="openCreate" class="gap-2 shrink-0">
                    <Plus class="h-4 w-4" />
                    Tambah Layanan
                </Button>
            </div>
        </div>

        <!-- ================================================================
             SERVICES TABLE
        ================================================================ -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden shadow-sm">
            <!-- Table header -->
            <div class="grid grid-cols-[auto_1fr_120px_100px_80px] items-center gap-4 border-b border-sidebar-border/50 bg-muted/30 px-4 py-3">
                <div class="w-8"></div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Layanan</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center">Status</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center">Urutan</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
            </div>

            <!-- Empty state -->
            <div
                v-if="filteredServices.length === 0"
                class="flex flex-col items-center justify-center gap-4 py-16 text-center"
            >
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-muted/50">
                    <Briefcase class="h-7 w-7 text-muted-foreground/60" />
                </div>
                <div>
                    <p class="font-semibold text-foreground">
                        {{ searchQuery ? 'Tidak ada hasil' : 'Belum ada layanan' }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ searchQuery
                            ? 'Coba kata kunci berbeda atau hapus filter.'
                            : 'Klik "Tambah Layanan" untuk menambahkan layanan pertama Anda.' }}
                    </p>
                </div>
                <Button v-if="!searchQuery" @click="openCreate" variant="outline" size="sm" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Tambah Layanan
                </Button>
            </div>

            <!-- Rows -->
            <div
                v-for="(service, index) in filteredServices"
                :key="service.id"
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
                    class="flex w-8 items-center justify-center transition-colors"
                    :class="searchQuery ? 'cursor-not-allowed opacity-30' : 'cursor-grab text-muted-foreground hover:text-foreground active:cursor-grabbing'"
                    :title="searchQuery ? 'Hapus filter untuk mengurutkan' : 'Seret untuk mengurutkan'"
                >
                    <GripVertical class="h-4 w-4" />
                </div>

                <!-- Service Info -->
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icon -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sidebar-border/50 bg-muted/40 text-foreground">
                        <div v-if="isSvgString(service.icon)" v-html="service.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-current" />
                        <component
                            v-else-if="service.icon && getIcon(service.icon)"
                            :is="getIcon(service.icon)"
                            class="h-5 w-5"
                        />
                        <Briefcase v-else class="h-5 w-5 text-muted-foreground/50" />
                    </div>

                    <!-- Text -->
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-foreground">
                            {{ service.title }}
                        </p>
                        <p class="truncate text-xs text-muted-foreground mt-0.5">
                            {{ service.short_description }}
                        </p>
                    </div>
                </div>

                <!-- Status Toggle -->
                <div class="flex justify-center">
                    <button
                        :id="`toggle-service-${service.id}`"
                        @click="toggleActive(service)"
                        :disabled="togglingId === service.id"
                        class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1"
                        :class="service.is_active
                            ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 focus:ring-emerald-500/30'
                            : 'bg-muted/60 text-muted-foreground hover:bg-muted focus:ring-muted-foreground/30'"
                    >
                        <Loader2 v-if="togglingId === service.id" class="h-3 w-3 animate-spin" />
                        <ToggleRight v-else-if="service.is_active" class="h-3.5 w-3.5" />
                        <ToggleLeft v-else class="h-3.5 w-3.5" />
                        <span>{{ service.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </button>
                </div>

                <!-- Order number -->
                <div class="text-center">
                    <span class="rounded-lg bg-muted/50 px-2.5 py-1 text-xs font-mono font-medium text-muted-foreground">
                        #{{ service.order }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-1.5">
                    <Button
                        :id="`btn-edit-service-${service.id}`"
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                        title="Edit"
                        @click="openEdit(service)"
                    >
                        <Edit2 class="h-4 w-4" />
                    </Button>
                    <Button
                        :id="`btn-delete-service-${service.id}`"
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-muted-foreground hover:text-destructive"
                        title="Hapus"
                        @click="openDelete(service)"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Drag hint -->
        <p v-if="servicesList.length > 1 && !searchQuery" class="text-center text-xs text-muted-foreground/60">
            Seret baris untuk mengubah urutan tampil layanan di halaman publik.
        </p>
    </div>

    <!-- ================================================================
         SHEET: CREATE / EDIT
    ================================================================ -->
    <Sheet v-model:open="sheetOpen">
        <SheetContent side="right" class="w-full sm:max-w-2xl overflow-y-auto flex flex-col gap-0 p-0">
            <!-- Header -->
            <SheetHeader class="border-b border-sidebar-border/50 px-6 py-5">
                <SheetTitle class="text-lg font-bold">
                    {{ isEditing ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
                </SheetTitle>
                <SheetDescription class="text-sm text-muted-foreground">
                    {{ isEditing
                        ? 'Perbarui informasi layanan yang sudah ada.'
                        : 'Isi detail layanan profesional baru yang ingin Anda tampilkan.' }}
                </SheetDescription>
            </SheetHeader>

            <!-- Form body -->
            <form @submit.prevent="submitForm" class="flex-1 space-y-6 px-6 py-6">

                <!-- Title -->
                <div class="grid gap-2">
                    <Label for="service-title" class="text-xs font-bold text-foreground">
                        Judul Layanan <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="service-title"
                        v-model="form.title"
                        placeholder="Contoh: Full-Stack Web Development"
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <!-- Slug -->
                <div class="grid gap-2">
                    <Label for="service-slug" class="text-xs font-bold text-foreground flex items-center gap-2">
                        Slug
                        <Badge variant="outline" class="text-[10px] px-1.5 py-0 font-normal text-muted-foreground">
                            Auto-generate
                        </Badge>
                    </Label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground select-none">
                            /services/
                        </span>
                        <Input
                            id="service-slug"
                            v-model="form.slug"
                            placeholder="full-stack-web-development"
                            class="pl-[4.5rem] font-mono text-sm"
                            @input="onSlugInput"
                            autocomplete="off"
                        />
                    </div>
                    <InputError :message="form.errors.slug" />
                </div>

                <!-- Short Description -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="service-short-desc" class="text-xs font-bold text-foreground">
                            Deskripsi Singkat <span class="text-destructive">*</span>
                        </Label>
                        <span
                            class="text-xs tabular-nums transition-colors"
                            :class="isShortDescOverLimit ? 'text-destructive font-semibold' : 'text-muted-foreground'"
                        >
                            {{ shortDescLength }}/200
                        </span>
                    </div>
                    <textarea
                        id="service-short-desc"
                        v-model="form.short_description"
                        rows="3"
                        maxlength="200"
                        placeholder="Deskripsikan singkat apa yang Anda tawarkan di layanan ini..."
                        class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 resize-none transition-colors"
                        :class="isShortDescOverLimit ? 'border-destructive focus-visible:ring-destructive' : ''"
                    />
                    <p v-if="isShortDescOverLimit" class="text-xs text-destructive">
                        Deskripsi singkat maksimal 200 karakter.
                    </p>
                    <InputError :message="form.errors.short_description" />
                </div>

                <!-- Icon Picker -->
                <div class="grid gap-2">
                    <Label class="text-xs font-bold text-foreground">Ikon Pendukung</Label>

                    <div class="flex border-b border-border mb-1">
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

                    <div v-if="activeIconTab === 'picker'" class="space-y-2">
                        <!-- Selected icon display / trigger -->
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                id="btn-icon-picker"
                                @click="iconPickerOpen = !iconPickerOpen"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border-2 transition-all duration-200"
                                :class="form.icon
                                    ? 'border-primary/50 bg-primary/5 text-primary'
                                    : 'border-dashed border-sidebar-border text-muted-foreground hover:border-primary/50 hover:text-primary'"
                            >
                                <div v-if="isSvgString(form.icon)" v-html="form.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-current" />
                                <component v-else-if="form.icon && getIcon(form.icon)" :is="getIcon(form.icon)" class="h-5 w-5" />
                                <Plus v-else class="h-4 w-4" />
                            </button>

                            <div class="flex-1 min-w-0">
                                <p v-if="form.icon" class="text-sm font-medium text-foreground truncate">
                                    {{ isSvgString(form.icon) ? 'Ikon Terpilih (SVG)' : form.icon }}
                                </p>
                                <p v-else class="text-sm text-muted-foreground">
                                    Pilih ikon untuk layanan ini
                                </p>
                                <p class="text-xs text-muted-foreground/70">
                                    Klik kotak di kiri untuk membuka pemilih ikon
                                </p>
                            </div>

                            <Button
                                v-if="form.icon"
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 text-muted-foreground hover:text-destructive shrink-0"
                                @click="clearIcon"
                            >
                                <X class="h-4 w-4" />
                            </Button>
                        </div>

                        <!-- Icon Picker Panel -->
                        <div
                            v-if="iconPickerOpen"
                            class="rounded-xl border border-sidebar-border/70 bg-card p-4 shadow-lg"
                        >
                            <!-- Search icons -->
                            <div class="relative mb-3">
                                <Search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    id="icon-search"
                                    v-model="iconSearchQuery"
                                    placeholder="Cari ikon..."
                                    class="h-8 pl-8 text-sm"
                                    autocomplete="off"
                                />
                            </div>

                            <!-- Icon grid -->
                            <div class="grid grid-cols-8 gap-1.5 max-h-48 overflow-y-auto pr-1">
                                <button
                                    v-for="iconName in filteredIcons"
                                    :key="iconName"
                                    type="button"
                                    :title="iconName"
                                    @click="selectIcon(iconName)"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary/40"
                                    :class="form.icon === iconName
                                        ? 'bg-primary text-primary-foreground shadow-sm'
                                        : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                                >
                                    <component :is="getIcon(iconName)" class="h-4 w-4" />
                                </button>
                                <p
                                    v-if="filteredIcons.length === 0"
                                    class="col-span-8 py-4 text-center text-sm text-muted-foreground"
                                >
                                    Ikon tidak ditemukan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="space-y-2">
                        <textarea
                            v-model="form.icon"
                            rows="4"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs font-mono shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
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

                <!-- Status & Order row -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Status -->
                    <div class="grid gap-2">
                        <Label class="text-xs font-bold text-foreground">Status</Label>
                        <button
                            type="button"
                            id="btn-toggle-status"
                            @click="form.is_active = !form.is_active"
                            class="flex h-10 items-center gap-2 rounded-md border px-3 text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1"
                            :class="form.is_active
                                ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 focus:ring-emerald-500/30'
                                : 'border-input bg-background text-muted-foreground focus:ring-muted-foreground/30'"
                        >
                            <ToggleRight v-if="form.is_active" class="h-4 w-4" />
                            <ToggleLeft v-else class="h-4 w-4" />
                            {{ form.is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                        <InputError :message="form.errors.is_active" />
                    </div>

                    <!-- Order -->
                    <div class="grid gap-2">
                        <Label for="service-order" class="text-xs font-bold text-foreground">
                            Urutan Tampil
                        </Label>
                        <Input
                            id="service-order"
                            v-model.number="form.order"
                            type="number"
                            min="0"
                            placeholder="0"
                        />
                        <InputError :message="form.errors.order" />
                    </div>
                </div>

                <!-- Long Description (CKEditor) -->
                <div class="grid gap-2">
                    <Label class="text-xs font-bold text-foreground">
                        Deskripsi Detail
                        <span class="ml-1 font-normal text-muted-foreground">(opsional)</span>
                    </Label>
                    <p class="text-xs text-muted-foreground -mt-1">
                        Jelaskan layanan ini secara mendalam: proses kerja, deliverables, teknologi yang digunakan, dll.
                    </p>
                    <CKEditor
                        v-model="form.long_description"
                        placeholder="Tulis deskripsi detail layanan Anda di sini..."
                    />
                    <InputError :message="form.errors.long_description" />
                </div>

                <!-- Form footer -->
                <div class="flex items-center justify-end gap-3 border-t border-sidebar-border/50 pt-6">
                    <Button
                        type="button"
                        variant="ghost"
                        @click="sheetOpen = false"
                        :disabled="form.processing"
                    >
                        Batal
                    </Button>
                    <Button
                        id="btn-submit-service"
                        type="submit"
                        :disabled="form.processing || isShortDescOverLimit"
                        class="gap-2 min-w-28"
                    >
                        <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambah Layanan' }}</span>
                    </Button>
                </div>
            </form>
        </SheetContent>
    </Sheet>

    <!-- ================================================================
         DELETE DIALOG
    ================================================================ -->
    <Dialog v-model:open="deleteDialogOpen">
        <DialogContent class="sm:max-w-md bg-card border-border">
            <DialogHeader>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-destructive/10 mb-2">
                    <AlertTriangle class="h-6 w-6 text-destructive" />
                </div>
                <DialogTitle class="text-lg font-bold">Hapus Layanan?</DialogTitle>
                <DialogDescription class="text-sm text-muted-foreground">
                    Tindakan ini tidak dapat dibatalkan. Layanan
                    <span v-if="deletingService" class="font-semibold text-foreground">
                        "{{ deletingService.title }}"
                    </span>
                    akan dihapus secara permanen dari database.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex-row gap-3 sm:justify-end">
                <Button
                    variant="ghost"
                    @click="deleteDialogOpen = false"
                    :disabled="deleteForm.processing"
                >
                    Batal
                </Button>
                <Button
                    id="btn-confirm-delete-service"
                    variant="destructive"
                    @click="confirmDelete"
                    :disabled="deleteForm.processing"
                    class="gap-2"
                >
                    <Loader2 v-if="deleteForm.processing" class="h-4 w-4 animate-spin" />
                    <Trash2 v-else class="h-4 w-4" />
                    Hapus Permanen
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
