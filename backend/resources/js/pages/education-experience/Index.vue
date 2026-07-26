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
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import {
    Plus,
    Search,
    Edit2,
    Trash2,
    GripVertical,
    AlertTriangle,
    Briefcase,
    GraduationCap,
    X,
    Loader2,
    Calendar,
    MapPin,
} from '@lucide/vue';
import { index as educationExperienceIndex } from '@/routes/education-experience';

// -----------------------------------------------------------------------
// Types & Interfaces
// -----------------------------------------------------------------------
interface Experience {
    id: number;
    company: string;
    title_position: string;
    location: string | null;
    start_date: string; // YYYY-MM
    end_date: string | null; // YYYY-MM
    description: string | null;
    website_url: string | null;
    logo_media_id: number | null;
    logo: any | null;
    order: number;
}

interface Education {
    id: number;
    institution: string;
    degree: string | null;
    major: string;
    gpa: string | null;
    location: string | null;
    start_date: string; // YYYY-MM
    end_date: string | null; // YYYY-MM
    description: string | null;
    logo_media_id: number | null;
    logo: any | null;
    order: number;
}

// -----------------------------------------------------------------------
// Props & Layout Setup
// -----------------------------------------------------------------------
const props = defineProps<{
    experiences: Experience[];
    educations: Education[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Education & Experience',
                href: educationExperienceIndex(),
            },
        ],
    },
});

// -----------------------------------------------------------------------
// Tabs & Local Lists
// -----------------------------------------------------------------------
const activeTab = ref<'experience' | 'education'>('experience');

const expList = ref<Experience[]>([...props.experiences]);
const eduList = ref<Education[]>([...props.educations]);

watch(() => props.experiences, (newVal) => {
    expList.value = [...newVal];
});

watch(() => props.educations, (newVal) => {
    eduList.value = [...newVal];
});

// -----------------------------------------------------------------------
// Search Filters
// -----------------------------------------------------------------------
const searchQuery = ref('');

const filteredExperiences = computed(() => {
    if (!searchQuery.value) return expList.value;
    const q = searchQuery.value.toLowerCase();
    return expList.value.filter(e =>
        e.company.toLowerCase().includes(q) ||
        e.title_position.toLowerCase().includes(q) ||
        (e.location && e.location.toLowerCase().includes(q))
    );
});

const filteredEducations = computed(() => {
    if (!searchQuery.value) return eduList.value;
    const q = searchQuery.value.toLowerCase();
    return eduList.value.filter(e =>
        e.institution.toLowerCase().includes(q) ||
        e.major.toLowerCase().includes(q) ||
        (e.degree && e.degree.toLowerCase().includes(q)) ||
        (e.location && e.location.toLowerCase().includes(q))
    );
});

// Reset search on tab switch
watch(activeTab, () => {
    searchQuery.value = '';
});

// Helper: Format Date for Display (e.g. "2024-01" -> "Jan 2024")
const formatDisplayDate = (dateStr: string | null): string => {
    if (!dateStr) return 'Sekarang';
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const parts = dateStr.split('-');
    if (parts.length < 2) return dateStr;
    const year = parts[0];
    const monthIdx = parseInt(parts[1], 10) - 1;
    return `${months[monthIdx]} ${year}`;
};

const formatPeriod = (start: string, end: string | null): string => {
    return `${formatDisplayDate(start)} — ${formatDisplayDate(end)}`;
};

// -----------------------------------------------------------------------
// Drag & Drop Reorder
// -----------------------------------------------------------------------
const draggedIndex = ref<number | null>(null);
const dragOverIndex = ref<number | null>(null);
const isDragging = ref(false);

const onDragStart = (index: number, e: DragEvent) => {
    if (searchQuery.value) return; // Disable drag during search
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
        if (activeTab.value === 'experience') {
            const items = [...expList.value];
            const dragged = items[draggedIndex.value];
            items.splice(draggedIndex.value, 1);
            items.splice(dragOverIndex.value, 0, dragged);
            expList.value = items;

            router.post('/admin-cms/experiences/reorder', {
                ids: items.map(x => x.id),
            }, { preserveScroll: true });
        } else {
            const items = [...eduList.value];
            const dragged = items[draggedIndex.value];
            items.splice(draggedIndex.value, 1);
            items.splice(dragOverIndex.value, 0, dragged);
            eduList.value = items;

            router.post('/admin-cms/educations/reorder', {
                ids: items.map(x => x.id),
            }, { preserveScroll: true });
        }
    }
    draggedIndex.value = null;
    dragOverIndex.value = null;
    isDragging.value = false;
};

const onDragLeave = () => {
    dragOverIndex.value = null;
};

// -----------------------------------------------------------------------
// Drawers (Create / Edit) & Forms
// -----------------------------------------------------------------------
const expSheetOpen = ref(false);
const eduSheetOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const expForm = useForm({
    company: '',
    title_position: '',
    location: '',
    start_date: '',
    end_date: '' as string | null,
    description: '',
    website_url: '',
    logo_media_id: null as number | null,
    order: 0,
});

const eduForm = useForm({
    institution: '',
    degree: '',
    major: '',
    gpa: '',
    location: '',
    start_date: '',
    end_date: '' as string | null,
    description: '',
    logo_media_id: null as number | null,
    order: 0,
});

// Media library picker state
const mediaModalOpen = ref(false);
const selectedLogo = ref<any>(null);

const openMediaPicker = () => {
    mediaModalOpen.value = true;
};

const handleLogoSelect = (media: any) => {
    if (activeTab.value === 'experience') {
        expForm.logo_media_id = media.id;
    } else {
        eduForm.logo_media_id = media.id;
    }
    selectedLogo.value = media;
};

const removeLogo = () => {
    if (activeTab.value === 'experience') {
        expForm.logo_media_id = null;
    } else {
        eduForm.logo_media_id = null;
    }
    selectedLogo.value = null;
};

// Open Create Form Drawer
const openCreate = () => {
    isEditing.value = false;
    editingId.value = null;
    selectedLogo.value = null;

    if (activeTab.value === 'experience') {
        expForm.reset();
        expForm.clearErrors();
        expForm.order = expList.value.length > 0
            ? Math.max(...expList.value.map(x => x.order)) + 1
            : 0;
        expSheetOpen.value = true;
    } else {
        eduForm.reset();
        eduForm.clearErrors();
        eduForm.order = eduList.value.length > 0
            ? Math.max(...eduList.value.map(x => x.order)) + 1
            : 0;
        eduSheetOpen.value = true;
    }
};

// Open Edit Form Drawer
const openEdit = (item: any) => {
    isEditing.value = true;
    editingId.value = item.id;
    selectedLogo.value = item.logo;

    if (activeTab.value === 'experience') {
        expForm.company = item.company;
        expForm.title_position = item.title_position;
        expForm.location = item.location || '';
        expForm.start_date = item.start_date;
        expForm.end_date = item.end_date || '';
        expForm.description = item.description || '';
        expForm.website_url = item.website_url || '';
        expForm.logo_media_id = item.logo_media_id;
        expForm.order = item.order;
        expForm.clearErrors();
        expSheetOpen.value = true;
    } else {
        eduForm.institution = item.institution;
        eduForm.degree = item.degree || '';
        eduForm.major = item.major;
        eduForm.gpa = item.gpa || '';
        eduForm.location = item.location || '';
        eduForm.start_date = item.start_date;
        eduForm.end_date = item.end_date || '';
        eduForm.description = item.description || '';
        eduForm.logo_media_id = item.logo_media_id;
        eduForm.order = item.order;
        eduForm.clearErrors();
        eduSheetOpen.value = true;
    }
};

// Submit Forms
const submitExpForm = () => {
    if (!expForm.end_date) expForm.end_date = null;

    if (isEditing.value && editingId.value) {
        expForm.put(`/admin-cms/experiences/${editingId.value}`, {
            onSuccess: () => {
                expSheetOpen.value = false;
                expForm.reset();
            },
        });
    } else {
        expForm.post('/admin-cms/experiences', {
            onSuccess: () => {
                expSheetOpen.value = false;
                expForm.reset();
            },
        });
    }
};

const submitEduForm = () => {
    if (!eduForm.end_date) eduForm.end_date = null;

    if (isEditing.value && editingId.value) {
        eduForm.put(`/admin-cms/educations/${editingId.value}`, {
            onSuccess: () => {
                eduSheetOpen.value = false;
                eduForm.reset();
            },
        });
    } else {
        eduForm.post('/admin-cms/educations', {
            onSuccess: () => {
                eduSheetOpen.value = false;
                eduForm.reset();
            },
        });
    }
};

// -----------------------------------------------------------------------
// Delete Confirmation Dialog
// -----------------------------------------------------------------------
const deleteDialogOpen = ref(false);
const deletingItem = ref<any | null>(null);

const openDelete = (item: any) => {
    deletingItem.value = item;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!deletingItem.value) return;

    if (activeTab.value === 'experience') {
        router.delete(`/admin-cms/experiences/${deletingItem.value.id}`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                deletingItem.value = null;
            },
        });
    } else {
        router.delete(`/admin-cms/educations/${deletingItem.value.id}`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                deletingItem.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Manajemen Education & Experience" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Education & Experience
            </h1>
            <p class="text-sm text-muted-foreground">
                Kelola riwayat karir profesional dan latar belakang pendidikan akademis Anda.
            </p>
        </div>

        <!-- ================================================================
             TABS BAR & ACTIONS
        ================================================================ -->
        <div class="flex flex-col gap-4 border-b border-border/70 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Tabs selection -->
            <div class="flex bg-card p-1 rounded-2xl border border-border/60 gap-1">
                <button
                    @click="activeTab = 'experience'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'experience'
                        ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    <Briefcase class="h-4 w-4" />
                    Pengalaman Kerja
                </button>
                <button
                    @click="activeTab = 'education'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'education'
                        ? 'bg-primary/10 text-primary font-bold border-b-2 border-primary'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    <GraduationCap class="h-4 w-4" />
                    Pendidikan
                </button>
            </div>

            <!-- Toolbar tools (Search + Add button) -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search bar -->
                <div class="relative max-w-xs flex-1 min-w-[200px]">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        :placeholder="activeTab === 'experience' ? 'Cari pengalaman...' : 'Cari pendidikan...'"
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

                <!-- Add Button -->
                <Button id="btn-add-item" @click="openCreate" class="gap-2 shrink-0">
                    <Plus class="h-4 w-4" />
                    <span>{{ activeTab === 'experience' ? 'Tambah Pengalaman' : 'Tambah Pendidikan' }}</span>
                </Button>
            </div>
        </div>

        <!-- ================================================================
             EXPERIENCE TAB CONTENT
        ================================================================ -->
        <div v-if="activeTab === 'experience'" class="space-y-4">
            <div class="rounded-2xl border border-border/70 bg-card overflow-hidden shadow-2xs">
                <!-- Table header -->
                <div class="grid grid-cols-[auto_1fr_200px_100px_100px] items-center gap-4 border-b border-border/70 bg-muted/20 px-4 py-3">
                    <div class="w-8"></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Posisi & Perusahaan</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Periode</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-center">Urutan</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
                </div>

                <!-- Empty state -->
                <div
                    v-if="filteredExperiences.length === 0"
                    class="flex flex-col items-center justify-center gap-4 py-16 text-center"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-muted/50">
                        <Briefcase class="h-7 w-7 text-muted-foreground/60" />
                    </div>
                    <div>
                        <p class="font-semibold text-foreground">
                            {{ searchQuery ? 'Tidak ada hasil' : 'Belum ada riwayat pengalaman' }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ searchQuery
                                ? 'Coba kata kunci berbeda atau bersihkan filter.'
                                : 'Klik "Tambah Pengalaman" untuk mendaftarkan karir profesional pertama Anda.' }}
                        </p>
                    </div>
                </div>

                <!-- Rows -->
                <div
                    v-for="(exp, index) in filteredExperiences"
                    :key="exp.id"
                    :draggable="!searchQuery"
                    @dragstart="onDragStart(index, $event)"
                    @dragover="onDragOver(index, $event)"
                    @dragend="onDragEnd"
                    @dragleave="onDragLeave"
                    @click="openEdit(exp)"
                    class="grid grid-cols-[auto_1fr_200px_100px_100px] items-center gap-4 border-b border-border/40 px-4 py-4 transition-all duration-150 last:border-b-0 cursor-pointer"
                    :class="{
                        'bg-primary/5 scale-[1.01] shadow-md': dragOverIndex === index && draggedIndex !== index,
                        'opacity-40': draggedIndex === index,
                        'hover:bg-muted/20': dragOverIndex !== index || draggedIndex === index,
                    }"
                >
                    <!-- Drag handle -->
                    <div
                        @click.stop
                        class="flex w-8 items-center justify-center"
                        :class="searchQuery ? 'cursor-not-allowed opacity-30' : 'cursor-grab text-muted-foreground hover:text-foreground active:cursor-grabbing'"
                    >
                        <GripVertical class="h-4 w-4" />
                    </div>

                    <!-- Experience Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-10 shrink-0 rounded-xl overflow-hidden border border-border/60 bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                            <img
                                v-if="exp.logo?.urls?.thumbnail"
                                :src="exp.logo.urls.thumbnail"
                                :alt="exp.company"
                                class="h-full w-full object-contain p-1"
                            />
                            <Briefcase v-else class="h-5 w-5 text-muted-foreground/60" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-foreground">
                                {{ exp.title_position }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-0.5">
                                <span class="font-medium text-foreground/80">{{ exp.company }}</span>
                                <span v-if="exp.location" class="flex items-center gap-0.5">
                                    <MapPin class="h-3 w-3" />
                                    {{ exp.location }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Period -->
                    <div class="text-sm font-medium text-muted-foreground flex items-center gap-1.5">
                        <Calendar class="h-4 w-4 text-muted-foreground/60 shrink-0" />
                        <span>{{ formatPeriod(exp.start_date, exp.end_date) }}</span>
                    </div>

                    <!-- Order -->
                    <div class="text-center">
                        <span class="rounded-lg bg-muted/50 px-2.5 py-1 text-xs font-mono font-medium text-muted-foreground">
                            #{{ exp.order }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-1.5" @click.stop>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10"
                            title="Edit"
                            @click="openEdit(exp)"
                        >
                            <Edit2 class="h-4 w-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                            title="Hapus"
                            @click="openDelete(exp)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
            <p v-if="expList.length > 1 && !searchQuery" class="text-center text-xs text-muted-foreground/60">
                Seret baris untuk mengatur urutan kronologis/tampilan manual dari riwayat pengalaman Anda.
            </p>
        </div>

        <!-- ================================================================
             EDUCATION TAB CONTENT
        ================================================================ -->
        <div v-else-if="activeTab === 'education'" class="space-y-4">
            <div class="rounded-2xl border border-border/70 bg-card overflow-hidden shadow-2xs">
                <!-- Table header -->
                <div class="grid grid-cols-[auto_1fr_200px_100px_100px] items-center gap-4 border-b border-border/70 bg-muted/20 px-4 py-3">
                    <div class="w-8"></div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Institusi & Gelar</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Periode</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-center">Urutan</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
                </div>

                <!-- Empty state -->
                <div
                    v-if="filteredEducations.length === 0"
                    class="flex flex-col items-center justify-center gap-4 py-16 text-center"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-muted/50">
                        <GraduationCap class="h-7 w-7 text-muted-foreground/60" />
                    </div>
                    <div>
                        <p class="font-semibold text-foreground">
                            {{ searchQuery ? 'Tidak ada hasil' : 'Belum ada riwayat pendidikan' }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ searchQuery
                                ? 'Coba kata kunci berbeda atau bersihkan filter.'
                                : 'Klik "Tambah Pendidikan" untuk mendaftarkan riwayat akademis Anda.' }}
                        </p>
                    </div>
                </div>

                <!-- Rows -->
                <div
                    v-for="(edu, index) in filteredEducations"
                    :key="edu.id"
                    :draggable="!searchQuery"
                    @dragstart="onDragStart(index, $event)"
                    @dragover="onDragOver(index, $event)"
                    @dragend="onDragEnd"
                    @dragleave="onDragLeave"
                    @click="openEdit(edu)"
                    class="grid grid-cols-[auto_1fr_200px_100px_100px] items-center gap-4 border-b border-border/40 px-4 py-4 transition-all duration-150 last:border-b-0 cursor-pointer"
                    :class="{
                        'bg-primary/5 scale-[1.01] shadow-md': dragOverIndex === index && draggedIndex !== index,
                        'opacity-40': draggedIndex === index,
                        'hover:bg-muted/20': dragOverIndex !== index || draggedIndex === index,
                    }"
                >
                    <!-- Drag handle -->
                    <div
                        @click.stop
                        class="flex w-8 items-center justify-center"
                        :class="searchQuery ? 'cursor-not-allowed opacity-30' : 'cursor-grab text-muted-foreground hover:text-foreground active:cursor-grabbing'"
                    >
                        <GripVertical class="h-4 w-4" />
                    </div>

                    <!-- Education Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-10 w-10 shrink-0 rounded-xl overflow-hidden border border-border/60 bg-slate-50 dark:bg-slate-900 flex items-center justify-center">
                            <img
                                v-if="edu.logo?.urls?.thumbnail"
                                :src="edu.logo.urls.thumbnail"
                                :alt="edu.institution"
                                class="h-full w-full object-contain p-1"
                            />
                            <GraduationCap v-else class="h-5 w-5 text-muted-foreground/60" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-foreground flex items-center gap-2">
                                {{ edu.institution }}
                                <Badge v-if="edu.gpa" variant="secondary" class="text-[10px] font-mono font-medium px-1.5 py-0">
                                    IPK: {{ edu.gpa }}
                                </Badge>
                            </p>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground mt-0.5">
                                <span class="font-medium text-foreground/80">
                                    {{ edu.degree ? `${edu.degree} - ` : '' }}{{ edu.major }}
                                </span>
                                <span v-if="edu.location" class="flex items-center gap-0.5">
                                    <MapPin class="h-3 w-3" />
                                    {{ edu.location }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Period -->
                    <div class="text-sm font-medium text-muted-foreground flex items-center gap-1.5">
                        <Calendar class="h-4 w-4 text-muted-foreground/60 shrink-0" />
                        <span>{{ formatPeriod(edu.start_date, edu.end_date) }}</span>
                    </div>

                    <!-- Order -->
                    <div class="text-center">
                        <span class="rounded-lg bg-muted/50 px-2.5 py-1 text-xs font-mono font-medium text-muted-foreground">
                            #{{ edu.order }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-1.5" @click.stop>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10"
                            title="Edit"
                            @click="openEdit(edu)"
                        >
                            <Edit2 class="h-4 w-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                            title="Hapus"
                            @click="openDelete(edu)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>
            <p v-if="eduList.length > 1 && !searchQuery" class="text-center text-xs text-muted-foreground/60">
                Seret baris untuk mengatur urutan kronologis/tampilan manual dari riwayat pendidikan Anda.
            </p>
        </div>
    </div>

    <!-- ================================================================
         SHEET FORM: EXPERIENCE
    ================================================================ -->
    <Sheet v-model:open="expSheetOpen">
        <SheetContent side="right" class="w-full sm:max-w-2xl overflow-y-auto flex flex-col gap-6 p-6">
            <!-- Header -->
            <SheetHeader class="border-b border-border/70 pb-4 text-left">
                <SheetTitle class="text-lg font-bold tracking-tight text-foreground">
                    {{ isEditing ? 'Edit Riwayat Pengalaman Kerja' : 'Tambah Pengalaman Kerja Baru' }}
                </SheetTitle>
                <SheetDescription class="text-xs text-muted-foreground">
                    Isi detail rekam jejak karir profesional Anda pada form di bawah.
                </SheetDescription>
            </SheetHeader>

            <!-- Form body -->
            <form @submit.prevent="submitExpForm" class="flex-1 space-y-5">
                <!-- Company -->
                <div class="grid gap-1.5">
                    <Label for="exp-company" class="text-xs font-semibold text-foreground">
                        Nama Perusahaan <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="exp-company"
                        v-model="expForm.company"
                        placeholder="Contoh: PT. Solusi Digital Indonesia"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="expForm.errors.company" />
                </div>

                <!-- Title/Position -->
                <div class="grid gap-1.5">
                    <Label for="exp-title" class="text-xs font-semibold text-foreground">
                        Jabatan / Posisi <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="exp-title"
                        v-model="expForm.title_position"
                        placeholder="Contoh: Full-Stack Developer"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="expForm.errors.title_position" />
                </div>

                <!-- Location -->
                <div class="grid gap-1.5">
                    <Label for="exp-location" class="text-xs font-semibold text-foreground">Lokasi</Label>
                    <Input
                        id="exp-location"
                        v-model="expForm.location"
                        placeholder="Contoh: Jakarta (Remote) atau Bandung (Hybrid)"
                        class="h-9 text-xs bg-card border-border/80"
                    />
                    <InputError :message="expForm.errors.location" />
                </div>

                <!-- Period (Start & End dates) -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div class="grid gap-1.5">
                        <Label for="exp-start-date" class="text-xs font-semibold text-foreground">
                            Bulan Mulai <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="exp-start-date"
                            v-model="expForm.start_date"
                            type="month"
                            class="h-9 text-xs bg-card border-border/80"
                            required
                        />
                        <InputError :message="expForm.errors.start_date" />
                    </div>

                    <!-- End Date -->
                    <div class="grid gap-1.5">
                        <Label for="exp-end-date" class="text-xs font-semibold text-foreground flex items-center justify-between">
                            <span>Bulan Selesai</span>
                            <span class="text-[10px] text-muted-foreground font-normal">(Biarkan kosong jika masih bekerja)</span>
                        </Label>
                        <Input
                            id="exp-end-date"
                            v-model="expForm.end_date"
                            type="month"
                            class="h-9 text-xs bg-card border-border/80"
                        />
                        <InputError :message="expForm.errors.end_date" />
                    </div>
                </div>

                <!-- Website URL -->
                <div class="grid gap-1.5">
                    <Label for="exp-url" class="text-xs font-semibold text-foreground">URL Website Perusahaan</Label>
                    <Input
                        id="exp-url"
                        v-model="expForm.website_url"
                        type="url"
                        placeholder="Contoh: https://solusidigital.id"
                        class="h-9 text-xs bg-card border-border/80"
                    />
                    <InputError :message="expForm.errors.website_url" />
                </div>

                <!-- Logo Picker -->
                <div class="grid gap-2 rounded-2xl border border-border/70 bg-card p-4 shadow-2xs">
                    <Label class="text-xs font-semibold text-foreground">Logo Perusahaan</Label>
                    <div class="flex items-center gap-3">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-border/80 bg-muted/20 overflow-hidden">
                            <img
                                v-if="selectedLogo?.urls?.thumbnail"
                                :src="selectedLogo.urls.thumbnail"
                                alt="Logo Perusahaan"
                                class="h-full w-full object-contain p-1"
                            />
                            <Briefcase v-else class="h-6 w-6 text-muted-foreground/50" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="font-semibold text-xs cursor-pointer border-border/80 h-8"
                                @click="openMediaPicker"
                            >
                                Pilih dari Media Library
                            </Button>
                            <Button
                                v-if="expForm.logo_media_id"
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="text-[11px] text-muted-foreground hover:text-destructive cursor-pointer w-fit p-1 h-6"
                                @click="removeLogo"
                            >
                                Hapus Logo
                            </Button>
                        </div>
                    </div>
                    <InputError :message="expForm.errors.logo_media_id" />
                </div>

                <!-- Order -->
                <div class="grid gap-1.5">
                    <Label for="exp-order" class="text-xs font-semibold text-foreground">Urutan Urutan Tampil</Label>
                    <Input
                        id="exp-order"
                        v-model.number="expForm.order"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="expForm.errors.order" />
                </div>

                <!-- Description -->
                <div class="grid gap-1.5">
                    <Label class="text-xs font-semibold text-foreground">Kontribusi & Deskripsi Kerja</Label>
                    <CKEditor
                        v-model="expForm.description"
                        placeholder="Tuliskan kontribusi Anda, peran, serta projek yang dikerjakan di perusahaan ini..."
                    />
                    <InputError :message="expForm.errors.description" />
                </div>

                <!-- Footer buttons -->
                <div class="flex items-center justify-end gap-2.5 border-t border-border/70 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="expSheetOpen = false"
                        :disabled="expForm.processing"
                        class="h-9 text-xs px-4 cursor-pointer border-border/80"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        :disabled="expForm.processing"
                        class="h-9 text-xs px-5 bg-primary text-white hover:bg-primary/90 font-semibold cursor-pointer shadow-xs gap-1.5"
                    >
                        <Loader2 v-if="expForm.processing" class="h-3.5 w-3.5 animate-spin" />
                        <span>{{ isEditing ? 'Simpan Perubahan' : 'Simpan Pengalaman' }}</span>
                    </Button>
                </div>
            </form>
        </SheetContent>
    </Sheet>

    <!-- ================================================================
         SHEET FORM: EDUCATION
    ================================================================ -->
    <Sheet v-model:open="eduSheetOpen">
        <SheetContent side="right" class="w-full sm:max-w-2xl overflow-y-auto flex flex-col gap-6 p-6">
            <!-- Header -->
            <SheetHeader class="border-b border-border/70 pb-4 text-left">
                <SheetTitle class="text-lg font-bold tracking-tight text-foreground">
                    {{ isEditing ? 'Edit Riwayat Pendidikan' : 'Tambah Riwayat Pendidikan Baru' }}
                </SheetTitle>
                <SheetDescription class="text-xs text-muted-foreground">
                    Isi detail riwayat akademis pendidikan formal Anda pada form di bawah.
                </SheetDescription>
            </SheetHeader>

            <!-- Form body -->
            <form @submit.prevent="submitEduForm" class="flex-1 space-y-5">
                <!-- Institution -->
                <div class="grid gap-1.5">
                    <Label for="edu-inst" class="text-xs font-semibold text-foreground">
                        Nama Institusi / Universitas <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="edu-inst"
                        v-model="eduForm.institution"
                        placeholder="Contoh: Universitas Brawijaya"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="eduForm.errors.institution" />
                </div>

                <!-- Degree -->
                <div class="grid gap-1.5">
                    <Label for="edu-degree" class="text-xs font-semibold text-foreground">Gelar Akademis</Label>
                    <Input
                        id="edu-degree"
                        v-model="eduForm.degree"
                        placeholder="Contoh: S1, D3, SMK, atau Sertifikasi"
                        class="h-9 text-xs bg-card border-border/80"
                    />
                    <InputError :message="eduForm.errors.degree" />
                </div>

                <!-- Major -->
                <div class="grid gap-1.5">
                    <Label for="edu-major" class="text-xs font-semibold text-foreground">
                        Jurusan / Bidang Studi <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="edu-major"
                        v-model="eduForm.major"
                        placeholder="Contoh: Teknik Informatika"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="eduForm.errors.major" />
                </div>

                <!-- GPA -->
                <div class="grid gap-1.5">
                    <Label for="edu-gpa" class="text-xs font-semibold text-foreground">GPA / IPK / Nilai</Label>
                    <Input
                        id="edu-gpa"
                        v-model="eduForm.gpa"
                        placeholder="Contoh: 3.85 / 4.00"
                        class="h-9 text-xs bg-card border-border/80"
                    />
                    <InputError :message="eduForm.errors.gpa" />
                </div>

                <!-- Location -->
                <div class="grid gap-1.5">
                    <Label for="edu-location" class="text-xs font-semibold text-foreground">Lokasi</Label>
                    <Input
                        id="edu-location"
                        v-model="eduForm.location"
                        placeholder="Contoh: Malang, Jawa Timur"
                        class="h-9 text-xs bg-card border-border/80"
                    />
                    <InputError :message="eduForm.errors.location" />
                </div>

                <!-- Period (Start & End dates) -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div class="grid gap-1.5">
                        <Label for="edu-start-date" class="text-xs font-semibold text-foreground">
                            Bulan Mulai <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="edu-start-date"
                            v-model="eduForm.start_date"
                            type="month"
                            class="h-9 text-xs bg-card border-border/80"
                            required
                        />
                        <InputError :message="eduForm.errors.start_date" />
                    </div>

                    <!-- End Date -->
                    <div class="grid gap-1.5">
                        <Label for="edu-end-date" class="text-xs font-semibold text-foreground flex items-center justify-between">
                            <span>Bulan Kelulusan</span>
                            <span class="text-[10px] text-muted-foreground font-normal">(Kosongkan jika belum lulus)</span>
                        </Label>
                        <Input
                            id="edu-end-date"
                            v-model="eduForm.end_date"
                            type="month"
                            class="h-9 text-xs bg-card border-border/80"
                        />
                        <InputError :message="eduForm.errors.end_date" />
                    </div>
                </div>

                <!-- Logo Picker -->
                <div class="grid gap-2 rounded-2xl border border-border/70 bg-card p-4 shadow-2xs">
                    <Label class="text-xs font-semibold text-foreground">Logo Institusi</Label>
                    <div class="flex items-center gap-3">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-border/80 bg-muted/20 overflow-hidden">
                            <img
                                v-if="selectedLogo?.urls?.thumbnail"
                                :src="selectedLogo.urls.thumbnail"
                                alt="Logo Sekolah/Universitas"
                                class="h-full w-full object-contain p-1"
                            />
                            <GraduationCap v-else class="h-6 w-6 text-muted-foreground/50" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="font-semibold text-xs cursor-pointer border-border/80 h-8"
                                @click="openMediaPicker"
                            >
                                Pilih dari Media Library
                            </Button>
                            <Button
                                v-if="eduForm.logo_media_id"
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="text-[11px] text-muted-foreground hover:text-destructive cursor-pointer w-fit p-1 h-6"
                                @click="removeLogo"
                            >
                                Hapus Logo
                            </Button>
                        </div>
                    </div>
                    <InputError :message="eduForm.errors.logo_media_id" />
                </div>

                <!-- Order -->
                <div class="grid gap-1.5">
                    <Label for="edu-order" class="text-xs font-semibold text-foreground">Urutan Urutan Tampil</Label>
                    <Input
                        id="edu-order"
                        v-model.number="eduForm.order"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="h-9 text-xs bg-card border-border/80"
                        required
                    />
                    <InputError :message="eduForm.errors.order" />
                </div>

                <!-- Description -->
                <div class="grid gap-1.5">
                    <Label class="text-xs font-semibold text-foreground">Aktivitas & Pencapaian Akademis</Label>
                    <CKEditor
                        v-model="eduForm.description"
                        placeholder="Tuliskan organisasi yang diikuti, kejuaraan, riset tugas akhir, dll..."
                    />
                    <InputError :message="eduForm.errors.description" />
                </div>

                <!-- Footer buttons -->
                <div class="flex items-center justify-end gap-2.5 border-t border-border/70 pt-4">
                    <Button
                        type="button"
                        variant="outline"
                        @click="eduSheetOpen = false"
                        :disabled="eduForm.processing"
                        class="h-9 text-xs px-4 cursor-pointer border-border/80"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        :disabled="eduForm.processing"
                        class="h-9 text-xs px-5 bg-primary text-white hover:bg-primary/90 font-semibold cursor-pointer shadow-xs gap-1.5"
                    >
                        <Loader2 v-if="eduForm.processing" class="h-3.5 w-3.5 animate-spin" />
                        <span>{{ isEditing ? 'Simpan Perubahan' : 'Simpan Pendidikan' }}</span>
                    </Button>
                </div>
            </form>
        </SheetContent>
    </Sheet>

    <!-- ================================================================
         MEDIA LIBRARY PICKER MODAL
    ================================================================ -->
    <MediaLibraryModal
        v-model:open="mediaModalOpen"
        @select="handleLogoSelect"
    />

    <!-- ================================================================
         DELETE CONFIRMATION DIALOG
    ================================================================ -->
    <Dialog v-model:open="deleteDialogOpen">
        <DialogContent class="sm:max-w-md bg-card border-border">
            <DialogHeader>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-destructive/10 mb-2">
                    <AlertTriangle class="h-6 w-6 text-destructive" />
                </div>
                <DialogTitle class="text-lg font-bold">Hapus Data Riwayat?</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus entri riwayat
                    <strong>
                        {{ activeTab === 'experience' ? deletingItem?.company : deletingItem?.institution }}
                    </strong>? Tindakan ini permanen dan tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="flex gap-2 sm:gap-0 mt-4">
                <Button variant="outline" @click="deleteDialogOpen = false">
                    Batal
                </Button>
                <Button variant="destructive" @click="confirmDelete">
                    Ya, Hapus
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
