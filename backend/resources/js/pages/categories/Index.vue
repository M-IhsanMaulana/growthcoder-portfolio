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
    Trash2,
    Edit2,
    Search,
    X,
    BookOpen,
} from '@lucide/vue';
import { index as categoriesIndex } from '@/routes/categories';

const props = defineProps<{
    categories: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kategori Blog',
                href: categoriesIndex(),
            },
        ],
    },
});

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
    meta_title: '',
    meta_description: '',
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

const openCreateSheet = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    editingCategoryId.value = null;
    isSlugManuallyEdited.value = false;
    sheetOpen.value = true;
};

const openEditSheet = (category: any) => {
    form.clearErrors();
    isEditing.value = true;
    editingCategoryId.value = category.id;
    isSlugManuallyEdited.value = true;

    form.name = category.name;
    form.slug = category.slug;
    form.description = category.description || '';
    form.meta_title = category.meta_title || '';
    form.meta_description = category.meta_description || '';

    sheetOpen.value = true;
};

const submit = () => {
    if (isEditing.value && editingCategoryId.value) {
        form.put(`/admin-cms/categories/${editingCategoryId.value}`, {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/admin-cms/categories', {
            onSuccess: () => {
                sheetOpen.value = false;
                form.reset();
            }
        });
    }
};

// Delete Dialog State
const deleteDialogOpen = ref(false);
const categoryToDelete = ref<any | null>(null);
const errorMessage = ref<string | null>(null);

const confirmDelete = (category: any) => {
    categoryToDelete.value = category;
    errorMessage.value = null;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!categoryToDelete.value) return;

    router.delete(`/admin-cms/categories/${categoryToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            categoryToDelete.value = null;
        },
        onError: (errors) => {
            if (errors.error) {
                errorMessage.value = errors.error;
            }
        }
    });
};
</script>

<template>
    <Head title="Kategori Blog" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Kategori Blog</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola taksonomi kategori artikel blog untuk strategi internal linking dan struktur SEO.
                </p>
            </div>
            <div>
                <Button @click="openCreateSheet" class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer flex items-center gap-2">
                    <Plus class="h-4 w-4" />
                    Tambah Kategori
                </Button>
            </div>
        </div>

        <!-- Toolbar / Search -->
        <div class="flex items-center justify-between">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                    v-model="listSearch"
                    placeholder="Cari kategori..."
                    class="pl-9 bg-card w-full"
                />
                <button
                    v-if="listSearch"
                    @click="listSearch = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </div>

        <!-- Categories Table -->
        <div v-if="filteredCategories.length > 0" class="border border-border rounded-xl overflow-hidden bg-card/50 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-border bg-neutral-50/50 dark:bg-neutral-900/50 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                            <th class="p-4 w-12">#</th>
                            <th class="p-4">Nama Kategori</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4 text-center w-32">Jumlah Artikel</th>
                            <th class="p-4 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50 text-sm">
                        <tr v-for="(cat, idx) in filteredCategories" :key="cat.id" class="hover:bg-neutral-50/30 dark:hover:bg-neutral-900/30 transition-colors">
                            <td class="p-4 text-muted-foreground font-mono text-xs">{{ idx + 1 }}</td>
                            <td class="p-4 font-semibold text-foreground flex items-center gap-2">
                                <BookOpen class="h-4 w-4 text-primary" />
                                {{ cat.name }}
                            </td>
                            <td class="p-4 text-muted-foreground font-mono text-xs">{{ cat.slug }}</td>
                            <td class="p-4 text-muted-foreground max-w-xs truncate" :title="cat.description">
                                {{ cat.description || '-' }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ cat.posts_count ?? 0 }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="openEditSheet(cat)"
                                        class="h-8 w-8 text-neutral-500 hover:text-primary hover:bg-neutral-100 dark:hover:bg-neutral-800 cursor-pointer"
                                        title="Edit Kategori"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(cat)"
                                        class="h-8 w-8 text-neutral-500 hover:text-destructive hover:bg-red-50 dark:hover:bg-red-950/20 cursor-pointer"
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
            <h3 class="text-md font-semibold text-foreground">Belum Ada Kategori</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                Silakan buat kategori blog baru terlebih dahulu untuk mulai menulis artikel terstruktur.
            </p>
            <Button @click="openCreateSheet" class="mt-4 bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer flex items-center gap-2">
                <Plus class="h-4 w-4" />
                Tambah Kategori Pertama
            </Button>
        </div>

        <!-- Create/Edit Slide-over Sheet Form -->
        <Sheet v-slot="{ close }" v-model:open="sheetOpen">
            <SheetContent class="w-full sm:max-w-md md:max-w-lg overflow-y-auto flex flex-col h-full bg-card border-border p-6">
                <SheetHeader>
                    <SheetTitle class="text-lg font-bold">
                        {{ isEditing ? 'Edit Kategori Blog' : 'Tambah Kategori Blog Baru' }}
                    </SheetTitle>
                    <SheetDescription>
                        Lengkapi detail nama, slug, deskripsi, dan override SEO metadata untuk kategori blog ini.
                    </SheetDescription>
                </SheetHeader>

                <form @submit.prevent="submit" class="flex-1 flex flex-col justify-between gap-6 py-4">
                    <div class="space-y-5">
                        <!-- Name -->
                        <div class="grid gap-2">
                            <Label for="cat-name" class="font-semibold text-sm">Nama Kategori <span class="text-red-500">*</span></Label>
                            <Input
                                id="cat-name"
                                v-model="form.name"
                                placeholder="Contoh: Laravel, Tips Karir, DevOps"
                                class="w-full"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Slug -->
                        <div class="grid gap-2">
                            <Label for="cat-slug" class="font-semibold text-sm">Slug URL Kategori <span class="text-red-500">*</span></Label>
                            <Input
                                id="cat-slug"
                                v-model="form.slug"
                                @input="isSlugManuallyEdited = true"
                                placeholder="contoh-laravel"
                                class="w-full font-mono text-xs"
                                required
                            />
                            <InputError :message="form.errors.slug" />
                        </div>

                        <!-- Description -->
                        <div class="grid gap-2">
                            <Label for="cat-desc" class="font-semibold text-sm">Deskripsi Singkat</Label>
                            <textarea
                                id="cat-desc"
                                v-model="form.description"
                                rows="3"
                                placeholder="Deskripsi kategori untuk membantu navigasi pembaca..."
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="border-t border-border pt-5 space-y-5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Override SEO Kategori</h4>

                            <!-- Meta Title -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="cat-meta-title" class="font-semibold text-sm">Meta Title</Label>
                                    <span class="text-[10px] text-muted-foreground" :class="{'text-destructive': form.meta_title.length > 60}">
                                        {{ form.meta_title.length }}/60
                                    </span>
                                </div>
                                <Input
                                    id="cat-meta-title"
                                    v-model="form.meta_title"
                                    placeholder="Judul khusus untuk mesin pencari..."
                                    class="w-full"
                                    maxlength="60"
                                />
                                <InputError :message="form.errors.meta_title" />
                            </div>

                            <!-- Meta Description -->
                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="cat-meta-desc" class="font-semibold text-sm">Meta Description</Label>
                                    <span class="text-[10px] text-muted-foreground" :class="{'text-destructive': form.meta_description.length > 160}">
                                        {{ form.meta_description.length }}/160
                                    </span>
                                </div>
                                <textarea
                                    id="cat-meta-desc"
                                    v-model="form.meta_description"
                                    rows="3"
                                    placeholder="Ringkasan SEO pencarian..."
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    maxlength="160"
                                ></textarea>
                                <InputError :message="form.errors.meta_description" />
                            </div>
                        </div>
                    </div>

                    <!-- Footer actions inside form -->
                    <SheetFooter class="flex items-center gap-2 border-t border-border pt-4 sm:space-x-0 mt-auto">
                        <Button type="button" variant="outline" class="w-full sm:w-auto cursor-pointer" @click="sheetOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="w-full sm:w-auto bg-primary text-white hover:bg-primary/90 cursor-pointer font-medium">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Kategori' }}
                        </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border border-border">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-destructive">
                        <AlertTriangle class="h-5 w-5" />
                        Konfirmasi Hapus Kategori
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori <span class="font-bold text-foreground">"{{ categoryToDelete?.name }}"</span> secara permanen? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <div v-if="errorMessage" class="bg-destructive/10 text-destructive text-sm p-3 rounded-lg flex items-start gap-2 border border-destructive/20 my-2">
                    <AlertTriangle class="h-4 w-4 shrink-0 mt-0.5" />
                    <span>{{ errorMessage }}</span>
                </div>

                <DialogFooter class="flex gap-2 sm:gap-0 pt-4 border-t border-border">
                    <Button variant="outline" type="button" @click="deleteDialogOpen = false" class="cursor-pointer">
                        Batal
                    </Button>
                    <Button type="button" variant="destructive" @click="executeDelete" class="cursor-pointer font-medium bg-destructive hover:bg-destructive/90 text-white">
                        Hapus Permanen
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
