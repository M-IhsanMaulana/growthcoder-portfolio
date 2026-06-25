<script setup lang="ts">
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import {
    Upload,
    X,
    Loader2,
    ImageIcon,
    FileIcon,
    AlertCircle
} from '@lucide/vue';
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

// Define Props
interface Props {
    open: boolean;
}

defineProps<Props>();

// Emits
const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'success', media: any): void;
}>();

// CSRF Utility
const getCsrfToken = () => {
    const token = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1];
    return token ? decodeURIComponent(token) : '';
};

// Form state
const file = ref<File | null>(null);
const filePreview = ref<string | null>(null);
const customFilename = ref('');
const altText = ref('');
const fileInput = ref<HTMLInputElement | null>(null);

// Upload progress state
const isUploading = ref(false);
const progress = ref(0);

// Drag and drop state
const isDragActive = ref(false);

const slugify = (text: string) => {
    return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start
        .replace(/-+$/, '');            // Trim - from end
};

const handleFileChange = (selectedFile: File) => {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    const maxSize = 10 * 1024 * 1024; // 10MB

    if (!validTypes.includes(selectedFile.type)) {
        toast.error('Only JPG, PNG, GIF, WebP, SVG images are allowed.');
        return;
    }
    if (selectedFile.size > maxSize) {
        toast.error('File size exceeds maximum limit of 10MB.');
        return;
    }

    file.value = selectedFile;

    // Auto-generate slugified custom name
    const originalBaseName = selectedFile.name.substring(0, selectedFile.name.lastIndexOf('.')) || selectedFile.name;
    customFilename.value = slugify(originalBaseName);

    // Create preview
    if (selectedFile.type !== 'image/svg+xml' && selectedFile.type !== 'image/svg') {
        const reader = new FileReader();
        reader.onload = (e) => {
            filePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(selectedFile);
    } else {
        filePreview.value = null; // Don't preview heavy SVG
    }
};

const triggerFileInput = () => {
    if (isUploading.value) return;
    fileInput.value?.click();
};

const onFileSelect = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files && files.length > 0) {
        handleFileChange(files[0]);
    }
};

// Drag and drop events
const onDragEnter = () => { isDragActive.value = true; };
const onDragOver = (e: DragEvent) => { e.preventDefault(); isDragActive.value = true; };
const onDragLeave = () => { isDragActive.value = false; };
const onDrop = (e: DragEvent) => {
    e.preventDefault();
    isDragActive.value = false;
    if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
        handleFileChange(e.dataTransfer.files[0]);
    }
};

const removeSelectedFile = () => {
    file.value = null;
    filePreview.value = null;
    customFilename.value = '';
    altText.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleUpload = () => {
    if (!file.value) return;

    // Enforce slug format (lowercase, replace spaces with hyphens, remove special characters)
    customFilename.value = slugify(customFilename.value);

    if (!altText.value) {
        toast.error('Alt Text is required for accessibility.');
        return;
    }

    isUploading.value = true;
    progress.value = 0;

    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('filename', customFilename.value);
    formData.append('alt_text', altText.value);

    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            progress.value = Math.round((e.loaded * 100) / e.total);
        }
    });

    xhr.addEventListener('load', () => {
        isUploading.value = false;
        if (xhr.status === 200 || xhr.status === 201) {
            const response = JSON.parse(xhr.responseText);
            toast.success('Image uploaded successfully!');
            emit('success', response.media);
            closeModal();
        } else {
            const response = JSON.parse(xhr.responseText || '{}');
            toast.error(response.message || 'Upload failed');
        }
    });

    xhr.addEventListener('error', () => {
        isUploading.value = false;
        toast.error('Upload failed due to network error');
    });

    xhr.open('POST', '/admin-cms/media');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');

    const csrf = getCsrfToken();
    if (csrf) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
    }

    xhr.send(formData);
};

const closeModal = () => {
    if (isUploading.value) return;
    removeSelectedFile();
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="closeModal">
        <DialogContent class="sm:max-w-lg bg-card border-border p-6 overflow-hidden flex flex-col max-h-[90vh]">
            <DialogHeader class="shrink-0">
                <DialogTitle class="text-xl font-bold flex items-center gap-2">
                    <Upload class="h-5 w-5 text-brand-primary" />
                    Upload Image
                </DialogTitle>
                <DialogDescription>
                    Select an image file, customize its slug filename, and fill in the alt text.
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-y-auto min-h-0 py-4 space-y-4 pr-1">
                <!-- Dropzone selector -->
                <div v-if="!file"
                    @dragenter="onDragEnter"
                    @dragover="onDragOver"
                    @dragleave="onDragLeave"
                    @drop="onDrop"
                    :class="[
                        'relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-10 text-center transition-all duration-200 cursor-pointer',
                        isDragActive
                            ? 'border-brand-primary bg-secondary/50 scale-[1.01]'
                            : 'border-border bg-card hover:border-brand-primary/60'
                    ]"
                    @click="triggerFileInput"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary/80 text-primary mb-4">
                        <Upload class="h-6 w-6 text-brand-primary" />
                    </div>
                    <p class="font-semibold text-sm">Drag and drop your image here, or click to browse</p>
                    <p class="text-xs text-muted-foreground mt-1">Supports PNG, JPG, GIF, WebP, SVG up to 10MB</p>
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                        class="hidden"
                        @change="onFileSelect"
                    />
                </div>

                <!-- Selected file preview & inputs -->
                <div v-else class="space-y-4">
                    <!-- File Preview Card -->
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-border bg-secondary/10">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Image preview or generic icon -->
                            <div v-if="filePreview" class="h-12 w-12 rounded-lg overflow-hidden border border-border bg-slate-100 flex items-center justify-center shrink-0">
                                <img :src="filePreview" class="h-full w-full object-cover" />
                            </div>
                            <div v-else class="h-12 w-12 rounded-lg bg-secondary flex items-center justify-center shrink-0">
                                <FileIcon class="h-6 w-6 text-brand-primary" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate text-foreground">{{ file.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</p>
                            </div>
                        </div>
                        <Button
                            v-if="!isUploading"
                            size="icon"
                            variant="ghost"
                            class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                            @click="removeSelectedFile"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>

                    <!-- Custom Filename Field -->
                    <div class="space-y-2">
                        <Label for="customName" class="text-sm font-semibold">Custom Filename (for secure SEO URLs)</Label>
                        <div class="relative">
                            <Input
                                id="customName"
                                v-model="customFilename"
                                placeholder="e.g. project-banner-large"
                                :disabled="isUploading"
                                @blur="customFilename = slugify(customFilename)"
                                @change="customFilename = slugify(customFilename)"
                                class="pr-12"
                            />
                            <div class="absolute inset-y-0 right-3 flex items-center text-xs font-semibold text-muted-foreground pointer-events-none select-none">
                                .webp
                            </div>
                        </div>
                        <p class="text-[10px] text-muted-foreground leading-snug">
                            Only lowercase letters, numbers, and hyphens. Spaces will be converted to hyphens. URL format will be: <span class="font-mono text-brand-primary">/media/{{ customFilename || 'filename' }}-{encoded_id}/original</span>
                        </p>
                    </div>

                    <!-- Alt Text Field -->
                    <div class="space-y-2">
                        <Label for="uploadAlt" class="text-sm font-semibold flex items-center justify-between">
                            <span>Alt Text (SEO & Accessibility)</span>
                            <span class="text-xs font-normal text-red-500">* Wajib Diisi</span>
                        </Label>
                        <Input
                            id="uploadAlt"
                            v-model="altText"
                            placeholder="Describe this image for SEO and screen readers..."
                            :disabled="isUploading"
                        />
                    </div>

                    <!-- Upload Progress -->
                    <div v-if="isUploading" class="space-y-2 pt-2">
                        <div class="flex justify-between items-center text-xs font-semibold">
                            <span class="text-brand-primary flex items-center gap-1.5">
                                <Loader2 class="h-3.5 w-3.5 animate-spin" />
                                Processing image...
                            </span>
                            <span>{{ progress }}%</span>
                        </div>
                        <div class="w-full h-2 bg-border rounded-full overflow-hidden">
                            <div class="h-full bg-brand-primary transition-all duration-200" :style="{ width: progress + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <DialogFooter class="shrink-0 flex gap-2 sm:gap-0 pt-4 border-t border-border">
                <Button variant="outline" @click="closeModal" :disabled="isUploading">
                    Cancel
                </Button>
                <Button
                    v-if="file"
                    variant="default"
                    @click="handleUpload"
                    :disabled="isUploading || !altText || !customFilename"
                    class="bg-brand-primary hover:bg-brand-primary-hover text-white font-medium"
                >
                    <Loader2 v-if="isUploading" class="mr-2 h-4 w-4 animate-spin" />
                    Upload Asset
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
