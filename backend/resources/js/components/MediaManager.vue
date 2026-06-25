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
    FileIcon,
    Loader2,
    ExternalLink,
    ChevronDown
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
import UploadMediaModal from '@/components/UploadMediaModal.vue';

// Define Props
interface Props {
    mode?: 'page' | 'modal';
    initialMedia?: {
        data: any[];
        next_page_url: string | null;
    };
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'page',
    initialMedia: () => ({ data: [], next_page_url: null })
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
const nextPageUrl = ref<string | null>(null);
const isLoading = ref(false);
const searchQuery = ref('');
const debouncedSearchQuery = ref('');
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

// Search Debounce
let searchTimeout: any = null;
watch(searchQuery, (newVal) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        debouncedSearchQuery.value = newVal;
        fetchMedia(true);
    }, 300);
});

// Fetch Media
const fetchMedia = async (reset = false) => {
    isLoading.value = true;
    try {
        let url = '/admin-cms/media';
        if (!reset && nextPageUrl.value) {
            url = nextPageUrl.value;
        }

        const urlObj = new URL(url, window.location.origin);
        if (debouncedSearchQuery.value) {
            urlObj.searchParams.set('q', debouncedSearchQuery.value);
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
        if (reset) {
            mediaList.value = data.data;
        } else {
            // Filter duplicates
            const existingIds = new Set(mediaList.value.map(item => item.id));
            const newItems = data.data.filter((item: any) => !existingIds.has(item.id));
            mediaList.value.push(...newItems);
        }
        nextPageUrl.value = data.next_page_url;
    } catch (error: any) {
        toast.error(error.message || 'Error loading media');
    } finally {
        isLoading.value = false;
    }
};

// Initialize
onMounted(() => {
    if (props.initialMedia && props.initialMedia.data && props.initialMedia.data.length > 0) {
        mediaList.value = props.initialMedia.data;
        nextPageUrl.value = props.initialMedia.next_page_url;
    } else {
        fetchMedia(true);
    }
});

// File helpers
const formatSize = (bytes: number) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const handleUploadSuccess = (newMedia: any) => {
    mediaList.value.unshift(newMedia);
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
        // Update local item
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
        toast.success(`${variantName} URL copied to clipboard!`);
        setTimeout(() => {
            copiedVariant.value = null;
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

        // Remove from list
        mediaList.value = mediaList.value.filter(m => m.id !== selectedMedia.value.id);
        toast.success('Media file deleted successfully.');
        isDetailSheetOpen.value = false;
        isDeleteConfirmOpen.value = false;
        selectedMedia.value = null;
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
    backgroundSize: '20px 20px',
    backgroundPosition: '0 0, 0 10px, 10px -10px, -10px 0px'
};
</script>

<template>
    <div class="space-y-6">
        <!-- Top controls & search -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-sm flex-1">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    placeholder="Search files by name..."
                    class="pl-9"
                />
            </div>
            <div class="flex items-center gap-2">
                <Button @click="isUploadModalOpen = true" variant="default" class="bg-primary hover:bg-brand-primary-hover text-white">
                    <Upload class="mr-2 h-4 w-4" />
                    Upload Image
                </Button>
            </div>
        </div>

        <!-- Media Grid -->
        <div v-if="mediaList.length > 0">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
                <div
                    v-for="item in mediaList"
                    :key="item.id"
                    @click="selectMediaItem(item)"
                    class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md cursor-pointer select-none"
                >
                    <!-- Thumbnail area with checkerboard bg -->
                    <div
                        class="relative aspect-square w-full border-b border-border overflow-hidden bg-slate-50 dark:bg-slate-900"
                        :style="item.mime_type === 'image/png' || item.mime_type === 'image/svg+xml' ? checkerboardStyle : {}"
                    >
                        <img
                            :src="item.urls.thumbnail"
                            :alt="item.alt_text || item.original_filename"
                            loading="lazy"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div v-if="item.mime_type === 'image/svg+xml'" class="absolute top-2 right-2 bg-brand-primary/95 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs">
                            SVG
                        </div>
                    </div>

                    <!-- Meta info footer -->
                    <div class="p-2.5 space-y-0.5">
                        <p class="text-xs font-medium truncate text-foreground leading-snug" :title="item.original_filename">
                            {{ item.original_filename }}
                        </p>
                        <p class="text-[10px] text-muted-foreground leading-none">
                            {{ item.width }}x{{ item.height }} • {{ formatSize(item.file_size) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Load More Footer -->
            <div v-if="nextPageUrl" class="flex justify-center pt-8">
                <Button
                    @click="fetchMedia(false)"
                    :disabled="isLoading"
                    variant="outline"
                    class="border-border hover:bg-secondary/40 font-medium"
                >
                    <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                    <ChevronDown v-else class="mr-2 h-4 w-4" />
                    Load More Assets
                </Button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!isLoading" class="flex flex-col items-center justify-center p-12 text-center border border-border rounded-2xl bg-card/40">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-secondary/80 text-muted-foreground mb-4">
                <ImageIcon class="h-8 w-8 text-muted-foreground/60" />
            </div>
            <h3 class="text-lg font-semibold">No media found</h3>
            <p class="text-sm text-muted-foreground max-w-xs mt-1">Upload your first image asset or refine your search query.</p>
        </div>

        <!-- Loading Skeleton Grid -->
        <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8">
            <div v-for="n in 12" :key="n" class="flex flex-col overflow-hidden rounded-xl border border-border bg-card animate-pulse">
                <div class="aspect-square w-full bg-border/40"></div>
                <div class="p-3 space-y-2">
                    <div class="h-3 w-3/4 bg-border/40 rounded"></div>
                    <div class="h-2 w-1/2 bg-border/40 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Upload Media Modal Form -->
        <UploadMediaModal
            v-model:open="isUploadModalOpen"
            @success="handleUploadSuccess"
        />

        <!-- Media Detail Slide-over Sheet -->
        <Sheet v-model:open="isDetailSheetOpen">
            <SheetContent class="w-full sm:max-w-md md:max-w-lg overflow-y-auto space-y-6">
                <SheetHeader>
                    <SheetTitle class="text-xl font-bold truncate pr-6">{{ selectedMedia?.original_filename }}</SheetTitle>
                    <SheetDescription>View properties, edit alt text, copy URLs, or delete asset.</SheetDescription>
                </SheetHeader>

                <div v-if="selectedMedia" class="space-y-6">
                    <!-- Image Preview Area -->
                    <div
                        class="relative rounded-xl border border-border aspect-video w-full flex items-center justify-center overflow-hidden bg-slate-50 dark:bg-slate-900"
                        :style="selectedMedia.mime_type === 'image/png' || selectedMedia.mime_type === 'image/svg+xml' ? checkerboardStyle : {}"
                    >
                        <img
                            :src="selectedMedia.urls.medium"
                            :alt="selectedMedia.alt_text || selectedMedia.original_filename"
                            class="max-h-full max-w-full object-contain"
                        />
                    </div>

                    <!-- Meta specifications list -->
                    <div class="rounded-xl border border-border p-4 bg-secondary/10 space-y-2.5">
                        <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Specifications</h4>
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                            <div>
                                <span class="text-muted-foreground block text-xs">MIME Type</span>
                                <span class="font-medium text-foreground">{{ selectedMedia.mime_type }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-xs">File Size</span>
                                <span class="font-medium text-foreground">{{ formatSize(selectedMedia.file_size) }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-xs">Dimensions</span>
                                <span class="font-medium text-foreground">{{ selectedMedia.width }} x {{ selectedMedia.height }} px</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground block text-xs">Uploaded At</span>
                                <span class="font-medium text-foreground">{{ new Date(selectedMedia.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alt Text Form (Accessibility & SEO) -->
                    <div class="space-y-2">
                        <Label for="altText" class="text-sm font-semibold flex items-center justify-between">
                            <span>Alt Text (SEO & Accessibility)</span>
                            <span class="text-xs font-normal text-red-500">* Wajib Diisi</span>
                        </Label>
                        <div class="flex gap-2">
                            <Input
                                id="altText"
                                v-model="altTextInput"
                                placeholder="Describe this image for screen readers and SEO..."
                                class="flex-1"
                                :disabled="isSavingAltText"
                            />
                            <Button
                                @click="saveAltText"
                                :disabled="isSavingAltText || !altTextInput"
                                variant="default"
                                class="bg-brand-primary text-white hover:bg-brand-primary-hover"
                            >
                                <Loader2 v-if="isSavingAltText" class="h-4 w-4 animate-spin" />
                                <span v-else>Save</span>
                            </Button>
                        </div>
                    </div>

                    <!-- Link Variant URLs Copier -->
                    <div class="space-y-3">
                        <Label class="text-sm font-semibold">Copy Secure Image URLs</Label>
                        <div class="space-y-2">
                            <div
                                v-for="(url, variant) in selectedMedia.urls"
                                :key="variant"
                                class="flex items-center justify-between gap-3 p-2 rounded-lg border border-border bg-card"
                            >
                                <div class="min-w-0">
                                    <span class="capitalize text-xs font-semibold text-muted-foreground block leading-none mb-0.5">{{ variant }}</span>
                                    <span class="text-xs truncate block max-w-xs text-foreground">{{ url }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <a :href="url" target="_blank" class="p-1.5 hover:bg-secondary rounded text-muted-foreground hover:text-foreground">
                                        <ExternalLink class="h-3.5 w-3.5" />
                                    </a>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                        @click="copyUrl(url, variant)"
                                    >
                                        <Check v-if="copiedVariant === variant" class="h-4 w-4 text-green-600" />
                                        <Copy v-else class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deletion action -->
                    <div class="pt-4 border-t border-border flex gap-3">
                        <Button
                            @click="checkDeleteMedia"
                            variant="destructive"
                            class="w-full font-medium"
                            :disabled="isCheckingUsage"
                        >
                            <Loader2 v-if="isCheckingUsage" class="mr-2 h-4 w-4 animate-spin" />
                            <Trash2 v-else class="mr-2 h-4 w-4" />
                            Delete Permanent
                        </Button>
                        <Button
                            v-if="props.mode === 'modal'"
                            @click="emit('select', selectedMedia)"
                            class="w-full bg-brand-accent hover:bg-brand-accent-hover text-white font-medium"
                        >
                            Select Image
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
                        Confirm Permanent Deletion
                    </DialogTitle>
                    <DialogDescription>
                        Are you sure you want to permanently delete this image? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>

                <!-- Usage Warning -->
                <div v-if="usageReport.in_use" class="p-3.5 rounded-lg border border-red-100 bg-red-50/50 space-y-2">
                    <p class="text-sm font-semibold text-red-800">
                        Warning: This image is currently in use!
                    </p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        <li v-for="usage in usageReport.usages" :key="usage.type">
                            Used by {{ usage.count }} {{ usage.label }}
                        </li>
                    </ul>
                    <p class="text-xs text-red-600 font-medium">
                        Deleting this image will cause broken image links. The references in those records will automatically be set to null.
                    </p>
                </div>
                <div v-else class="p-3.5 rounded-lg border border-border bg-secondary/10">
                    <p class="text-sm text-muted-foreground">
                        This image is not currently used by any other module and can be safely deleted.
                    </p>
                </div>

                <DialogFooter class="flex gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteConfirmOpen = false" :disabled="isDeleting">
                        Cancel
                    </Button>
                    <Button variant="destructive" @click="executeDeleteMedia" :disabled="isDeleting">
                        <Loader2 v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
                        Confirm Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
