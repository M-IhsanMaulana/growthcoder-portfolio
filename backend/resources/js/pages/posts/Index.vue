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
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import {
    Plus,
    FileText,
    AlertTriangle,
    Trash2,
    Edit2,
    Eye,
    Search,
    X,
    Calendar,
    BookOpen,
    Filter
} from '@lucide/vue';
import { index as postsIndex, create as postsCreate, edit as postsEdit, show as postsShow } from '@/routes/posts';

const props = defineProps<{
    posts: any[];
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
                title: 'Daftar Artikel',
                href: postsIndex(),
            },
        ],
    },
});

// Search and Filter States
const searchFilter = ref(props.filters.q || '');
const categoryFilter = ref(props.filters.category_id || 'all');
const statusFilter = ref(props.filters.status || 'all');

// Watch states to sync and submit filters via Inertia
watch([searchFilter, categoryFilter, statusFilter], () => {
    applyFilters();
});

const applyFilters = () => {
    router.get('/admin-cms/posts', {
        q: searchFilter.value || undefined,
        category_id: categoryFilter.value !== 'all' ? categoryFilter.value : undefined,
        status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    searchFilter.value = '';
    categoryFilter.value = 'all';
    statusFilter.value = 'all';
};

// Format Date Utility
const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Delete Dialog State
const deleteDialogOpen = ref(false);
const postToDelete = ref<any | null>(null);

const confirmDelete = (post: any) => {
    postToDelete.value = post;
    deleteDialogOpen.value = true;
};

const executeDelete = () => {
    if (!postToDelete.value) return;

    router.delete(`/admin-cms/posts/${postToDelete.value.id}`, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            postToDelete.value = null;
        }
    });
};
</script>

<template>
    <Head title="Artikel Blog" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4 md:p-6 overflow-y-auto">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-border pb-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Artikel Blog</h1>
                <p class="text-sm text-muted-foreground">
                    Tulis dan kelola artikel blog teknis atau studi kasus untuk optimasi SEO organik platform Anda.
                </p>
            </div>
            <div>
                <Link :href="postsCreate()">
                    <Button class="bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Tulis Artikel Baru
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Toolbar Filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-card p-3 rounded-2xl border border-border/70 shadow-2xs">
            <!-- Search -->
            <div class="relative flex-1 max-w-md">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                    v-model="searchFilter"
                    placeholder="Cari artikel..."
                    class="pl-9 pr-8 bg-background border-border/80 h-9 text-xs w-full"
                />
                <button
                    v-if="searchFilter"
                    @click="searchFilter = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                >
                    <X class="h-3.5 w-3.5" />
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Category Filter -->
                <Select :model-value="String(categoryFilter)" @update:model-value="(v) => categoryFilter = String(v)">
                    <SelectTrigger class="h-9 min-w-[150px] text-xs bg-background border-border/80 font-medium">
                        <div class="flex items-center gap-1.5 truncate">
                            <Filter class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                            <SelectValue placeholder="Semua Kategori" />
                        </div>
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kategori</SelectItem>
                        <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                            {{ cat.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <!-- Status Filter -->
                <Select :model-value="String(statusFilter)" @update:model-value="(v) => statusFilter = String(v)">
                    <SelectTrigger class="h-9 w-[130px] text-xs bg-background border-border/80 font-medium">
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Status</SelectItem>
                        <SelectItem value="published">Published</SelectItem>
                        <SelectItem value="draft">Draft</SelectItem>
                        <SelectItem value="scheduled">Scheduled</SelectItem>
                    </SelectContent>
                </Select>

                <!-- Reset Filters Button -->
                <Button
                    v-if="searchFilter || categoryFilter !== 'all' || statusFilter !== 'all'"
                    variant="ghost"
                    size="sm"
                    @click="resetFilters"
                    class="h-9 px-2.5 text-xs text-muted-foreground hover:text-primary cursor-pointer"
                >
                    Reset
                </Button>
            </div>
        </div>

        <!-- Posts Table -->
        <div v-if="posts.length > 0" class="border border-border/70 rounded-2xl overflow-hidden bg-card shadow-2xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-border/70 bg-muted/20 text-[11px] font-bold text-muted-foreground uppercase tracking-wider">
                            <th class="p-4 w-20">Cover</th>
                            <th class="p-4">Judul Artikel</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 w-32">Status</th>
                            <th class="p-4 w-44">Tanggal Publikasi</th>
                            <th class="p-4 w-28 text-center">Waktu Baca</th>
                            <th class="p-4 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60 text-sm">
                        <tr
                            v-for="post in posts"
                            :key="post.id"
                            @click="router.visit(postsEdit(post.id).url)"
                            class="hover:bg-muted/20 transition-colors cursor-pointer"
                        >
                            <!-- Cover Column -->
                            <td class="p-4">
                                <div class="h-10 w-16 rounded-lg overflow-hidden bg-slate-50 dark:bg-slate-900 border border-border/60 flex items-center justify-center shrink-0">
                                    <img
                                        v-if="post.cover_image"
                                        :src="post.cover_image.urls.thumbnail"
                                        :alt="post.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <FileText v-else class="h-5 w-5 text-muted-foreground/50" />
                                </div>
                            </td>

                            <!-- Title Column -->
                            <td class="p-4">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-semibold text-foreground hover:text-primary transition-colors line-clamp-1">
                                        {{ post.title }}
                                    </span>
                                    <span class="text-[11px] text-muted-foreground font-mono truncate max-w-[280px]">/blog/{{ post.slug }}</span>
                                </div>
                            </td>

                            <!-- Categories Badges -->
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="cat in post.categories"
                                        :key="cat.id"
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-muted/50 text-foreground border border-border/60"
                                    >
                                        {{ cat.name }}
                                    </span>
                                    <span v-if="post.categories.length === 0" class="text-xs text-muted-foreground">-</span>
                                </div>
                            </td>

                            <!-- Status Badges -->
                            <td class="p-4">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                        post.status === 'published'
                                            ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
                                            : post.status === 'scheduled'
                                                ? 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30'
                                                : 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20'
                                    ]"
                                >
                                    {{ post.status }}
                                </span>
                            </td>

                            <!-- Published Date -->
                            <td class="p-4 text-xs text-muted-foreground font-medium">
                                <div class="flex items-center gap-1.5" v-if="post.status === 'published'">
                                    <Calendar class="h-3.5 w-3.5 shrink-0" />
                                    <span>{{ formatDate(post.published_at) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400" v-else-if="post.status === 'scheduled'">
                                    <Calendar class="h-3.5 w-3.5 shrink-0" />
                                    <span title="Jadwal rilis">{{ formatDate(post.scheduled_at) }}</span>
                                </div>
                                <span v-else class="text-muted-foreground">-</span>
                            </td>

                            <!-- Reading Time -->
                            <td class="p-4 text-center text-xs font-bold text-foreground">
                                <div class="flex items-center justify-center gap-1">
                                    <BookOpen class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                    <span>{{ post.reading_time || 1 }} mnt</span>
                                </div>
                            </td>

                            <!-- Actions Column -->
                            <td class="p-4 text-right" @click.stop>
                                <div class="flex items-center justify-end gap-1">
                                    <Link :href="postsShow(post.id)">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10 cursor-pointer"
                                            title="Detail & Statistik Artikel"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link :href="postsEdit(post.id)">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10 cursor-pointer"
                                            title="Edit Artikel"
                                        >
                                            <Edit2 class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(post)"
                                        class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10 cursor-pointer"
                                        title="Hapus Artikel"
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
                <FileText class="h-6 w-6" />
            </div>
            <h3 class="text-md font-semibold text-foreground">Belum Ada Artikel</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">
                Belum ada konten yang ditulis. Mulai bagikan pengetahuan teknis Anda dengan menulis artikel baru.
            </p>
            <Link :href="postsCreate()">
                <Button class="mt-4 bg-primary hover:bg-primary/90 text-white font-medium shadow-sm transition-all duration-150 cursor-pointer flex items-center gap-2">
                    <Plus class="h-4 w-4" />
                    Tulis Artikel Pertama
                </Button>
            </Link>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border border-border">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-destructive">
                        <AlertTriangle class="h-5 w-5" />
                        Konfirmasi Hapus Artikel
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus artikel <span class="font-bold text-foreground">"{{ postToDelete?.title }}"</span> secara permanen? Seluruh data relasi terkait akan ikut terhapus.
                    </DialogDescription>
                </DialogHeader>

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
