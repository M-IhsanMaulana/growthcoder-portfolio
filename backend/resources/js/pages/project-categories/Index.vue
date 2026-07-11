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
import InputError from '@/components/InputError.vue';
import {
    Plus,
    Folder,
    AlertTriangle,
    ArrowUp,
    ArrowDown,
    Trash2,
    Edit2,
    Search,
    X,
    FolderOpen,
    Code,
    Smartphone,
    Bot,
    Server,
    Globe,
    Cpu,
    Database,
    Terminal,
    Briefcase,
    Layers,
    Settings,
    AppWindow,
    FileJson,
    Cloud,
    Wrench,
    Sparkles,
    BookOpen,
    User,
    Mail,
    Tv,
    Layout,
    Link,
    Tag,
    List,
    Workflow,
    Binary,
    Braces
} from '@lucide/vue';
import * as LucideIcons from '@lucide/vue';
import { index as projectCategoriesIndex } from '@/routes/project-categories';
import { getLucideSvgString, isSvgString } from '@/utils/icon';

const props = defineProps<{
    categories: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Project Categories',
                href: projectCategoriesIndex(),
            },
        ],
    },
});

// Map string names to Lucide Icon components
const availableIcons: Record<string, any> = {
    Code,
    Smartphone,
    Bot,
    Server,
    Globe,
    Cpu,
    Database,
    Terminal,
    Briefcase,
    Layers,
    Settings,
    AppWindow,
    FileJson,
    Cloud,
    Wrench,
    Sparkles,
    BookOpen,
    User,
    Mail,
    Folder,
    FolderOpen,
    Tv,
    Layout,
    Link,
    Tag,
    List,
    Workflow,
    Binary,
    Braces
};

// Search / Filtering State
const listSearch = ref('');
const filteredCategories = computed(() => {
    if (!listSearch.value) return props.categories;
    const q = listSearch.value.toLowerCase();
    return props.categories.filter(c =>
        c.name.toLowerCase().includes(q) ||
        (c.description && c.description.toLowerCase().includes(q))
    );
});

// Form and Sheet State
const sheetOpen = ref(false);
const isEditing = ref(false);
const editingCategoryId = ref<number | null>(null);
const isSlugManuallyEdited = ref(false);

const form = useForm({
    name: '',
    slug: '',
    description: '',
    icon: '',
    order: 0
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

// Open Create Drawer
const openCreate = () => {
    isEditing.value = false;
    editingCategoryId.value = null;
    form.reset();
    form.clearErrors();
    isSlugManuallyEdited.value = false;
    activeIconTab.value = 'picker';
    sheetOpen.value = true;
};

// Open Edit Drawer
const openEdit = (category: any) => {
    isEditing.value = true;
    editingCategoryId.value = category.id;
    form.name = category.name;
    form.slug = category.slug;
    form.description = category.description || '';
    form.icon = category.icon || '';
    form.order = category.order;
    form.clearErrors();
    isSlugManuallyEdited.value = true;
    activeIconTab.value = isSvgString(category.icon) ? 'custom' : 'picker';
    sheetOpen.value = true;
};

// Submit form
const submit = () => {
    if (isEditing.value && editingCategoryId.value) {
        form.put(`/admin-cms/project-categories/${editingCategoryId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/admin-cms/project-categories', {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    }
};

// Display Order Management
const moveCategory = (id: number, direction: 'up' | 'down') => {
    router.post(`/admin-cms/project-categories/${id}/move`, {
        direction: direction
    }, {
        preserveScroll: true
    });
};

const isFirst = (category: any) => {
    return props.categories.findIndex(c => c.id === category.id) === 0;
};

const isLast = (category: any) => {
    return props.categories.findIndex(c => c.id === category.id) === props.categories.length - 1;
};

// Visual Icon Picker Dialog State
const iconPickerOpen = ref(false);
const iconSearch = ref('');
const activeIconTab = ref<'picker' | 'custom'>('picker');
const filteredIcons = computed(() => {
    const keys = Object.keys(availableIcons);
    if (!iconSearch.value) return keys;
    const q = iconSearch.value.toLowerCase();
    return keys.filter(k => k.toLowerCase().includes(q));
});

const selectIcon = (iconName: string) => {
    form.icon = getLucideSvgString(iconName);
    iconPickerOpen.value = false;
};

// Delete Dialog State
const deleteDialogOpen = ref(false);
const categoryToDelete = ref<any | null>(null);

const confirmDelete = (category: any) => {
    categoryToDelete.value = category;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!categoryToDelete.value) return;
    router.delete(`/admin-cms/project-categories/${categoryToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            categoryToDelete.value = null;
        }
    });
};

// Computed warning for modified slug
const showSlugWarning = computed(() => {
    if (!isEditing.value || !editingCategoryId.value) return false;
    const original = props.categories.find(c => c.id === editingCategoryId.value);
    return original && form.slug !== original.slug;
});
</script>

<template>
    <Head title="Kategori Proyek" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Kategori Proyek</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola pengelompokan portofolio studi kasus proyek Anda secara dinamis.
                </p>
            </div>
            <div>
                <Button @click="openCreate" class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Kategori
                </Button>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex items-center justify-between">
            <div class="relative max-w-sm flex-1">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="listSearch"
                    placeholder="Cari kategori berdasarkan nama..."
                    class="pl-9"
                />
            </div>
        </div>

        <!-- Table List -->
        <div v-if="filteredCategories.length > 0" class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-sidebar-border/70 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 w-[100px]">Urutan</th>
                            <th class="p-4 w-[80px] text-center">Ikon</th>
                            <th class="p-4">Nama</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4 w-[150px] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border/50 text-sm">
                        <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-muted/15 transition-colors duration-150">
                            <!-- Order Column with Up/Down buttons -->
                            <td class="p-4 font-medium text-foreground">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-6 text-center font-semibold text-neutral-600 dark:text-neutral-400">
                                        {{ category.order }}
                                    </span>
                                    <div class="flex flex-col">
                                        <button
                                            type="button"
                                            :disabled="isFirst(category)"
                                            @click="moveCategory(category.id, 'up')"
                                            class="p-0.5 text-muted-foreground hover:text-primary disabled:opacity-30 disabled:hover:text-muted-foreground transition-colors cursor-pointer disabled:cursor-not-allowed"
                                            title="Pindahkan Ke Atas"
                                        >
                                            <ArrowUp class="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="isLast(category)"
                                            @click="moveCategory(category.id, 'down')"
                                            class="p-0.5 text-muted-foreground hover:text-primary disabled:opacity-30 disabled:hover:text-muted-foreground transition-colors cursor-pointer disabled:cursor-not-allowed"
                                            title="Pindahkan Ke Bawah"
                                        >
                                            <ArrowDown class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </td>

                            <!-- Icon Column -->
                            <td class="p-4 text-center">
                                <div class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300">
                                    <div v-if="isSvgString(category.icon)" v-html="category.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-current" />
                                    <component :is="availableIcons[category.icon]" v-else-if="category.icon && availableIcons[category.icon]" class="h-5 w-5" />
                                    <Folder v-else class="h-5 w-5 text-neutral-400" />
                                </div>
                            </td>

                            <!-- Name -->
                            <td class="p-4 font-semibold text-foreground">
                                {{ category.name }}
                            </td>

                            <!-- Slug -->
                            <td class="p-4 text-muted-foreground font-mono text-xs">
                                {{ category.slug }}
                            </td>

                            <!-- Description -->
                            <td class="p-4 text-muted-foreground max-w-xs truncate" :title="category.description">
                                {{ category.description || '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-neutral-500 hover:text-primary hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                        @click="openEdit(category)"
                                        title="Edit Kategori"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-neutral-500 hover:text-destructive hover:bg-red-50 dark:hover:bg-red-950/20"
                                        @click="confirmDelete(category)"
                                        title="Hapus Kategori"
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
        <div v-else class="flex flex-col items-center justify-center p-12 text-center border border-border border-dashed rounded-2xl bg-card/50">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-100 dark:bg-neutral-800 text-neutral-500 mb-4">
                <Folder class="h-6 w-6" />
            </div>
            <h3 class="text-md font-semibold text-foreground">Tidak ada kategori ditemukan</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                {{ listSearch ? 'Tidak ada kategori yang cocok dengan pencarian Anda.' : 'Silakan tambahkan kategori proyek baru untuk memulai.' }}
            </p>
            <Button v-if="!listSearch" @click="openCreate" class="mt-4 bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Kategori
            </Button>
        </div>

        <!-- Slide-over Sheet Form -->
        <Sheet v-slot="{ close }" v-model:open="sheetOpen">
            <SheetContent class="w-full sm:max-w-md md:max-w-lg overflow-y-auto flex flex-col h-full bg-card border-border p-6">
                <SheetHeader>
                    <SheetTitle class="text-lg font-bold">
                        {{ isEditing ? 'Edit Kategori Proyek' : 'Tambah Kategori Proyek Baru' }}
                    </SheetTitle>
                    <SheetDescription>
                        {{ isEditing ? 'Perbarui informasi kategori proyek Anda di bawah ini.' : 'Buat kategori baru untuk mengelompokkan proyek portofolio Anda.' }}
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submit" class="flex-1 flex flex-col justify-between gap-6 py-4">
                    <div class="space-y-5">
                        <!-- Name Field -->
                        <div class="grid gap-2">
                            <Label for="name" class="font-semibold text-sm">Nama Kategori <span class="text-red-500">*</span></Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                required
                                placeholder="Contoh: Web Application, Telegram Bot"
                                class="w-full"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Slug Field -->
                        <div class="grid gap-2">
                            <Label for="slug" class="font-semibold text-sm">Slug Kategori <span class="text-red-500">*</span></Label>
                            <Input
                                id="slug"
                                v-model="form.slug"
                                required
                                @input="isSlugManuallyEdited = true"
                                placeholder="Contoh: web-application"
                                class="w-full font-mono text-xs"
                            />
                            <InputError :message="form.errors.slug" />
                            <!-- Warning Warning manual slug -->
                            <div v-if="showSlugWarning" class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-lg text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2 mt-1">
                                <AlertTriangle class="h-4 w-4 shrink-0 mt-0.5" />
                                <div>
                                    Mengubah slug akan memengaruhi URL publik kategori ini. Pastikan redirect sudah dikonfigurasi.
                                </div>
                            </div>
                        </div>

                        <!-- Icon Field -->
                        <div class="grid gap-2">
                            <Label class="font-semibold text-sm">Ikon Kategori</Label>
                            
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
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-border bg-neutral-50 dark:bg-neutral-900 text-neutral-600 dark:text-neutral-300">
                                    <div v-if="isSvgString(form.icon)" v-html="form.icon" class="h-5 w-5 flex items-center justify-center [&_svg]:h-5 [&_svg]:w-5 [&_svg]:text-primary" />
                                    <component :is="availableIcons[form.icon]" v-else-if="form.icon && availableIcons[form.icon]" class="h-5 w-5 text-primary" />
                                    <Folder v-else class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <Button type="button" variant="outline" class="font-medium text-xs" @click="iconPickerOpen = true">
                                    Pilih Ikon...
                                </Button>
                                <Button v-if="form.icon" type="button" variant="ghost" size="icon" class="text-muted-foreground hover:text-destructive h-8 w-8" @click="form.icon = ''">
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>

                            <div v-else class="space-y-2">
                                <textarea
                                    v-model="form.icon"
                                    rows="4"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs font-mono shadow-sm placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
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
                                        @click="form.icon = ''"
                                    >
                                        Hapus
                                    </Button>
                                </div>
                            </div>
                            <InputError :message="form.errors.icon" />
                        </div>

                        <!-- Order Field -->
                        <div class="grid gap-2">
                            <Label for="order" class="font-semibold text-sm">Urutan Tampilan</Label>
                            <Input
                                id="order"
                                type="number"
                                v-model.number="form.order"
                                min="0"
                                required
                                class="w-full"
                            />
                            <p class="text-[11px] text-muted-foreground">Urutan tampilan pada filter kategori di halaman publik (mengecil ke membesar).</p>
                            <InputError :message="form.errors.order" />
                        </div>

                        <!-- Description Field -->
                        <div class="grid gap-2">
                            <Label for="description" class="font-semibold text-sm">Deskripsi Kategori</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                placeholder="Jelaskan deskripsi singkat kategori ini..."
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            ></textarea>
                            <InputError :message="form.errors.description" />
                        </div>
                    </div>

                    <!-- Footer actions inside form -->
                    <SheetFooter class="flex items-center gap-2 border-t border-border pt-4 sm:space-x-0 mt-auto">
                        <Button type="button" variant="outline" class="w-full sm:w-auto" @click="sheetOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="w-full sm:w-auto bg-primary text-white font-medium">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                        </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Searchable Icon Picker Dialog -->
        <Dialog v-model:open="iconPickerOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle>Pilih Ikon Kategori</DialogTitle>
                    <DialogDescription>
                        Cari dan pilih ikon yang mewakili kategori ini di halaman publik.
                    </DialogDescription>
                </DialogHeader>

                <div class="relative mt-2">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="iconSearch"
                        placeholder="Cari ikon (misal: Code, Server, Bot...)"
                        class="pl-9"
                    />
                </div>

                <div class="grid grid-cols-5 gap-3 max-h-[300px] overflow-y-auto p-1 mt-2">
                    <button
                        v-for="iconName in filteredIcons"
                        :key="iconName"
                        type="button"
                        @click="selectIcon(iconName)"
                        class="flex flex-col items-center justify-center p-3 rounded-lg border border-border bg-muted/30 hover:bg-primary/10 hover:border-primary/50 hover:text-primary transition-all duration-150 gap-2 text-neutral-600 dark:text-neutral-300 cursor-pointer"
                        :class="{ 'border-primary bg-primary/10 text-primary ring-2 ring-primary/20': form.icon === iconName }"
                    >
                        <component :is="availableIcons[iconName]" class="h-6 w-6" />
                        <span class="text-[10px] truncate max-w-full text-center font-medium">{{ iconName }}</span>
                    </button>
                    <div v-if="filteredIcons.length === 0" class="col-span-5 text-center p-4 text-xs text-muted-foreground">
                        Tidak ada ikon yang cocok.
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="iconPickerOpen = false">Batal</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="text-destructive flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5" />
                        Hapus Kategori Proyek
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori <strong>{{ categoryToDelete?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="flex gap-2 sm:gap-0">
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
