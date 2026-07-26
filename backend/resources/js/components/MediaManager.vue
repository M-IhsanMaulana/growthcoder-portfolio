<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import {
    Search,
    Upload,
    X,
    Copy,
    Check,
    Trash2,
    AlertTriangle,
    Image as ImageIcon,
    Loader2,
    ExternalLink,
    ChevronLeft,
    ChevronRight,
    Filter,
    ArrowUpDown,
    Grid,
    List,
    Info,
    Calendar,
    HardDrive,
    Maximize2,
    FileText,
    SlidersHorizontal,
    RefreshCw
} from '@lucide/vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle
} from '@/components/ui/sheet';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import UploadMediaModal from '@/components/UploadMediaModal.vue';

// Define Props
interface Props {
    mode?: 'page' | 'modal';
    initialMedia?: any;
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'page',
    initialMedia: () => null
});

// Emits for modal picker mode
const emit = defineEmits<{
    (e: 'select', media: any): void;
    (e: 'cancel'): void;
}>();

// CSRF Utility
const getCsrfToken = () => {
    const token = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1];
    return token ? decodeURIComponent(token) : '';
};

// State
const mediaList = ref<any[]>([]);
const paginationMeta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 24,
    total: 0,
    from: 0,
    to: 0,
    prev_page_url: null as string | null,
    next_page_url: null as string | null,
});

const isLoading = ref(false);
const searchQuery = ref('');
const debouncedSearchQuery = ref('');
const selectedType = ref(''); // '' | 'png' | 'jpeg' | 'webp' | 'svg'
const selectedSort = ref('latest'); // 'latest' | 'oldest' | 'name' | 'size_desc'
const perPage = ref(24);
const viewMode = ref<'grid' | 'list'>('grid');
const isUploadModalOpen = ref(false);

// Selected media details sheet
const selectedMedia = ref<any | null>(null);
const isDetailSheetOpen = ref(false);
const isSavingAltText = ref(false);
const altTextInput = ref('');

// Delete check dialog
const isDeleteConfirmOpen = ref(false);
const isCheckingUsage = ref(false);
const isDeleting = ref(false);
const usageReport = ref<{ in_use: boolean; usages: any[] }>({ in_use: false, usages: [] });

// Copy indicator
const copiedVariant = ref<string | null>(null);
const copiedGridId = ref<number | null>(null);

// Search Debounce
let searchTimeout: any = null;
watch(searchQuery, (newVal) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        debouncedSearchQuery.value = newVal;
        fetchMedia(1);
    }, 300);
});

// Fetch Media
const fetchMedia = async (page = 1) => {
    isLoading.value = true;
    try {
        const urlObj = new URL('/admin-cms/media', window.location.origin);
        urlObj.searchParams.set('page', page.toString());
        urlObj.searchParams.set('per_page', perPage.value.toString());

        if (debouncedSearchQuery.value) {
            urlObj.searchParams.set('q', debouncedSearchQuery.value);
        }
        if (selectedType.value) {
            urlObj.searchParams.set('type', selectedType.value);
        }
        if (selectedSort.value) {
            urlObj.searchParams.set('sort', selectedSort.value);
        }

        const response = await fetch(urlObj.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch media');
        }

        const data = await response.json();
        mediaList.value = data.data || [];
        paginationMeta.value = {
            current_page: data.current_page || 1,
            last_page: data.last_page || 1,
            per_page: data.per_page || 24,
            total: data.total || 0,
            from: data.from || 0,
            to: data.to || 0,
            prev_page_url: data.prev_page_url || null,
            next_page_url: data.next_page_url || null,
        };
    } catch (error: any) {
        toast.error(error.message || 'Error loading media');
    } finally {
        isLoading.value = false;
    }
};

// Filter & Sort Change Watchers
const handleFilterChange = () => {
    fetchMedia(1);
};

// Page Navigation
const goToPage = (page: number) => {
    if (page >= 1 && page <= paginationMeta.value.last_page && page !== paginationMeta.value.current_page) {
        fetchMedia(page);
    }
};

// Compute Displayed Page Numbers for Pagination
const displayedPages = computed(() => {
    const total = paginationMeta.value.last_page;
    const current = paginationMeta.value.current_page;

    if (total <= 7) {
        return Array.from({ length: total }, (_, i) => i + 1);
    }

    const pages: (number | string)[] = [];
    pages.push(1);

    if (current > 3) {
        pages.push('...');
    }

    const start = Math.max(2, current - 1);
    const end = Math.min(total - 1, current + 1);

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    if (current < total - 2) {
        pages.push('...');
    }

    pages.push(total);

    return pages;
});

// Initialize
onMounted(() => {
    if (props.initialMedia && props.initialMedia.data) {
        mediaList.value = props.initialMedia.data;
        paginationMeta.value = {
            current_page: props.initialMedia.current_page || 1,
            last_page: props.initialMedia.last_page || 1,
            per_page: props.initialMedia.per_page || 24,
            total: props.initialMedia.total || 0,
            from: props.initialMedia.from || 0,
            to: props.initialMedia.to || 0,
            prev_page_url: props.initialMedia.prev_page_url || null,
            next_page_url: props.initialMedia.next_page_url || null,
        };
    } else {
        fetchMedia(1);
    }
});

// File helpers
const formatSize = (bytes: number) => {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getExtensionLabel = (mimeType: string, filename: string) => {
    if (mimeType === 'image/svg+xml' || mimeType === 'image/svg') return 'SVG';
    if (mimeType === 'image/png') return 'PNG';
    if (mimeType === 'image/webp') return 'WEBP';
    if (mimeType === 'image/jpeg' || mimeType === 'image/jpg') return 'JPG';
    if (mimeType === 'image/gif') return 'GIF';
    const ext = filename.split('.').pop()?.toUpperCase();
    return ext || 'IMG';
};

const getExtensionBadgeClass = (ext: string) => {
    switch (ext) {
        case 'SVG':
            return 'bg-amber-500/90 text-white dark:bg-amber-600';
        case 'PNG':
            return 'bg-blue-600/90 text-white dark:bg-blue-600';
        case 'WEBP':
            return 'bg-emerald-600/90 text-white dark:bg-emerald-600';
        case 'JPG':
            return 'bg-purple-600/90 text-white dark:bg-purple-600';
        default:
            return 'bg-slate-700 text-white';
    }
};

const handleUploadSuccess = (newMedia: any) => {
    toast.success('Gambar berhasil diunggah.');
    fetchMedia(1);
};

// Reset Search
const clearSearch = () => {
    searchQuery.value = '';
    debouncedSearchQuery.value = '';
    fetchMedia(1);
};

// Details Sheet
const openDetails = (media: any) => {
    selectedMedia.value = media;
    altTextInput.value = media.alt_text || '';
    isDetailSheetOpen.value = true;
};

// Update Alt Text
const saveAltText = async () => {
    if (!selectedMedia.value) return;
    isSavingAltText.value = true;

    try {
        const response = await fetch(`/admin-cms/media/${selectedMedia.value.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                _method: 'PUT',
                alt_text: altTextInput.value
            })
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || 'Failed to update alt text');
        }

        const data = await response.json();
        const idx = mediaList.value.findIndex(m => m.id === selectedMedia.value.id);
        if (idx !== -1) {
            mediaList.value[idx].alt_text = data.media.alt_text;
            selectedMedia.value.alt_text = data.media.alt_text;
        }

        toast.success('Alt text updated successfully!');
    } catch (error: any) {
        toast.error(error.message || 'Failed to save alt text');
    } finally {
        isSavingAltText.value = false;
    }
};

// Copy URL Utility
const copyUrl = (url: string, variantName: string) => {
    navigator.clipboard.writeText(url).then(() => {
        copiedVariant.value = variantName;
        toast.success(`URL ${variantName} tersalin!`);
        setTimeout(() => {
            copiedVariant.value = null;
        }, 2000);
    });
};

const copyCardUrl = (e: Event, item: any) => {
    e.stopPropagation();
    const url = item.urls.original;
    navigator.clipboard.writeText(url).then(() => {
        copiedGridId.value = item.id;
        toast.success('URL gambar berhasil disalin!');
        setTimeout(() => {
            copiedGridId.value = null;
        }, 2000);
    });
};

// Delete Verification & Trashing
const checkDeleteMedia = async () => {
    if (!selectedMedia.value) return;
    isCheckingUsage.value = true;

    try {
        const response = await fetch(`/admin-cms/media/${selectedMedia.value.id}/usage`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to check usage');
        }

        usageReport.value = await response.json();
        isDeleteConfirmOpen.value = true;
    } catch (error: any) {
        toast.error(error.message || 'Error checking media usage');
    } finally {
        isCheckingUsage.value = false;
    }
};

const executeDeleteMedia = async () => {
    if (!selectedMedia.value) return;
    isDeleting.value = true;

    try {
        const response = await fetch(`/admin-cms/media/${selectedMedia.value.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        });

        if (!response.ok) {
            throw new Error('Failed to delete media');
        }

        toast.success('Media file deleted successfully.');
        isDetailSheetOpen.value = false;
        isDeleteConfirmOpen.value = false;
        selectedMedia.value = null;
        fetchMedia(paginationMeta.value.current_page);
    } catch (error: any) {
        toast.error(error.message || 'Failed to delete media');
    } finally {
        isDeleting.value = false;
    }
};

// Select for Modal mode
const selectMediaItem = (item: any) => {
    if (props.mode === 'modal') {
        emit('select', item);
    } else {
        openDetails(item);
    }
};

// Checkerboard background styling helper
const checkerboardStyle = {
    backgroundImage: 'linear-gradient(45deg, #e5e7eb 25%, transparent 25%), linear-gradient(-45deg, #e5e7eb 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e5e7eb 75%), linear-gradient(-45deg, transparent 75%, #e5e7eb 75%)',
    backgroundSize: '16px 16px',
    backgroundPosition: '0 0, 0 8px, 8px -8px, -8px 0px'
};
</script>

<template>
    <div class="space-y-5">
        <!-- Top Toolbar Control Bar -->
        <div class="flex flex-col gap-3 rounded-2xl border border-border/70 bg-card p-4 shadow-xs">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[240px]">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Cari media berdasarkan nama berkas atau alt text..."
                        class="pl-9 pr-8 h-10 text-sm bg-background border-border/80 focus-visible:ring-1"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute top-1/2 right-2.5 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>

                <!-- Filters & Controls -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- File Type Filter -->
                    <Select
                        :model-value="selectedType || '_all'"
                        @update:model-value="(val) => { selectedType = val === '_all' ? '' : String(val); handleFilterChange(); }"
                    >
                        <SelectTrigger class="h-9 min-w-[145px] text-xs bg-background border-border/80 font-medium">
                            <div class="flex items-center gap-1.5 truncate">
                                <Filter class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                <SelectValue placeholder="Semua Format" />
                            </div>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">Semua Format</SelectItem>
                            <SelectItem value="png">PNG</SelectItem>
                            <SelectItem value="jpeg">JPG / JPEG</SelectItem>
                            <SelectItem value="webp">WebP</SelectItem>
                            <SelectItem value="svg">SVG</SelectItem>
                        </SelectContent>
                    </Select>

                    <!-- Sort Order -->
                    <Select
                        :model-value="selectedSort"
                        @update:model-value="(val) => { selectedSort = String(val); handleFilterChange(); }"
                    >
                        <SelectTrigger class="h-9 min-w-[155px] text-xs bg-background border-border/80 font-medium">
                            <div class="flex items-center gap-1.5 truncate">
                                <ArrowUpDown class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                <SelectValue placeholder="Urutkan" />
                            </div>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="latest">Terbaru</SelectItem>
                            <SelectItem value="oldest">Terlama</SelectItem>
                            <SelectItem value="name">Nama A-Z</SelectItem>
                            <SelectItem value="size_desc">Ukuran Terbesar</SelectItem>
                        </SelectContent>
                    </Select>

                    <!-- Per Page Select -->
                    <Select
                        :model-value="String(perPage)"
                        @update:model-value="(val) => { perPage = Number(val); handleFilterChange(); }"
                    >
                        <SelectTrigger class="h-9 w-[115px] text-xs bg-background border-border/80 font-medium">
                            <div class="flex items-center gap-1.5 truncate">
                                <SlidersHorizontal class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                <SelectValue placeholder="24 / hal" />
                            </div>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="24">24 / hal</SelectItem>
                            <SelectItem value="48">48 / hal</SelectItem>
                            <SelectItem value="96">96 / hal</SelectItem>
                        </SelectContent>
                    </Select>

                    <!-- View Mode Toggle -->
                    <div class="flex items-center border border-border/70 rounded-lg p-0.5 bg-muted/40">
                        <button
                            @click="viewMode = 'grid'"
                            :class="[
                                'p-1.5 rounded-md transition-colors',
                                viewMode === 'grid' ? 'bg-background shadow-2xs text-primary font-semibold' : 'text-muted-foreground hover:text-foreground'
                            ]"
                            title="Grid View"
                        >
                            <Grid class="h-4 w-4" />
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="[
                                'p-1.5 rounded-md transition-colors',
                                viewMode === 'list' ? 'bg-background shadow-2xs text-primary font-semibold' : 'text-muted-foreground hover:text-foreground'
                            ]"
                            title="List View"
                        >
                            <List class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Refresh Button -->
                    <Button
                        @click="fetchMedia(paginationMeta.current_page)"
                        variant="outline"
                        size="icon"
                        class="h-9 w-9 border-border/70"
                        title="Refresh Media"
                    >
                        <RefreshCw :class="['h-3.5 w-3.5 text-muted-foreground', isLoading ? 'animate-spin' : '']" />
                    </Button>

                    <!-- Upload Button -->
                    <Button
                        @click="isUploadModalOpen = true"
                        variant="default"
                        class="h-9 bg-primary hover:bg-brand-primary-hover text-white font-medium text-xs px-3.5"
                    >
                        <Upload class="mr-1.5 h-3.5 w-3.5" />
                        Unggah Media
                    </Button>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="flex items-center justify-between border-t border-border/50 pt-2.5 text-xs text-muted-foreground">
                <div>
                    Total <span class="font-semibold text-foreground">{{ paginationMeta.total }}</span> aset gambar terdaftar
                </div>
                <div v-if="debouncedSearchQuery || selectedType" class="flex items-center gap-2">
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-medium">Filter Aktif</span>
                    <button @click="clearSearch(); selectedType = ''; handleFilterChange();" class="text-xs text-primary hover:underline font-medium">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State Skeleton -->
        <div v-if="isLoading" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
            <div v-for="n in 12" :key="n" class="flex flex-col overflow-hidden rounded-xl border border-border/60 bg-card animate-pulse">
                <div class="aspect-square w-full bg-muted/60"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 w-3/4 bg-muted/60 rounded"></div>
                    <div class="h-2 w-1/2 bg-muted/60 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Media Container: Grid View -->
        <div v-else-if="mediaList.length > 0 && viewMode === 'grid'" class="space-y-6">
            <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
                <div
                    v-for="item in mediaList"
                    :key="item.id"
                    @click="selectMediaItem(item)"
                    class="group relative flex flex-col overflow-hidden rounded-xl border border-border/70 bg-card shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-primary/40 cursor-pointer select-none"
                >
                    <!-- Thumbnail Container with Overlay -->
                    <div
                        class="relative aspect-square w-full border-b border-border/60 overflow-hidden bg-slate-50 dark:bg-slate-900/60"
                        :style="item.mime_type === 'image/png' || item.mime_type === 'image/svg+xml' ? checkerboardStyle : {}"
                    >
                        <img
                            :src="item.urls.thumbnail || item.urls.original"
                            :alt="item.alt_text || item.original_filename"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />

                        <!-- File Format Badge -->
                        <div
                            :class="[
                                'absolute top-2 left-2 text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs tracking-wider uppercase',
                                getExtensionBadgeClass(getExtensionLabel(item.mime_type, item.original_filename))
                            ]"
                        >
                            {{ getExtensionLabel(item.mime_type, item.original_filename) }}
                        </div>

                        <!-- Card Quick Action Hover Buttons -->
                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2 p-2 backdrop-blur-[2px]">
                            <button
                                @click="copyCardUrl($event, item)"
                                class="p-2 rounded-lg bg-background/90 text-foreground hover:bg-primary hover:text-white shadow-xs transition-colors"
                                title="Salin URL Gambar"
                            >
                                <Check v-if="copiedGridId === item.id" class="h-4 w-4 text-emerald-500" />
                                <Copy v-else class="h-4 w-4" />
                            </button>
                            <button
                                @click.stop="openDetails(item)"
                                class="p-2 rounded-lg bg-background/90 text-foreground hover:bg-primary hover:text-white shadow-xs transition-colors"
                                title="Detail Media"
                            >
                                <Info class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Meta info footer -->
                    <div class="p-2.5 space-y-0.5 bg-card">
                        <p class="text-xs font-semibold truncate text-foreground leading-snug group-hover:text-primary transition-colors" :title="item.original_filename">
                            {{ item.original_filename }}
                        </p>
                        <div class="flex items-center justify-between text-[10px] text-muted-foreground pt-0.5">
                            <span>{{ item.width > 0 ? `${item.width}×${item.height}` : 'Vector' }}</span>
                            <span class="font-mono">{{ formatSize(item.file_size) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Container: List View -->
        <div v-else-if="mediaList.length > 0 && viewMode === 'list'" class="rounded-xl border border-border/70 bg-card overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/40 border-b border-border/70 text-muted-foreground font-semibold">
                        <tr>
                            <th class="py-3 px-4 w-14">Pratinjau</th>
                            <th class="py-3 px-4">Nama Berkas</th>
                            <th class="py-3 px-4">Format</th>
                            <th class="py-3 px-4">Dimensi</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4">Tanggal Unggah</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/60">
                        <tr
                            v-for="item in mediaList"
                            :key="item.id"
                            @click="selectMediaItem(item)"
                            class="hover:bg-muted/20 transition-colors cursor-pointer"
                        >
                            <td class="py-2 px-4">
                                <div
                                    class="h-10 w-10 rounded-lg border border-border/60 overflow-hidden flex items-center justify-center bg-slate-50 dark:bg-slate-900"
                                    :style="item.mime_type === 'image/png' || item.mime_type === 'image/svg+xml' ? checkerboardStyle : {}"
                                >
                                    <img
                                        :src="item.urls.thumbnail || item.urls.original"
                                        :alt="item.original_filename"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </td>
                            <td class="py-2.5 px-4 font-semibold text-foreground">
                                <p class="truncate max-w-[280px]" :title="item.original_filename">{{ item.original_filename }}</p>
                                <p v-if="item.alt_text" class="text-[11px] font-normal text-muted-foreground truncate max-w-[280px]">Alt: {{ item.alt_text }}</p>
                            </td>
                            <td class="py-2.5 px-4">
                                <span :class="['text-[10px] font-bold px-2 py-0.5 rounded uppercase', getExtensionBadgeClass(getExtensionLabel(item.mime_type, item.original_filename))]">
                                    {{ getExtensionLabel(item.mime_type, item.original_filename) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-muted-foreground font-mono">
                                {{ item.width > 0 ? `${item.width} × ${item.height} px` : 'SVG Vector' }}
                            </td>
                            <td class="py-2.5 px-4 text-muted-foreground font-mono">
                                {{ formatSize(item.file_size) }}
                            </td>
                            <td class="py-2.5 px-4 text-muted-foreground">
                                {{ new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) }}
                            </td>
                            <td class="py-2.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5" @click.stop>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                        @click="copyCardUrl($event, item)"
                                        title="Salin URL"
                                    >
                                        <Check v-if="copiedGridId === item.id" class="h-3.5 w-3.5 text-emerald-500" />
                                        <Copy v-else class="h-3.5 w-3.5" />
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                        @click="openDetails(item)"
                                        title="Detail Media"
                                    >
                                        <Info class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isLoading && mediaList.length === 0" class="flex flex-col items-center justify-center p-12 text-center border border-border/70 rounded-2xl bg-card/60">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 text-primary mb-4">
                <ImageIcon class="h-8 w-8" />
            </div>
            <h3 class="text-base font-bold text-foreground">Tidak Ada Media Ditemukan</h3>
            <p class="text-xs text-muted-foreground max-w-sm mt-1 mb-4">
                {{ debouncedSearchQuery || selectedType ? 'Tidak ada berkas yang sesuai dengan kriteria pencarian/filter Anda.' : 'Belum ada aset gambar yang diunggah ke Media Library.' }}
            </p>
            <Button v-if="debouncedSearchQuery || selectedType" @click="clearSearch(); selectedType = ''; handleFilterChange();" variant="outline" size="sm" class="h-8 text-xs font-medium">
                Bersihkan Filter
            </Button>
        </div>

        <!-- Pagination Controls Bar -->
        <div v-if="paginationMeta.total > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-border/70">
            <!-- Range Stats -->
            <div class="text-xs text-muted-foreground text-center sm:text-left">
                Menampilkan <span class="font-bold text-foreground">{{ paginationMeta.from || 0 }}</span> - <span class="font-bold text-foreground">{{ paginationMeta.to || 0 }}</span> dari <span class="font-bold text-foreground">{{ paginationMeta.total }}</span> media
            </div>

            <!-- Page Buttons -->
            <div v-if="paginationMeta.last_page > 1" class="flex items-center gap-1">
                <!-- Previous Button -->
                <Button
                    @click="goToPage(paginationMeta.current_page - 1)"
                    :disabled="paginationMeta.current_page === 1 || isLoading"
                    variant="outline"
                    size="sm"
                    class="h-8 px-2.5 text-xs font-medium border-border/70"
                >
                    <ChevronLeft class="h-4 w-4 mr-1" />
                    <span>Sebelumnya</span>
                </Button>

                <!-- Numbered Page Buttons -->
                <template v-for="(p, idx) in displayedPages" :key="idx">
                    <span v-if="p === '...'" class="px-2 text-xs text-muted-foreground select-none">...</span>
                    <Button
                        v-else
                        @click="goToPage(Number(p))"
                        :disabled="isLoading"
                        :variant="paginationMeta.current_page === Number(p) ? 'default' : 'outline'"
                        size="sm"
                        :class="[
                            'h-8 min-w-[32px] px-2 text-xs font-semibold border-border/70',
                            paginationMeta.current_page === Number(p) ? 'bg-primary text-white shadow-2xs hover:bg-brand-primary-hover border-primary' : 'text-foreground hover:bg-muted'
                        ]"
                    >
                        {{ p }}
                    </Button>
                </template>

                <!-- Next Button -->
                <Button
                    @click="goToPage(paginationMeta.current_page + 1)"
                    :disabled="paginationMeta.current_page === paginationMeta.last_page || isLoading"
                    variant="outline"
                    size="sm"
                    class="h-8 px-2.5 text-xs font-medium border-border/70"
                >
                    <span>Selanjutnya</span>
                    <ChevronRight class="h-4 w-4 ml-1" />
                </Button>
            </div>
        </div>

        <!-- Upload Media Modal Form -->
        <UploadMediaModal
            v-model:open="isUploadModalOpen"
            @success="handleUploadSuccess"
        />

        <!-- Media Detail Slide-over Sheet -->
        <Sheet v-model:open="isDetailSheetOpen">
            <SheetContent class="w-full sm:max-w-lg md:max-w-xl flex flex-col h-full bg-background p-0 gap-0 border-l border-border/70 shadow-2xl">
                <!-- Sheet Header -->
                <SheetHeader class="px-6 py-5 border-b border-border/60 bg-muted/10 shrink-0">
                    <div class="flex items-center justify-between gap-3 pr-6">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="flex items-center justify-center h-9 w-9 rounded-lg bg-primary/10 text-primary shrink-0">
                                <ImageIcon class="h-4.5 w-4.5" />
                            </div>
                            <div class="min-w-0">
                                <SheetTitle class="text-base font-bold text-foreground truncate">
                                    {{ selectedMedia?.original_filename }}
                                </SheetTitle>
                                <SheetDescription class="text-xs text-muted-foreground truncate">
                                    Detail properti media, teks alt SEO, dan URL aset.
                                </SheetDescription>
                            </div>
                        </div>
                    </div>
                </SheetHeader>

                <!-- Scrollable Body -->
                <div v-if="selectedMedia" class="flex-1 overflow-y-auto p-6 space-y-5">
                    <!-- Image Preview Container -->
                    <div class="rounded-xl border border-border/60 bg-card p-3 shadow-2xs space-y-2">
                        <div
                            class="relative rounded-lg border border-border/40 aspect-video w-full flex items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-900/50"
                            :style="selectedMedia.mime_type === 'image/png' || selectedMedia.mime_type === 'image/svg+xml' ? checkerboardStyle : {}"
                        >
                            <img
                                :src="selectedMedia.urls.medium || selectedMedia.urls.original"
                                :alt="selectedMedia.alt_text || selectedMedia.original_filename"
                                class="max-h-full max-w-full object-contain p-2 transition-transform duration-300 hover:scale-105"
                            />
                        </div>
                        <div class="flex items-center justify-between px-1 text-xs text-muted-foreground">
                            <span class="font-mono text-[11px] truncate max-w-[240px]">{{ selectedMedia.original_filename }}</span>
                            <a
                                :href="selectedMedia.urls.original"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-xs text-primary hover:underline font-medium"
                            >
                                <span>Lihat Original</span>
                                <ExternalLink class="h-3 w-3" />
                            </a>
                        </div>
                    </div>

                    <!-- Meta Specifications Grid -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-3">
                        <h4 class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">Spesifikasi Berkas</h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-background border border-border/50 text-muted-foreground">
                                    <FileText class="h-3.5 w-3.5 text-primary" />
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted-foreground block">MIME Type</span>
                                    <span class="font-semibold text-foreground truncate block">{{ selectedMedia.mime_type }}</span>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-background border border-border/50 text-muted-foreground">
                                    <HardDrive class="h-3.5 w-3.5 text-primary" />
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted-foreground block">Ukuran Berkas</span>
                                    <span class="font-semibold text-foreground truncate block">{{ formatSize(selectedMedia.file_size) }}</span>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-background border border-border/50 text-muted-foreground">
                                    <Maximize2 class="h-3.5 w-3.5 text-primary" />
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted-foreground block">Dimensi</span>
                                    <span class="font-semibold text-foreground truncate block">{{ selectedMedia.width > 0 ? `${selectedMedia.width} × ${selectedMedia.height} px` : 'Vector SVG' }}</span>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-background border border-border/50 text-muted-foreground">
                                    <Calendar class="h-3.5 w-3.5 text-primary" />
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] text-muted-foreground block">Tanggal Unggah</span>
                                    <span class="font-semibold text-foreground truncate block">{{ new Date(selectedMedia.created_at).toLocaleDateString('id-ID') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alt Text Form (Accessibility & SEO) -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <Label for="altText" class="text-xs font-semibold text-foreground flex items-center gap-2">
                                <span>Alt Text (SEO & Aksesibilitas)</span>
                            </Label>
                            <span class="text-[10px] font-medium text-amber-600 dark:text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full border border-amber-500/20">Sangat Direkomendasikan</span>
                        </div>
                        <div class="flex gap-2">
                            <Input
                                id="altText"
                                v-model="altTextInput"
                                placeholder="Deskripsikan gambar ini untuk SEO..."
                                class="flex-1 text-xs"
                                :disabled="isSavingAltText"
                            />
                            <Button
                                @click="saveAltText"
                                :disabled="isSavingAltText || !altTextInput"
                                size="sm"
                                class="h-9 px-4 font-medium"
                            >
                                <Loader2 v-if="isSavingAltText" class="h-4 w-4 animate-spin" />
                                <span v-else>Simpan</span>
                            </Button>
                        </div>
                    </div>

                    <!-- Link Variant URLs Copier -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider">Salin URL Varian Gambar</h4>
                        </div>
                        <div class="space-y-2">
                            <div
                                v-for="(url, variant) in selectedMedia.urls"
                                :key="variant"
                                class="flex items-center justify-between gap-3 p-2.5 rounded-lg border border-border/50 bg-muted/20 hover:border-border transition-colors"
                            >
                                <div class="min-w-0 flex-1">
                                    <span class="capitalize text-[11px] font-bold text-foreground block mb-0.5">{{ variant }}</span>
                                    <span class="text-xs font-mono text-muted-foreground truncate block select-all">{{ url }}</span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a
                                        :href="url"
                                        target="_blank"
                                        class="p-1.5 hover:bg-background rounded-md text-muted-foreground hover:text-foreground border border-transparent hover:border-border/50 transition-colors"
                                        title="Buka URL"
                                    >
                                        <ExternalLink class="h-3.5 w-3.5" />
                                    </a>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-7 w-7 text-muted-foreground hover:text-foreground"
                                        @click="copyUrl(url, variant)"
                                        title="Salin URL"
                                    >
                                        <Check v-if="copiedVariant === variant" class="h-3.5 w-3.5 text-emerald-500" />
                                        <Copy v-else class="h-3.5 w-3.5" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar -->
                <div v-if="selectedMedia" class="border-t border-border/60 bg-muted/10 p-4 flex items-center justify-between gap-3 shrink-0">
                    <Button
                        @click="checkDeleteMedia"
                        variant="outline"
                        size="sm"
                        class="text-destructive hover:text-destructive hover:bg-destructive/10 border-destructive/20 hover:border-destructive/30 h-9"
                        :disabled="isCheckingUsage"
                    >
                        <Loader2 v-if="isCheckingUsage" class="mr-1.5 h-4 w-4 animate-spin" />
                        <Trash2 v-else class="mr-1.5 h-4 w-4" />
                        Hapus Permanen
                    </Button>

                    <div class="flex items-center gap-2">
                        <Button
                            v-if="props.mode === 'modal'"
                            @click="emit('select', selectedMedia)"
                            size="sm"
                            class="h-9 font-medium"
                        >
                            Pilih Gambar
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="h-9"
                            @click="isDetailSheetOpen = false"
                        >
                            Tutup
                        </Button>
                    </div>
                </div>
            </SheetContent>
        </Sheet>

        <!-- Referential Usage / Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteConfirmOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-red-600">
                        <AlertTriangle class="h-5 w-5" />
                        Konfirmasi Hapus Permanen
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus gambar ini secara permanen? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <!-- Usage Warning -->
                <div v-if="usageReport.in_use" class="p-3.5 rounded-lg border border-red-100 bg-red-50/50 space-y-2">
                    <p class="text-sm font-semibold text-red-800">
                        Peringatan: Gambar ini sedang digunakan!
                    </p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        <li v-for="usage in usageReport.usages" :key="usage.type">
                            Digunakan oleh {{ usage.count }} {{ usage.label }}
                        </li>
                    </ul>
                    <p class="text-xs text-red-600 font-medium">
                        Menghapus gambar ini akan membuat tautan gambar menjadi rusak (broken link).
                    </p>
                </div>
                <div v-else class="p-3.5 rounded-lg border border-border bg-secondary/10">
                    <p class="text-sm text-muted-foreground">
                        Gambar ini tidak sedang digunakan oleh modul lain dan dapat dihapus dengan aman.
                    </p>
                </div>

                <DialogFooter class="flex gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteConfirmOpen = false" :disabled="isDeleting">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="executeDeleteMedia" :disabled="isDeleting">
                        <Loader2 v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
                        Konfirmasi Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
