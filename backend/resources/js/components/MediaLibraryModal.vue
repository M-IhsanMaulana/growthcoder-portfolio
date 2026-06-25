<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog';
import MediaManager from '@/components/MediaManager.vue';

// Define Props
interface Props {
    open: boolean;
}

defineProps<Props>();

// Emits
const emit = defineEmits<{
    (e: 'update:open', val: boolean): void;
    (e: 'select', media: any): void;
}>();

const handleSelect = (media: any) => {
    emit('select', media);
    emit('update:open', false);
};

const handleCancel = () => {
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[92vw] lg:max-w-7xl h-[85vh] flex flex-col p-6 overflow-hidden bg-card border-border">
            <DialogHeader class="shrink-0">
                <DialogTitle class="text-xl font-bold">Select Media</DialogTitle>
                <DialogDescription>
                    Choose an existing image from the Media Library or upload a new one.
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-y-auto min-h-0 py-4">
                <MediaManager
                    mode="modal"
                    @select="handleSelect"
                    @cancel="handleCancel"
                />
            </div>
        </DialogContent>
    </Dialog>
</template>
