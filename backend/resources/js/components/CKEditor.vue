<script lang="ts">
import { Plugin, ButtonView } from 'ckeditor5';

// Custom Plugin for Laravel Media Library
export class MediaLibraryPlugin extends Plugin {
    static get pluginName() {
        return 'MediaLibraryPlugin';
    }

    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('mediaLibrary', (locale) => {
            const button = new ButtonView(locale);

            button.set({
                label: 'Sisipkan Gambar',
                icon: `<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="16" height="16" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="6.5" cy="6.5" r="1.5" fill="currentColor"/><path d="M2 13l4-4 4 4 4-4 4 4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
                withText: false,
                tooltip: 'Sisipkan Gambar dari Media Library'
            });

            button.on('execute', () => {
                editor.fire('openMediaLibrary');
            });

            return button;
        });
    }
}
</script>

<script setup lang="ts">
import { ref, watch } from 'vue';
import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Underline,
    Strikethrough,
    Code,
    CodeBlock,
    BlockQuote,
    List,
    Link,
    Table,
    TableToolbar,
    Alignment,
    Highlight,
    HorizontalLine,
    FontColor,
    FontSize,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    SourceEditing,
    GeneralHtmlSupport
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import { Check } from '@lucide/vue';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
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

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const editorData = ref(props.modelValue || '');
const editorInstance = ref<any>(null);
const mediaModalOpen = ref(false);
const imageConfigOpen = ref(false);
const pendingImageMedia = ref<any | null>(null);

// Image configuration form
const imageConfig = ref({
    altText: '',
    caption: '',
    sizeVariant: 'large'
});

const editorConfig = {
    licenseKey: 'GPL',
    placeholder: props.placeholder || 'Tulis deskripsi studi kasus di sini...',
    plugins: [
        Essentials,
        Paragraph,
        Heading,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Code,
        CodeBlock,
        BlockQuote,
        List,
        Link,
        Table,
        TableToolbar,
        Alignment,
        Highlight,
        HorizontalLine,
        FontColor,
        FontSize,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize,
        SourceEditing,
        GeneralHtmlSupport,
        MediaLibraryPlugin
    ],
    toolbar: {
        items: [
            'sourceEditing',
            '|',
            'undo', 'redo',
            '|',
            'heading',
            '|',
            'fontSize', 'fontColor',
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'code', 'highlight',
            '|',
            'alignment',
            '|',
            'bulletedList', 'numberedList', 'blockQuote', 'codeBlock',
            '|',
            'link', 'insertTable', 'mediaLibrary', 'horizontalLine'
        ]
    },
    heading: {
        options: [
            { model: 'paragraph' as const, title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading2' as const, view: 'h2' as const, title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3' as const, view: 'h3' as const, title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4' as const, view: 'h4' as const, title: 'Heading 4', class: 'ck-heading_heading4' }
        ]
    },
    table: {
        contentToolbar: [
            'tableColumn', 'tableRow', 'mergeTableCells'
        ]
    },
    image: {
        toolbar: [
            'imageStyle:block',
            'imageStyle:side',
            '|',
            'toggleImageCaption',
            'imageTextAlternative'
        ]
    },
    htmlSupport: {
        allow: [
            {
                name: /.*/,
                attributes: true,
                classes: true,
                styles: true
            }
        ]
    }
};

const onEditorReady = (editor: any) => {
    editorInstance.value = editor;
    editor.on('openMediaLibrary', () => {
        mediaModalOpen.value = true;
    });
};

// Sync parent modelValue modifications to local editor state
watch(() => props.modelValue, (newVal) => {
    if (editorData.value !== newVal) {
        editorData.value = newVal || '';
    }
});

// Emit local updates to parent
watch(editorData, (newVal) => {
    emit('update:modelValue', newVal);
});

// Media library select handler -> opens config dialog
const onMediaSelect = (media: any) => {
    pendingImageMedia.value = media;
    imageConfig.value.altText = media.alt_text || media.original_filename || '';
    imageConfig.value.caption = '';
    
    if (media.urls.large) {
        imageConfig.value.sizeVariant = 'large';
    } else if (media.urls.medium) {
        imageConfig.value.sizeVariant = 'medium';
    } else {
        imageConfig.value.sizeVariant = 'original';
    }
    
    imageConfigOpen.value = true;
};

// Confirm image insertion from config dialog
const confirmInsertImage = () => {
    if (!editorInstance.value || !pendingImageMedia.value) return;

    const media = pendingImageMedia.value;
    const size = imageConfig.value.sizeVariant;
    const imageUrl = media.urls[size] || media.urls.original;
    const altText = imageConfig.value.altText || 'Project detail image';
    const caption = imageConfig.value.caption;

    const editor = editorInstance.value;

    editor.model.change((writer: any) => {
        const imageElement = writer.createElement('imageBlock', {
            src: imageUrl,
            alt: altText,
        });

        if (caption) {
            const captionElement = writer.createElement('caption');
            writer.insertText(caption, captionElement);
            writer.append(captionElement, imageElement);
        }

        editor.model.insertContent(imageElement, editor.model.document.selection);
    });

    imageConfigOpen.value = false;
    pendingImageMedia.value = null;
};
</script>

<template>
    <div class="flex flex-col border border-sidebar-border/70 rounded-xl overflow-hidden bg-card transition-all duration-200">
        <!-- CKEditor Component -->
        <Ckeditor
            :editor="ClassicEditor"
            v-model="editorData"
            :config="editorConfig"
            @ready="onEditorReady"
        />

        <!-- Media Library Modal for Inline Images -->
        <MediaLibraryModal
            :open="mediaModalOpen"
            @update:open="mediaModalOpen = $event"
            @select="onMediaSelect"
        />

        <!-- Image Configuration Dialog -->
        <Dialog v-model:open="imageConfigOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle>Konfigurasi Gambar</DialogTitle>
                    <DialogDescription>
                        Atur detail caption, alt text, dan ukuran varian sebelum disisipkan ke editor.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 py-2" v-if="pendingImageMedia">
                    <!-- Image Preview -->
                    <div class="h-28 w-full rounded-lg overflow-hidden border border-sidebar-border bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                        <img
                            :src="pendingImageMedia.urls.thumbnail"
                            alt="Selected media"
                            class="h-full w-auto object-contain"
                        />
                    </div>

                    <!-- Size Variant selector -->
                    <div class="grid gap-1.5">
                        <Label class="text-xs font-bold text-foreground">Ukuran Resolusi Gambar</Label>
                        <select
                            v-model="imageConfig.sizeVariant"
                            class="flex h-9 w-full rounded-md border border-input bg-card px-3 py-1 text-sm shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option value="original">Original (Full Size)</option>
                            <option value="large" v-if="pendingImageMedia.urls.large">Large (Desktop Wide)</option>
                            <option value="medium" v-if="pendingImageMedia.urls.medium">Medium (Mobile Friendly)</option>
                            <option value="thumbnail" v-if="pendingImageMedia.urls.thumbnail">Thumbnail</option>
                            <option value="webp" v-if="pendingImageMedia.urls.webp">WebP (Compressed)</option>
                        </select>
                    </div>

                    <!-- Alt Text -->
                    <div class="grid gap-1.5">
                        <Label for="img-alt" class="text-xs font-bold text-foreground">Alt Text (SEO & Aksesibilitas)</Label>
                        <Input
                            id="img-alt"
                            v-model="imageConfig.altText"
                            placeholder="Deskripsikan konten gambar..."
                        />
                    </div>

                    <!-- Figcaption -->
                    <div class="grid gap-1.5">
                        <Label for="img-caption" class="text-xs font-bold text-foreground">Keterangan Gambar (Caption)</Label>
                        <textarea
                            id="img-caption"
                            v-model="imageConfig.caption"
                            rows="2"
                            placeholder="Isi keterangan gambar (akan tampil di bawah gambar)..."
                            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        ></textarea>
                    </div>
                </div>

                <DialogFooter class="flex gap-2 sm:gap-0">
                    <Button variant="outline" type="button" @click="imageConfigOpen = false" class="cursor-pointer">
                        Batal
                    </Button>
                    <Button type="button" @click="confirmInsertImage" class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer flex items-center gap-2">
                        <Check class="h-4 w-4" />
                        Sisipkan Gambar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style>
/* Custom CKEditor CSS to match dashboard aesthetic */
.ck-editor {
    width: 100%;
}

.ck-editor__editable_inline {
    min-height: 380px;
    padding: 1.5rem 2rem !important;
}

/* Light / Dark Mode adaptive styling using CSS Variables */
.ck.ck-editor__main>.ck-editor__editable {
    background: var(--color-card) !important;
    color: var(--color-neutral-800) !important;
    border-color: var(--color-sidebar-border) !important;
    border-bottom-left-radius: 0.75rem !important;
    border-bottom-right-radius: 0.75rem !important;
}

.dark .ck.ck-editor__main>.ck-editor__editable {
    color: var(--color-neutral-200) !important;
}

.ck.ck-toolbar {
    background: var(--color-neutral-50) !important;
    border-color: var(--color-sidebar-border) !important;
    border-top-left-radius: 0.75rem !important;
    border-top-right-radius: 0.75rem !important;
    padding: 0.5rem !important;
}

.dark .ck.ck-toolbar {
    background: var(--color-neutral-900) !important;
}

/* Fix dropdowns and button hover states in dark mode */
.dark .ck.ck-button {
    color: var(--color-neutral-300) !important;
}

.dark .ck.ck-button:hover,
.dark .ck.ck-button.ck-on {
    background: var(--color-neutral-800) !important;
    color: var(--color-neutral-100) !important;
}

.dark .ck.ck-dropdown__panel {
    background: var(--color-neutral-950) !important;
    border-color: var(--color-sidebar-border) !important;
}

.dark .ck.ck-list {
    background: var(--color-neutral-950) !important;
}

.dark .ck.ck-list__item .ck-button {
    color: var(--color-neutral-300) !important;
}

.dark .ck.ck-list__item .ck-button:hover {
    background: var(--color-neutral-800) !important;
}

/* Focused editor border */
.ck.ck-editor__editable.ck-focused {
    border-color: var(--color-primary) !important;
    box-shadow: 0 0 0 2px rgba(var(--color-primary-rgb), 0.2) !important;
}
</style>
