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
import { index as workflowsIndex } from '@/routes/workflows';
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
interface Workflow {
    id: number;
    title: string;
    slug: string;
    short_description: string;
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
    workflows: Workflow[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'My Workflow Management',
                href: workflowsIndex(),
            },
        ],
    },
});

// -----------------------------------------------------------------------
// Lucide Icon Picker
// -----------------------------------------------------------------------
// A curated set of commonly used Lucide icons for workflow steps
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
    return name;
};

// -----------------------------------------------------------------------
// Workflows List (local copy for drag-and-drop)
// -----------------------------------------------------------------------
const workflowsList = ref<Workflow[]>([...props.workflows]);

watch(() => props.workflows, (newVal) => {
    workflowsList.value = [...newVal];
});

// -----------------------------------------------------------------------
// Search
// -----------------------------------------------------------------------
const searchQuery = ref('');

const filteredWorkflows = computed(() => {
    if (!searchQuery.value) {
        return workflowsList.value;
    }
    const q = searchQuery.value.toLowerCase();
    return workflowsList.value.filter(w =>
        w.title.toLowerCase().includes(q) ||
        w.short_description.toLowerCase().includes(q)
    );
});

// -----------------------------------------------------------------------
// Stats
// -----------------------------------------------------------------------
const activeCount = computed(() => workflowsList.value.filter(w => w.is_active).length);

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
        const items = [...workflowsList.value];
        const dragged = items[draggedIndex.value];
        items.splice(draggedIndex.value, 1);
        items.splice(dragOverIndex.value, 0, dragged);
        workflowsList.value = items;

        router.post('/admin-cms/workflows/reorder', {
            ids: items.map(w => w.id),
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

const toggleActive = (workflow: Workflow) => {
    togglingId.value = workflow.id;
    router.post(`/admin-cms/workflows/${workflow.id}/toggle-active`, {}, {
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
const editingWorkflowId = ref<number | null>(null);

const form = useForm({
    title: '',
    slug: '',
    short_description: '',
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
    editingWorkflowId.value = null;
    isSlugManuallyEdited.value = false;
    form.reset();
    form.clearErrors();
    form.order = workflowsList.value.length > 0
        ? Math.max(...workflowsList.value.map(w => w.order)) + 1
        : 0;
    form.is_active = true;
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = 'picker';
    sheetOpen.value = true;
};

const openEdit = (workflow: Workflow) => {
    isEditing.value = true;
    editingWorkflowId.value = workflow.id;
    isSlugManuallyEdited.value = true;
    form.title = workflow.title;
    form.slug = workflow.slug;
    form.short_description = workflow.short_description;
    form.icon = workflow.icon;
    form.is_active = workflow.is_active;
    form.order = workflow.order;
    form.clearErrors();
    iconSearchQuery.value = '';
    iconPickerOpen.value = false;
    activeIconTab.value = isSvgString(workflow.icon) ? 'custom' : 'picker';
    sheetOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingWorkflowId.value) {
        form.put(`/admin-cms/workflows/${editingWorkflowId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/admin-cms/workflows', {
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
const deletingWorkflow = ref<Workflow | null>(null);
const deleteForm = useForm({});

const openDelete = (workflow: Workflow) => {
    deletingWorkflow.value = workflow;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deletingWorkflow.value) return;
    deleteForm.delete(`/admin-cms/workflows/${deletingWorkflow.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            deletingWorkflow.value = null;
        },
    });
};
</script>

<template>
    <Head title="My Workflow Management" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                My Workflow Management
            </h1>
            <p class="text-sm text-muted-foreground">
                Kelola tahapan alur kerja (workflow) pengembangan sistem Anda.
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
                    id="workflows-search"
                    v-model="searchQuery"
                    placeholder="Cari alur kerja..."
                    class="pl-9 pr-8 bg-card border-border/80 h-9 text-xs"
                />
                <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                >
                    <X class="h-3.5 w-3.5" />
                </button>
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
                        {{ workflowsList.length }} Total
                    </Badge>
                </div>

                <!-- Add button -->
                <Button id="btn-add-workflow" @click="openCreate" class="gap-2 shrink-0">
                    <Plus class="h-4 w-4" />
                    Tambah Alur Kerja
                </Button>
            </div>
        </div>

        <!-- ================================================================
             WORKFLOWS TABLE
        ================================================================ -->
        <div class="rounded-2xl border border-border/70 bg-card overflow-hidden shadow-2xs">
            <!-- Table header -->
            <div class="grid grid-cols-[auto_1fr_120px_100px_80px] items-center gap-4 border-b border-border/70 bg-muted/20 px-4 py-3">
                <div class="w-8"></div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Alur Kerja</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-center">Status</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-center">Urutan</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
            </div>

            <!-- Empty state -->
            <div
                v-if="filteredWorkflows.length === 0"
                class="flex flex-col items-center justify-center gap-4 py-16 text-center"
            >
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-muted/50">
                    <Briefcase class="h-7 w-7 text-muted-foreground/60" />
                </div>
                <div>
                    <p class="font-semibold text-foreground">
                        {{ searchQuery ? 'Tidak ada hasil' : 'Belum ada alur kerja' }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ searchQuery
                            ? 'Coba kata kunci berbeda atau hapus filter.'
                            : 'Klik "Tambah Alur Kerja" untuk menambahkan alur kerja pertama Anda.' }}
                    </p>
                </div>
                <Button v-if="!searchQuery" @click="openCreate" variant="outline" size="sm" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Tambah Alur Kerja
                </Button>
            </div>

            <!-- Rows -->
            <div
                v-for="(workflow, index) in filteredWorkflows"
                :key="workflow.id"
                :draggable="!searchQuery"
                @dragstart="onDragStart(index, $event)"
                @dragover="onDragOver(index, $event)"
                @dragend="onDragEnd"
                @dragleave="onDragLeave"
                @click="openEdit(workflow)"
                class="grid grid-cols-[auto_1fr_120px_100px_80px] items-center gap-4 border-b border-border/40 px-4 py-4 transition-all duration-150 last:border-b-0 cursor-pointer"
                :class="{
                    'bg-primary/5 scale-[1.01] shadow-md': dragOverIndex === index && draggedIndex !== index,
                    'opacity-40': draggedIndex === index,
                    'hover:bg-muted/20': dragOverIndex !== index || draggedIndex === index,
                }"
            >
                <!-- Drag Handle -->
                <div 
                    @click.stop
                    class="flex h-8 w-8 cursor-grab items-center justify-center rounded-lg text-muted-foreground/40 hover:bg-muted hover:text-muted-foreground active:cursor-grabbing"
                    :class="{ 'pointer-events-none opacity-20': searchQuery }"
                >
                    <GripVertical class="h-4 w-4" />
                </div>

                <!-- Workflow Details -->
                <div class="flex items-start gap-4 min-w-0">
                    <!-- Icon Display -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-muted/30 text-primary border border-border/60">
                        <div v-if="isSvgString(workflow.icon)" v-html="workflow.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-current" />
                        <component v-else :is="getIcon(workflow.icon) || Briefcase" class="h-5 w-5" />
                    </div>

                    <!-- Meta -->
                    <div class="flex flex-col gap-0.5 min-w-0">
                        <span class="font-semibold text-foreground text-sm truncate">
                            {{ workflow.title }}
                        </span>
                        <span class="text-xs text-muted-foreground truncate max-w-xl">
                            {{ workflow.short_description }}
                        </span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="flex justify-center" @click.stop>
                    <button
                        @click="toggleActive(workflow)"
                        :disabled="togglingId === workflow.id"
                        class="inline-flex rounded-full transition-opacity disabled:opacity-50 cursor-pointer"
                        title="Klik untuk mengubah status"
                    >
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                workflow.is_active
                                    ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                                    : 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20'
                            ]"
                        >
                            <Loader2 v-if="togglingId === workflow.id" class="h-2.5 w-2.5 animate-spin mr-1" />
                            {{ workflow.is_active ? 'Aktif' : 'Draft' }}
                        </span>
                    </button>
                </div>

                <!-- Order Indicator -->
                <div class="text-center text-xs font-mono font-medium text-muted-foreground">
                    #{{ workflow.order }}
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-1" @click.stop>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10"
                        @click="openEdit(workflow)"
                    >
                        <Edit2 class="h-4 w-4" />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                        @click="openDelete(workflow)"
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
            <SheetContent class="sm:max-w-lg overflow-y-auto flex flex-col gap-6 p-6">
                <SheetHeader class="border-b border-border/70 pb-4 text-left">
                    <SheetTitle class="text-lg font-bold tracking-tight text-foreground">
                        {{ isEditing ? 'Edit Alur Kerja' : 'Tambah Alur Kerja Baru' }}
                    </SheetTitle>
                    <SheetDescription class="text-xs text-muted-foreground">
                        {{ isEditing ? 'Ubah informasi alur kerja yang sudah ada di sini. Klik Simpan setelah selesai.' : 'Tambahkan langkah alur kerja baru untuk ditampilkan pada portofolio Anda.' }}
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submitForm" class="space-y-5 flex-1">
                    <!-- Title -->
                    <div class="space-y-1.5">
                        <Label for="title" class="text-xs font-semibold text-foreground">Judul Alur Kerja <span class="text-destructive">*</span></Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Contoh: Discovery & Analysis"
                            class="h-9 text-xs bg-card border-border/80"
                            required
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <!-- Slug -->
                    <div class="space-y-1.5">
                        <Label for="slug" class="text-xs font-semibold text-foreground">Slug URL <span class="text-destructive">*</span></Label>
                        <Input
                            id="slug"
                            v-model="form.slug"
                            @input="onSlugInput"
                            placeholder="discovery-analysis"
                            class="h-9 text-xs font-mono bg-card border-border/80"
                            required
                        />
                        <InputError :message="form.errors.slug" />
                    </div>

                    <!-- Short Description -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <Label for="short_description" class="text-xs font-semibold text-foreground">Deskripsi Singkat <span class="text-destructive">*</span></Label>
                            <span 
                                class="text-[10px] font-semibold"
                                :class="isShortDescOverLimit ? 'text-destructive' : 'text-muted-foreground'"
                            >
                                {{ shortDescLength }}/200
                            </span>
                        </div>
                        <textarea
                            id="short_description"
                            v-model="form.short_description"
                            rows="3"
                            class="flex w-full rounded-md border border-border/80 bg-card px-3 py-2 text-xs shadow-2xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Tuliskan rangkuman deskripsi alur kerja (maks 200 karakter)..."
                            required
                        ></textarea>
                        <InputError :message="form.errors.short_description" />
                    </div>

                    <!-- Icon Picker -->
                    <div class="space-y-2">
                        <Label class="text-xs font-semibold text-foreground">Pilih Ikon Pendukung</Label>
                        
                        <div class="flex bg-card p-1 rounded-xl border border-border/60 gap-1 mb-3">
                            <button 
                                type="button" 
                                @click="activeIconTab = 'picker'" 
                                class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                                :class="activeIconTab === 'picker' ? 'bg-primary/10 text-primary font-bold' : 'text-muted-foreground hover:text-foreground'"
                            >
                                Pilih dari Lucide
                            </button>
                            <button 
                                type="button" 
                                @click="activeIconTab = 'custom'" 
                                class="flex-1 py-1.5 px-3 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                                :class="activeIconTab === 'custom' ? 'bg-primary/10 text-primary font-bold' : 'text-muted-foreground hover:text-foreground'"
                            >
                                Custom SVG
                            </button>
                        </div>

                        <div v-if="activeIconTab === 'picker'" class="flex items-center gap-3 bg-muted/20 p-3 rounded-xl border border-border/70">
                            <!-- Current Icon View -->
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-card border border-border/80 text-foreground shadow-2xs shrink-0">
                                <div v-if="isSvgString(form.icon)" v-html="form.icon" class="h-6 w-6 flex items-center justify-center [&_svg]:h-6 [&_svg]:w-6 [&_svg]:text-current" />
                                <component v-else :is="getIcon(form.icon) || Briefcase" class="h-6 w-6 text-primary" />
                            </div>

                            <!-- Controls -->
                            <div class="flex items-center gap-2">
                                <Dialog v-model:open="iconPickerOpen">
                                    <Button type="button" variant="outline" size="sm" class="h-8 text-xs border-border/80" @click="iconPickerOpen = true">
                                        Pilih Ikon...
                                    </Button>
                                    <DialogContent class="sm:max-w-md">
                                        <DialogHeader>
                                            <DialogTitle class="text-base font-bold">Pilih Ikon Lucide</DialogTitle>
                                            <DialogDescription class="text-xs">
                                                Cari dan pilih ikon yang paling relevan dengan langkah alur kerja ini.
                                            </DialogDescription>
                                        </DialogHeader>

                                        <!-- Search input inside picker -->
                                        <div class="relative my-2">
                                            <Search class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
                                            <Input
                                                v-model="iconSearchQuery"
                                                placeholder="Cari ikon..."
                                                class="pl-9 h-8 text-xs"
                                                @keydown.enter.prevent
                                            />
                                        </div>

                                        <!-- Grid of Icons -->
                                        <div class="grid grid-cols-6 gap-2 max-h-[300px] overflow-y-auto p-2 border border-border/70 rounded-xl bg-muted/20">
                                            <button
                                                v-for="name in filteredIcons"
                                                :key="name"
                                                type="button"
                                                @click="selectIcon(name)"
                                                class="flex flex-col items-center justify-center p-2 rounded-lg hover:bg-primary/10 hover:text-primary transition-colors text-muted-foreground cursor-pointer"
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
                                    class="text-destructive hover:bg-destructive/10 h-8 text-xs cursor-pointer"
                                    @click="clearIcon"
                                >
                                    <X class="h-3.5 w-3.5 mr-1" /> Hapus
                                </Button>
                            </div>
                        </div>
                        
                        <div v-else class="space-y-2">
                            <textarea
                                v-model="form.icon"
                                rows="4"
                                class="flex min-h-[90px] w-full rounded-xl border border-border/80 bg-card px-3 py-2 text-xs font-mono placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Tempel kode markup SVG Anda di sini (misal: <svg>...</svg>)"
                            ></textarea>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-muted-foreground">Pastikan kode SVG valid dan memiliki stroke='currentColor' atau fill='currentColor' agar warnanya seragam.</span>
                                <Button 
                                    v-if="form.icon" 
                                    type="button" 
                                    variant="ghost" 
                                    size="sm" 
                                    class="text-destructive hover:bg-destructive/10 h-7 text-[10px] cursor-pointer"
                                    @click="clearIcon"
                                >
                                    Hapus
                                </Button>
                            </div>
                        </div>
                        <InputError :message="form.errors.icon" />
                    </div>

                    <!-- Status (is_active) Toggle Box -->
                    <label
                        class="flex items-center justify-between p-3.5 rounded-xl border transition-all cursor-pointer select-none"
                        :class="form.is_active
                            ? 'border-emerald-500/40 bg-emerald-500/10 dark:bg-emerald-500/15 shadow-2xs'
                            : 'border-border/70 bg-card hover:bg-muted/30'"
                    >
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-foreground block">Tampilkan ke Publik</span>
                            <p class="text-[10px] text-muted-foreground">
                                Aktifkan agar alur kerja ini langsung tayang di halaman portfolio Anda.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click.prevent="form.is_active = !form.is_active"
                            class="text-primary transition-colors focus:outline-none cursor-pointer"
                        >
                            <ToggleRight v-if="form.is_active" class="h-9 w-9 text-emerald-500" />
                            <ToggleLeft v-else class="h-9 w-9 text-slate-400" />
                        </button>
                    </label>

                    <!-- Footer actions -->
                    <div class="flex justify-end gap-2.5 border-t border-border/70 pt-4">
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="sheetOpen = false"
                            :disabled="form.processing"
                            class="h-9 text-xs px-4 cursor-pointer border-border/80"
                        >
                            Batal
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing || isShortDescOverLimit"
                            class="h-9 text-xs px-5 bg-primary text-white hover:bg-primary/90 font-semibold cursor-pointer shadow-xs gap-1.5"
                        >
                            <Loader2 v-if="form.processing" class="h-3.5 w-3.5 animate-spin" />
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
                    <DialogTitle>Hapus Alur Kerja?</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus alur kerja <strong>{{ deletingWorkflow?.title }}</strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
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
