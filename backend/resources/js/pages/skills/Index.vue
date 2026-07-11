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
    Grid,
    List,
    Star,
    Edit2,
    Trash2,
    GripVertical,
    AlertTriangle,
    Award,
    Code,
    Server,
    Cpu,
    Database,
    Wrench,
    HelpCircle,
    Layers,
    BookOpen,
    FolderPlus
} from '@lucide/vue';
import { index as skillsIndex } from '@/routes/skills';

const props = defineProps<{
    skills: any[]; // The Groups
    technologies: any[]; // The Tech Stack Master
    filters: {
        q?: string;
        level?: string;
        featured?: boolean;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Skills Management',
                href: skillsIndex(),
            },
        ],
    },
});

// Levels definition
const levels = [
    { value: 'beginner', label: 'Beginner', color: 'bg-slate-500/10 text-slate-400 border-slate-500/20' },
    { value: 'intermediate', label: 'Intermediate', color: 'bg-blue-500/10 text-blue-400 border-blue-500/20' },
    { value: 'advanced', label: 'Advanced', color: 'bg-purple-500/10 text-purple-400 border-purple-500/20' },
    { value: 'expert', label: 'Expert', color: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' },
];

const getLevelLabel = (levelVal: string) => {
    return levels.find(l => l.value === levelVal)?.label || levelVal;
};

const getLevelColor = (levelVal: string) => {
    return levels.find(l => l.value === levelVal)?.color || 'bg-neutral-500/10 text-neutral-400 border-neutral-500/20';
};

const getLevelSegments = (levelVal: string) => {
    return {
        beginner: 1,
        intermediate: 2,
        advanced: 3,
        expert: 4
    }[levelVal] || 1;
};

const getLevelColorSegment = (levelVal: string) => {
    return {
        beginner: 'bg-slate-400 dark:bg-slate-500',
        intermediate: 'bg-blue-500 dark:bg-blue-400',
        advanced: 'bg-purple-500 dark:bg-purple-400',
        expert: 'bg-emerald-500 dark:bg-emerald-400'
    }[levelVal] || 'bg-slate-400';
};

// Tech category helpers for icons
const categories = [
    { value: 'frontend', label: 'Frontend', icon: Code },
    { value: 'backend', label: 'Backend', icon: Server },
    { value: 'devops', label: 'DevOps', icon: Cpu },
    { value: 'database', label: 'Database', icon: Database },
    { value: 'tools', label: 'Tools', icon: Wrench },
];

const getCategoryIcon = (catValue: string) => {
    return categories.find(c => c.value === catValue)?.icon || HelpCircle;
};

// Search & Filter local state
const listSearch = ref(props.filters.q || '');
const levelFilter = ref<string>(props.filters.level || 'all');
const featuredFilter = ref<boolean>(props.filters.featured || false);

const isFilterActive = computed(() => {
    return listSearch.value !== '' || levelFilter.value !== 'all' || featuredFilter.value;
});

// Skills local copy for reordering groups
const groupsList = ref([...props.skills]);
watch(() => props.skills, (newVal) => {
    groupsList.value = [...newVal];
});

// Drag & Drop for groups
const draggedGroupIndex = ref<number | null>(null);
const dragOverGroupIndex = ref<number | null>(null);

const onGroupDragStart = (index: number, e: DragEvent) => {
    if (isFilterActive.value) return;
    draggedGroupIndex.value = index;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', `group:${index}`);
    }
};

const onGroupDragOver = (index: number, e: DragEvent) => {
    if (isFilterActive.value || draggedGroupIndex.value === null) return;
    dragOverGroupIndex.value = index;
};

const onGroupDragEnd = () => {
    if (isFilterActive.value) return;
    if (draggedGroupIndex.value !== null && dragOverGroupIndex.value !== null && draggedGroupIndex.value !== dragOverGroupIndex.value) {
        const draggedItem = groupsList.value[draggedGroupIndex.value];
        groupsList.value.splice(draggedGroupIndex.value, 1);
        groupsList.value.splice(dragOverGroupIndex.value, 0, draggedItem);

        const orderedIds = groupsList.value.map(item => item.id);
        router.post('/admin-cms/skills/reorder', {
            ids: orderedIds
        }, {
            preserveScroll: true
        });
    }
    draggedGroupIndex.value = null;
    dragOverGroupIndex.value = null;
};

// Drag & Drop for items within a specific group
const draggedItemIndex = ref<number | null>(null);
const dragOverItemIndex = ref<number | null>(null);
const activeGroupDragId = ref<number | null>(null);

const onItemDragStart = (groupId: number, index: number, e: DragEvent) => {
    if (isFilterActive.value) return;
    draggedItemIndex.value = index;
    activeGroupDragId.value = groupId;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', `item:${groupId}:${index}`);
    }
};

const onItemDragOver = (groupId: number, index: number, e: DragEvent) => {
    if (isFilterActive.value || draggedItemIndex.value === null || activeGroupDragId.value !== groupId) return;
    dragOverItemIndex.value = index;
};

const onItemDragEnd = (groupId: number) => {
    if (isFilterActive.value || activeGroupDragId.value !== groupId) return;
    const group = groupsList.value.find(g => g.id === groupId);
    if (group && draggedItemIndex.value !== null && dragOverItemIndex.value !== null && draggedItemIndex.value !== dragOverItemIndex.value) {
        const items = [...group.items];
        const dragged = items[draggedItemIndex.value];
        items.splice(draggedItemIndex.value, 1);
        items.splice(dragOverItemIndex.value, 0, dragged);
        group.items = items; // local update

        const orderedIds = items.map(item => item.id);
        router.post('/admin-cms/skill-items/reorder', {
            ids: orderedIds
        }, {
            preserveScroll: true
        });
    }
    draggedItemIndex.value = null;
    dragOverItemIndex.value = null;
    activeGroupDragId.value = null;
};

// Computed for rendering filtered hierarchy
const filteredSkillsHierarchy = computed(() => {
    return groupsList.value.map(group => {
        let items = [...(group.items || [])];

        if (listSearch.value) {
            const q = listSearch.value.toLowerCase();
            items = items.filter(i => {
                const displayName = i.technology_id ? i.technology?.name : i.name;
                return displayName?.toLowerCase().includes(q);
            });
        }

        if (levelFilter.value !== 'all') {
            items = items.filter(i => i.level === levelFilter.value);
        }

        if (featuredFilter.value) {
            items = items.filter(i => i.is_featured);
        }

        return {
            ...group,
            filteredItems: items
        };
    }).filter(group => {
        // If filters are active, only show groups that have matching items
        if (isFilterActive.value) {
            return group.filteredItems.length > 0;
        }
        return true;
    });
});

// Group Modal State (Dialog)
const groupModalOpen = ref(false);
const isEditingGroup = ref(false);
const editingGroupId = ref<number | null>(null);

const groupForm = useForm({
    name: '',
    order: 0,
});

const openCreateGroup = () => {
    isEditingGroup.value = false;
    editingGroupId.value = null;
    groupForm.reset();
    groupForm.clearErrors();
    groupForm.order = props.skills.length > 0 ? Math.max(...props.skills.map(s => s.order)) + 1 : 0;
    groupModalOpen.value = true;
};

const openEditGroup = (group: any) => {
    isEditingGroup.value = true;
    editingGroupId.value = group.id;
    groupForm.name = group.name;
    groupForm.order = group.order;
    groupForm.clearErrors();
    groupModalOpen.value = true;
};

const submitGroup = () => {
    if (isEditingGroup.value && editingGroupId.value) {
        groupForm.put(`/admin-cms/skills/${editingGroupId.value}`, {
            onSuccess: () => {
                groupModalOpen.value = false;
                groupForm.reset();
            }
        });
    } else {
        groupForm.post('/admin-cms/skills', {
            onSuccess: () => {
                groupModalOpen.value = false;
                groupForm.reset();
            }
        });
    }
};

// Item Modal State (Sheet Drawer)
const itemSheetOpen = ref(false);
const isEditingItem = ref(false);
const editingItemId = ref<number | null>(null);
const itemType = ref<'tech' | 'custom'>('tech');

const itemForm = useForm({
    skill_id: '' as number | string,
    name: '',
    technology_id: '' as number | string,
    level: 'beginner',
    years_of_experience: null as number | null,
    is_featured: false,
    order: 0,
});

// Available technologies for selector (avoiding duplication)
const availableTechnologies = computed(() => {
    const assignedIds: number[] = [];
    props.skills.forEach(g => {
        (g.items || []).forEach((i: any) => {
            if (i.technology_id) {
                assignedIds.push(i.technology_id);
            }
        });
    });

    return props.technologies.filter(t => {
        if (isEditingItem.value && t.id === itemForm.technology_id) {
            return true;
        }
        return !assignedIds.includes(t.id);
    });
});

const openCreateItem = (groupId: number) => {
    isEditingItem.value = false;
    editingItemId.value = null;
    itemType.value = 'tech';
    itemForm.reset();
    itemForm.clearErrors();
    itemForm.skill_id = groupId;
    
    // Default order is max order in that group + 1
    const group = props.skills.find(g => g.id === groupId);
    itemForm.order = group && group.items?.length > 0 ? Math.max(...group.items.map((i: any) => i.order)) + 1 : 0;
    
    itemSheetOpen.value = true;
};

const openEditItem = (item: any) => {
    isEditingItem.value = true;
    editingItemId.value = item.id;
    itemType.value = item.technology_id ? 'tech' : 'custom';
    itemForm.skill_id = item.skill_id;
    itemForm.technology_id = item.technology_id || '';
    itemForm.name = item.name || '';
    itemForm.level = item.level;
    itemForm.years_of_experience = item.years_of_experience;
    itemForm.is_featured = item.is_featured;
    itemForm.order = item.order;
    itemForm.clearErrors();
    itemSheetOpen.value = true;
};

const submitItem = () => {
    // Clear unused fields based on selection
    if (itemType.value === 'tech') {
        itemForm.name = '';
    } else {
        itemForm.technology_id = '';
    }

    if (isEditingItem.value && editingItemId.value) {
        itemForm.put(`/admin-cms/skill-items/${editingItemId.value}`, {
            onSuccess: () => {
                itemSheetOpen.value = false;
                itemForm.reset();
            }
        });
    } else {
        itemForm.post('/admin-cms/skill-items', {
            onSuccess: () => {
                itemSheetOpen.value = false;
                itemForm.reset();
            }
        });
    }
};

// Delete Confirmation
const deleteDialogOpen = ref(false);
const deleteType = ref<'group' | 'item'>('group');
const targetToDelete = ref<any | null>(null);

const confirmDeleteGroup = (group: any) => {
    deleteType.value = 'group';
    targetToDelete.value = group;
    deleteDialogOpen.value = true;
};

const confirmDeleteItem = (item: any) => {
    deleteType.value = 'item';
    targetToDelete.value = item;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!targetToDelete.value) return;
    if (deleteType.value === 'group') {
        router.delete(`/admin-cms/skills/${targetToDelete.value.id}`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                targetToDelete.value = null;
            }
        });
    } else {
        router.delete(`/admin-cms/skill-items/${targetToDelete.value.id}`, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                targetToDelete.value = null;
            }
        });
    }
};

// Quick featured toggle for items
const toggleItemFeatured = (item: any) => {
    router.put(`/admin-cms/skill-items/${item.id}`, {
        skill_id: item.skill_id,
        technology_id: item.technology_id,
        name: item.name,
        level: item.level,
        years_of_experience: item.years_of_experience,
        is_featured: !item.is_featured,
        order: item.order
    }, {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Manajemen Kategori & Item Keahlian" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                    Manajemen Keahlian & Kategori
                </h1>
                <p class="text-sm text-muted-foreground">
                    Kelola kelompok/kategori skill (seperti Backend Development, Soft Skills) dan definisikan item-item keahlian di dalamnya.
                </p>
            </div>
            <div class="flex gap-2">
                <Button @click="openCreateGroup" variant="outline" class="font-semibold cursor-pointer">
                    <FolderPlus class="mr-2 h-4 w-4" />
                    Tambah Kategori/Grup
                </Button>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between bg-card p-4 rounded-xl border border-border/60 shadow-xs">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center flex-1">
                <!-- Search -->
                <div class="relative w-full sm:max-w-xs">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="listSearch"
                        placeholder="Cari item keahlian..."
                        class="pl-9"
                    />
                </div>

                <!-- Level Filters -->
                <div class="flex items-center gap-1.5">
                    <Award class="h-3.5 w-3.5 text-muted-foreground" />
                    <select
                        v-model="levelFilter"
                        class="text-xs rounded-lg border border-input bg-card px-2.5 py-1.5 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        <option value="all">Semua Level</option>
                        <option v-for="lvl in levels" :key="lvl.value" :value="lvl.value">
                            {{ lvl.label }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Featured Toggle -->
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-muted-foreground cursor-pointer select-none">
                    <input
                        type="checkbox"
                        v-model="featuredFilter"
                        class="rounded-sm border-input bg-card text-primary focus:ring-primary h-4 w-4 transition duration-150 cursor-pointer"
                    />
                    Featured Only
                </label>
            </div>
        </div>

        <!-- Warning Drag & Drop disabled when filtered -->
        <div v-if="isFilterActive" class="p-3 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/40 rounded-lg text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2">
            <AlertTriangle class="h-4 w-4 shrink-0" />
            <span>Fungsi drag-and-drop untuk mengurutkan dinonaktifkan sementara filter pencarian aktif.</span>
        </div>

        <!-- Hierarchical List -->
        <div v-if="filteredSkillsHierarchy.length > 0" class="space-y-6">
            <div
                v-for="(group, groupIdx) in filteredSkillsHierarchy"
                :key="group.id"
                :draggable="!isFilterActive"
                @dragstart="onGroupDragStart(groupIdx, $event)"
                @dragover.prevent="onGroupDragOver(groupIdx, $event)"
                @dragend="onGroupDragEnd"
                class="group/card relative rounded-xl border border-border/60 bg-card p-5 shadow-xs transition-all duration-200"
                :class="{
                    'opacity-40 bg-muted/20': groupIdx === draggedGroupIndex,
                    'border-t-2 border-t-primary': groupIdx === dragOverGroupIndex && groupIdx !== draggedGroupIndex
                }"
            >
                <!-- Group Card Header -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/40 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <!-- Group Drag handle -->
                        <div v-if="!isFilterActive" class="text-muted-foreground/40 hover:text-foreground cursor-grab active:cursor-grabbing p-1 rounded hover:bg-neutral-100 dark:hover:bg-neutral-800">
                            <GripVertical class="h-4.5 w-4.5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-foreground flex items-center gap-2">
                                {{ group.name }}
                                <Badge variant="outline" class="text-[10px] font-bold py-0.5 px-2 bg-muted/50 rounded-md">
                                    {{ group.items?.length || 0 }} Item
                                </Badge>
                            </h2>
                        </div>
                    </div>
                    
                    <!-- Group Action buttons -->
                    <div class="flex items-center gap-2">
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 text-muted-foreground hover:text-foreground cursor-pointer"
                            @click="openEditGroup(group)"
                        >
                            <Edit2 class="h-3.5 w-3.5 mr-1.5" />
                            Rename
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 text-muted-foreground hover:text-destructive cursor-pointer"
                            @click="confirmDeleteGroup(group)"
                        >
                            <Trash2 class="h-3.5 w-3.5 mr-1.5" />
                            Hapus
                        </Button>
                        <div class="h-4 w-px bg-border/80"></div>
                        <Button
                            size="sm"
                            class="h-8 bg-primary hover:bg-primary/90 text-white font-medium cursor-pointer"
                            @click="openCreateItem(group.id)"
                        >
                            <Plus class="h-3.5 w-3.5 mr-1.5" />
                            Tambah Item
                        </Button>
                    </div>
                </div>

                <!-- Items nested inside Group -->
                <div v-if="group.filteredItems && group.filteredItems.length > 0" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-border/40 text-[11px] font-bold uppercase tracking-wider text-muted-foreground bg-muted/20">
                                <th class="p-3 w-[50px] text-center" v-if="!isFilterActive">Urut</th>
                                <th class="p-3 w-[70px] text-center">Logo</th>
                                <th class="p-3">Nama Skill</th>
                                <th class="p-3 w-[120px]">Tipe</th>
                                <th class="p-3">Tingkat Kemahiran</th>
                                <th class="p-3 text-center w-[80px]">Featured</th>
                                <th class="p-3 text-center w-[120px]">Pengalaman</th>
                                <th class="p-3 w-[100px] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/30 text-sm">
                            <tr
                                v-for="(item, itemIdx) in group.filteredItems"
                                :key="item.id"
                                :draggable="!isFilterActive"
                                @dragstart="onItemDragStart(group.id, itemIdx, $event)"
                                @dragover.prevent="onItemDragOver(group.id, itemIdx, $event)"
                                @dragend="onItemDragEnd(group.id)"
                                class="hover:bg-muted/10 transition-colors duration-150 border-l-2"
                                :class="{
                                    'opacity-40 bg-muted/20': itemIdx === draggedItemIndex && activeGroupDragId === group.id,
                                    'border-l-primary bg-primary/5': itemIdx === dragOverItemIndex && draggedItemIndex !== itemIdx && activeGroupDragId === group.id,
                                    'border-l-transparent': itemIdx !== dragOverItemIndex || activeGroupDragId !== group.id
                                }"
                            >
                                <!-- Item drag handle -->
                                <td class="p-3 text-center" v-if="!isFilterActive">
                                    <div class="flex items-center justify-center text-muted-foreground/40 hover:text-foreground cursor-grab active:cursor-grabbing p-1 rounded">
                                        <GripVertical class="h-3.5 w-3.5" />
                                    </div>
                                </td>

                                <!-- Logo -->
                                <td class="p-3 text-center">
                                    <div class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-neutral-50 dark:bg-neutral-900 overflow-hidden">
                                        <img
                                            v-if="item.technology?.logo?.urls?.thumbnail"
                                            :src="item.technology.logo.urls.thumbnail"
                                            :alt="item.display_name"
                                            class="h-full w-full object-contain p-0.5"
                                        />
                                        <!-- Custom fallback award badge -->
                                        <Award v-else class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                </td>

                                <!-- Display Name -->
                                <td class="p-3 font-bold text-foreground">
                                    {{ item.display_name }}
                                </td>

                                <!-- Type -->
                                <td class="p-3">
                                    <Badge
                                        variant="outline"
                                        class="text-[9px] font-bold px-1.5 py-0 border uppercase rounded-xs"
                                        :class="item.technology_id
                                            ? 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                            : 'bg-amber-500/10 text-amber-400 border-amber-500/20'"
                                    >
                                        {{ item.technology_id ? 'Tech Stack' : 'Custom Text' }}
                                    </Badge>
                                </td>

                                <!-- Levels segments -->
                                <td class="p-3">
                                    <div class="flex flex-col gap-1 max-w-[130px]">
                                        <div class="flex items-center justify-between text-[10px] font-semibold text-foreground">
                                            <Badge variant="outline" class="font-bold text-[8px] px-1 py-0" :class="getLevelColor(item.level)">
                                                {{ getLevelLabel(item.level) }}
                                            </Badge>
                                        </div>
                                        <div class="flex gap-0.5 bg-neutral-100/50 dark:bg-neutral-900/50 p-0.5 rounded-xs border border-border/30">
                                            <div
                                                v-for="i in 4"
                                                :key="i"
                                                class="h-1 flex-1 rounded-xs"
                                                :class="[
                                                    i <= getLevelSegments(item.level)
                                                        ? getLevelColorSegment(item.level)
                                                        : 'bg-neutral-200 dark:bg-neutral-800'
                                                ]"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Featured Status -->
                                <td class="p-3 text-center">
                                    <button
                                        type="button"
                                        @click="toggleItemFeatured(item)"
                                        class="p-1 rounded-md hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-400 hover:text-amber-500 cursor-pointer transition-colors"
                                    >
                                        <Star class="h-4 w-4" :class="{ 'fill-amber-400 text-amber-400': item.is_featured }" />
                                    </button>
                                </td>

                                <!-- Years of experience -->
                                <td class="p-3 text-center text-foreground font-semibold font-mono text-xs">
                                    {{ item.years_of_experience ? `${item.years_of_experience} Tahun` : '-' }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-7 w-7 text-neutral-500 hover:text-primary hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                                            @click="openEditItem(item)"
                                            title="Edit Item"
                                        >
                                            <Edit2 class="h-3.5 w-3.5" />
                                        </Button>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="h-7 w-7 text-neutral-500 hover:text-destructive hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
                                            @click="confirmDeleteItem(item)"
                                            title="Hapus Item"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Empty Group items -->
                <div v-else class="text-center py-6 border border-dashed border-border/50 rounded-lg bg-muted/10">
                    <p class="text-xs text-muted-foreground">Kategori ini belum memiliki item keahlian.</p>
                    <Button
                        size="sm"
                        variant="link"
                        class="text-xs font-semibold text-primary mt-1 cursor-pointer"
                        @click="openCreateItem(group.id)"
                    >
                        Tambah Item Pertama
                    </Button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center p-12 text-center border border-border border-dashed rounded-2xl bg-card/50">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 mb-4">
                <Award class="h-6 w-6" />
            </div>
            <h3 class="text-md font-semibold text-foreground">Tidak ada kategori skill ditemukan</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                {{ listSearch ? 'Tidak ada item yang cocok dengan pencarian Anda.' : 'Silakan tambahkan data kelompok/kategori skill baru (misal: Backend Development).' }}
            </p>
            <Button v-if="!listSearch" @click="openCreateGroup" class="mt-4 bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Kategori Pertama
            </Button>
        </div>

        <!-- Group Modal Dialog (Add / Edit group) -->
        <Dialog v-model:open="groupModalOpen">
            <DialogContent class="sm:max-w-md bg-card border-border p-6">
                <DialogHeader>
                    <DialogTitle class="text-lg font-bold">
                        {{ isEditingGroup ? 'Edit Nama Kategori/Grup' : 'Tambah Kategori/Grup Baru' }}
                    </DialogTitle>
                    <DialogDescription>
                        Kategori ini akan mengelompokkan item keahlian (misal: Frontend, Soft Skills) di halaman publik.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitGroup" class="space-y-4 pt-4">
                    <div class="grid gap-2">
                        <Label for="group_name" class="font-semibold text-sm">Nama Kategori <span class="text-red-500">*</span></Label>
                        <Input
                            id="group_name"
                            v-model="groupForm.name"
                            required
                            placeholder="Contoh: Backend Development, Soft Skills"
                            class="w-full"
                        />
                        <InputError :message="groupForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="group_order" class="font-semibold text-sm">Urutan Tampil <span class="text-red-500">*</span></Label>
                        <Input
                            id="group_order"
                            type="number"
                            v-model="groupForm.order"
                            required
                            placeholder="Urutan (misal: 0, 1, 2)"
                            class="w-full"
                        />
                        <InputError :message="groupForm.errors.order" />
                    </div>

                    <DialogFooter class="flex sm:justify-end gap-2 border-t pt-4 border-border mt-4">
                        <Button
                            type="button"
                            variant="outline"
                            class="cursor-pointer font-semibold"
                            @click="groupModalOpen = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            class="bg-primary hover:bg-primary/90 text-white font-semibold cursor-pointer shadow-sm"
                            :disabled="groupForm.processing"
                        >
                            {{ groupForm.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Item Slide-over Form Sheet (Add / Edit item) -->
        <Sheet v-model:open="itemSheetOpen">
            <SheetContent class="w-full sm:max-w-md md:max-w-lg overflow-y-auto flex flex-col h-full bg-card border-border p-6">
                <SheetHeader>
                    <SheetTitle class="text-lg font-bold">
                        {{ isEditingItem ? 'Edit Item Keahlian' : 'Tambah Item Keahlian Baru' }}
                    </SheetTitle>
                    <SheetDescription>
                        Tambahkan keahlian teknis (tech stack) atau keahlian non-teknis (soft skill/bahasa) ke dalam kategori yang dipilih.
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submitItem" class="flex-1 flex flex-col justify-between gap-6 py-4">
                    <div class="space-y-5">
                        <!-- Group Category Selector (ReadOnly representation if preselected) -->
                        <div class="grid gap-2">
                            <Label class="font-semibold text-sm">Dimasukkan Ke Kategori</Label>
                            <select
                                v-model="itemForm.skill_id"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option v-for="g in skills" :key="g.id" :value="g.id">
                                    {{ g.name }}
                                </option>
                            </select>
                            <InputError :message="itemForm.errors.skill_id" />
                        </div>

                        <!-- Item Type Toggles -->
                        <div class="grid gap-2">
                            <Label class="font-semibold text-sm">Tipe Item Keahlian</Label>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    @click="itemType = 'tech'"
                                    class="flex-1 py-2 px-3 rounded-lg border text-xs font-semibold cursor-pointer transition-all duration-200"
                                    :class="itemType === 'tech' ? 'bg-primary text-white border-primary' : 'bg-card border-border text-muted-foreground hover:text-foreground'"
                                >
                                    Teknologi Master (Tech Stack)
                                </button>
                                <button
                                    type="button"
                                    @click="itemType = 'custom'"
                                    class="flex-1 py-2 px-3 rounded-lg border text-xs font-semibold cursor-pointer transition-all duration-200"
                                    :class="itemType === 'custom' ? 'bg-primary text-white border-primary' : 'bg-card border-border text-muted-foreground hover:text-foreground'"
                                >
                                    Teks Bebas / Soft Skill / Bahasa
                                </button>
                            </div>
                        </div>

                        <!-- Technology selector (if Type is tech) -->
                        <div v-if="itemType === 'tech'" class="grid gap-2">
                            <Label for="tech_id" class="font-semibold text-sm">Pilih Teknologi Master <span class="text-red-500">*</span></Label>
                            <select
                                id="tech_id"
                                v-model="itemForm.technology_id"
                                :required="itemType === 'tech'"
                                class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="" disabled>Pilih Teknologi</option>
                                <option v-for="tech in availableTechnologies" :key="tech.id" :value="tech.id">
                                    {{ tech.name }} ({{ tech.category }})
                                </option>
                            </select>
                            <InputError :message="itemForm.errors.technology_id" />
                            <p class="text-xs text-muted-foreground">
                                Mengambil logo & nama dari Master Teknologi. Teknologi yang sudah terdaftar di kategori lain tidak akan muncul.
                            </p>
                        </div>

                        <!-- Custom Name input (if Type is custom) -->
                        <div v-else class="grid gap-2">
                            <Label for="custom_name" class="font-semibold text-sm">Nama Skill / Keahlian <span class="text-red-500">*</span></Label>
                            <Input
                                id="custom_name"
                                v-model="itemForm.name"
                                :required="itemType === 'custom'"
                                placeholder="Contoh: Problem Solving, English Communication, Negosiasi"
                                class="w-full"
                            />
                            <InputError :message="itemForm.errors.name" />
                        </div>

                        <!-- Skill Level dropdown -->
                        <div class="grid gap-2">
                            <Label for="item_level" class="font-semibold text-sm">Tingkat Kemahiran <span class="text-red-500">*</span></Label>
                            <select
                                id="item_level"
                                v-model="itemForm.level"
                                required
                                class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option v-for="lvl in levels" :key="lvl.value" :value="lvl.value">
                                    {{ lvl.label }}
                                </option>
                            </select>
                            <InputError :message="itemForm.errors.level" />
                        </div>

                        <!-- Years of experience input -->
                        <div class="grid gap-2">
                            <Label for="item_years" class="font-semibold text-sm">Estimasi Pengalaman (Tahun)</Label>
                            <Input
                                id="item_years"
                                type="number"
                                step="0.1"
                                min="0"
                                max="99.9"
                                v-model="itemForm.years_of_experience"
                                placeholder="Contoh: 3.5 (kosongkan jika tidak relevan)"
                                class="w-full"
                            />
                            <InputError :message="itemForm.errors.years_of_experience" />
                        </div>

                        <!-- Manual Order input -->
                        <div class="grid gap-2">
                            <Label for="item_order" class="font-semibold text-sm">Nomor Urutan Tampil <span class="text-red-500">*</span></Label>
                            <Input
                                id="item_order"
                                type="number"
                                min="0"
                                v-model="itemForm.order"
                                required
                                placeholder="Urutan (misal: 0, 1, 2)"
                                class="w-full"
                            />
                            <InputError :message="itemForm.errors.order" />
                        </div>

                        <!-- Featured Toggle -->
                        <div class="flex items-center gap-2 pt-2">
                            <input
                                id="item_is_featured"
                                type="checkbox"
                                v-model="itemForm.is_featured"
                                class="rounded-sm border-input bg-card text-primary focus:ring-primary h-4 w-4 transition duration-150 cursor-pointer"
                            />
                            <Label for="item_is_featured" class="font-semibold text-sm cursor-pointer select-none">
                                Tampilkan sebagai Keahlian Utama (Featured)
                            </Label>
                            <InputError :message="itemForm.errors.is_featured" />
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t pt-4 border-border">
                        <Button
                            type="button"
                            variant="outline"
                            class="cursor-pointer font-semibold"
                            @click="itemSheetOpen = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            class="bg-primary hover:bg-primary/90 text-white font-semibold cursor-pointer shadow-sm transition-all"
                            :disabled="itemForm.processing"
                        >
                            {{ itemForm.processing ? 'Menyimpan...' : 'Simpan Item' }}
                        </Button>
                    </div>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Delete Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border-border p-6">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-destructive font-bold">
                        <AlertTriangle class="h-5 w-5" />
                        Konfirmasi Hapus {{ deleteType === 'group' ? 'Kategori' : 'Item' }}
                    </DialogTitle>
                    <DialogDescription class="pt-2 text-muted-foreground text-sm leading-relaxed">
                        <span v-if="deleteType === 'group'">
                            Apakah Anda yakin ingin menghapus kategori skill <strong class="text-foreground">{{ targetToDelete?.name }}</strong>?
                            Tindakan ini akan **menghapus semua item keahlian di dalamnya secara permanen**.
                        </span>
                        <span v-else>
                            Apakah Anda yakin ingin menghapus item keahlian <strong class="text-foreground">{{ targetToDelete?.display_name }}</strong>?
                            Tindakan ini bersifat permanen.
                        </span>
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="flex sm:justify-end gap-2 border-t pt-4 border-border mt-4">
                    <Button
                        type="button"
                        variant="outline"
                        class="cursor-pointer font-semibold"
                        @click="deleteDialogOpen = false"
                    >
                        Batal
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        class="bg-destructive hover:bg-destructive/90 text-white font-semibold cursor-pointer shadow-sm"
                        @click="executeDelete"
                    >
                        Hapus Permanen
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
