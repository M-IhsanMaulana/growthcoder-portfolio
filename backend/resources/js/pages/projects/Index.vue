<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
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
import {
    Plus,
    Folder,
    AlertTriangle,
    Trash2,
    Edit2,
    Search,
    GripVertical,
    Star,
    ExternalLink,
    Filter
} from '@lucide/vue';
import { index as projectsIndex, create as projectsCreate, edit as projectsEdit } from '@/routes/projects';

const props = defineProps<{
    projects: any[];
    categories: any[];
    filters: {
        q?: string;
        category_id?: string;
        status?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Proyek',
                href: projectsIndex(),
            },
        ],
    },
});

// Local reactive copy of projects for reordering
const items = ref([...props.projects]);

watch(() => props.projects, (newVal) => {
    items.value = [...newVal];
});

// Search and Filter States
const searchFilter = ref('');
const categoryFilter = ref('all');
const statusFilter = ref('all');

// Check if any filter is active
const isFilterActive = computed(() => {
    return searchFilter.value !== '' || categoryFilter.value !== 'all' || statusFilter.value !== 'all';
});

// Filtered projects computed
const filteredProjects = computed(() => {
    let result = [...items.value];

    if (searchFilter.value) {
        const q = searchFilter.value.toLowerCase();
        result = result.filter(p => 
            p.title.toLowerCase().includes(q) || 
            (p.short_description && p.short_description.toLowerCase().includes(q))
        );
    }

    if (categoryFilter.value !== 'all') {
        const catId = Number(categoryFilter.value);
        result = result.filter(p => p.category_id === catId);
    }

    if (statusFilter.value !== 'all') {
        result = result.filter(p => p.status === statusFilter.value);
    }

    return result;
});

// Drag and Drop State
const draggedIndex = ref<number | null>(null);
const dragOverIndex = ref<number | null>(null);

const onDragStart = (index: number, e: DragEvent) => {
    if (isFilterActive.value) return; // Disable dragging when filtered
    
    draggedIndex.value = index;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', index.toString());
    }
};

const onDragOver = (index: number, e: DragEvent) => {
    if (isFilterActive.value || draggedIndex.value === null) return;
    
    // Determine drag direction and target to show indicator
    dragOverIndex.value = index;
};

const onDragEnd = () => {
    if (isFilterActive.value) return;

    if (draggedIndex.value !== null && dragOverIndex.value !== null && draggedIndex.value !== dragOverIndex.value) {
        // Perform local reorder
        const draggedItem = items.value[draggedIndex.value];
        
        // Remove from old position
        items.value.splice(draggedIndex.value, 1);
        // Insert into new position
        items.value.splice(dragOverIndex.value, 0, draggedItem);

        // Update database
        const orderedIds = items.value.map(item => item.id);
        router.post('/admin-cms/projects/reorder', {
            ids: orderedIds
        }, {
            preserveScroll: true
        });
    }

    draggedIndex.value = null;
    dragOverIndex.value = null;
};

// Reset Filters
const resetFilters = () => {
    searchFilter.value = '';
    categoryFilter.value = 'all';
    statusFilter.value = 'all';
};

// Delete Dialog State
const deleteDialogOpen = ref(false);
const projectToDelete = ref<any | null>(null);

const confirmDelete = (project: any) => {
    projectToDelete.value = project;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!projectToDelete.value) return;
    router.delete(`/admin-cms/projects/${projectToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            projectToDelete.value = null;
        }
    });
};

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Manajemen Proyek & Studi Kasus" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-sidebar-border/70 pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Proyek & Studi Kasus</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola dan publikasikan portofolio studi kasus naratif proyek Anda.
                </p>
            </div>
            <div>
                <Link :href="projectsCreate()">
                    <Button class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Proyek Baru
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Controls / Search & Filters -->
        <div class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between bg-neutral-50/50 dark:bg-neutral-900/30 p-3 rounded-xl border border-sidebar-border/50">
            <!-- Search field -->
            <div class="relative flex-1 max-w-md">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchFilter"
                    placeholder="Cari proyek berdasarkan judul..."
                    class="pl-9 bg-card"
                />
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Category Filter -->
                <div class="flex items-center gap-1.5">
                    <Filter class="h-3.5 w-3.5 text-muted-foreground" />
                    <select
                        v-model="categoryFilter"
                        class="text-xs rounded-lg border border-input bg-card px-2.5 py-1.5 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        <option value="all">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <select
                    v-model="statusFilter"
                    class="text-xs rounded-lg border border-input bg-card px-2.5 py-1.5 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                >
                    <option value="all">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>

                <Button
                    v-if="isFilterActive"
                    variant="ghost"
                    size="sm"
                    class="text-xs text-muted-foreground hover:text-primary cursor-pointer h-8 px-2"
                    @click="resetFilters"
                >
                    Reset
                </Button>
            </div>
        </div>

        <!-- Warning Drag & Drop disabled when filtered -->
        <div v-if="isFilterActive" class="p-3 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/40 rounded-lg text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2">
            <AlertTriangle class="h-4 w-4 shrink-0" />
            <span>Fungsi drag-and-drop untuk mengurutkan proyek dinonaktifkan sementara filter pencarian aktif.</span>
        </div>

        <!-- Table List -->
        <div v-if="filteredProjects.length > 0" class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sidebar-border/70 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 w-[60px] text-center" v-if="!isFilterActive">Urut</th>
                            <th class="p-4 w-[100px]">Cover</th>
                            <th class="p-4">Proyek</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Unggulan</th>
                            <th class="p-4">Tanggal Dibuat</th>
                            <th class="p-4 w-[120px] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border/50 text-sm">
                        <tr
                            v-for="(project, index) in filteredProjects"
                            :key="project.id"
                            :draggable="!isFilterActive"
                            @dragstart="onDragStart(index, $event)"
                            @dragover.prevent="onDragOver(index, $event)"
                            @dragend="onDragEnd"
                            class="hover:bg-muted/15 transition-all duration-150 select-none border-l-2"
                            :class="{
                                'opacity-40 bg-muted/20': index === draggedIndex,
                                'border-l-primary bg-primary/5': index === dragOverIndex && index !== draggedIndex,
                                'border-l-transparent': index !== dragOverIndex || index === draggedIndex
                            }"
                        >
                            <!-- Grab handle column -->
                            <td class="p-4 text-center" v-if="!isFilterActive">
                                <div class="flex items-center justify-center text-muted-foreground/60 hover:text-foreground cursor-grab active:cursor-grabbing p-1 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800">
                                    <GripVertical class="h-4 w-4" />
                                </div>
                            </td>

                            <!-- Cover Image -->
                            <td class="p-4">
                                <div class="h-12 w-20 rounded-lg overflow-hidden border border-sidebar-border bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                    <img
                                        v-if="project.cover_image"
                                        :src="project.cover_image.urls.thumbnail"
                                        :alt="project.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Folder v-else class="h-5 w-5 text-neutral-400" />
                                </div>
                            </td>

                            <!-- Title / Slug -->
                            <td class="p-4">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-foreground leading-tight hover:text-primary transition-colors">
                                        {{ project.title }}
                                    </span>
                                    <span class="text-[11px] text-muted-foreground font-mono mt-0.5 max-w-xs truncate">
                                        {{ project.slug }}
                                    </span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="p-4 text-muted-foreground font-medium">
                                {{ project.category ? project.category.name : '-' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="p-4 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                    :class="{
                                        'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300': project.status === 'draft',
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-900/30': project.status === 'published'
                                    }"
                                >
                                    {{ project.status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                            </td>

                            <!-- Featured Star -->
                            <td class="p-4 text-center">
                                <div class="flex justify-center">
                                    <Star
                                        class="h-4 w-4"
                                        :class="{
                                            'text-amber-500 fill-amber-500': project.is_featured,
                                            'text-neutral-300 dark:text-neutral-700': !project.is_featured
                                        }"
                                    />
                                </div>
                            </td>

                            <!-- Created Date -->
                            <td class="p-4 text-muted-foreground text-xs font-medium">
                                {{ formatDate(project.created_at) }}
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Link :href="projectsEdit({ project: project.id })">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-8 w-8 text-neutral-500 hover:text-primary hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                                            title="Edit Proyek"
                                        >
                                            <Edit2 class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-neutral-500 hover:text-destructive hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                                        @click="confirmDelete(project)"
                                        title="Hapus Proyek"
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

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center p-16 text-center border border-sidebar-border border-dashed rounded-2xl bg-card/50">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 mb-4">
                <Folder class="h-6 w-6" />
            </div>
            <h3 class="text-md font-semibold text-foreground">Tidak ada proyek ditemukan</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                {{ isFilterActive ? 'Tidak ada proyek yang cocok dengan filter pencarian Anda.' : 'Silakan tambahkan studi kasus proyek pertama Anda untuk memulai.' }}
            </p>
            <div class="mt-4 flex gap-2">
                <Button v-if="isFilterActive" @click="resetFilters" variant="outline" class="text-xs h-9 cursor-pointer">
                    Clear Filters
                </Button>
                <Link :href="projectsCreate()">
                    <Button class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 h-9 cursor-pointer">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Proyek Baru
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="text-destructive flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5" />
                        Hapus Studi Kasus Proyek
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus proyek <strong>{{ projectToDelete?.title }}</strong>? Tindakan ini akan menghapus semua relasi pivot terkait secara permanen. File gambar asli di Media Library tidak akan terhapus.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="flex gap-2 sm:gap-0 mt-4">
                    <Button variant="outline" @click="deleteDialogOpen = false" class="cursor-pointer">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="executeDelete" class="cursor-pointer bg-destructive hover:bg-destructive/90 text-destructive-foreground">
                        Ya, Hapus Permanen
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
