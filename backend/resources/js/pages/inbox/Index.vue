<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
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
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Search,
    Mail,
    Trash2,
    CheckSquare,
    Loader2,
    Inbox,
    Calendar,
    User,
    ChevronLeft,
    ChevronRight,
    Send,
    AlertCircle,
    CheckCircle2
} from '@lucide/vue';
import { index as inboxIndex, markAsRead, markAsReplied, destroy } from '@/routes/inbox';

// -----------------------------------------------------------------------
// Types
// -----------------------------------------------------------------------
interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject: string | null;
    message: string;
    status: 'unread' | 'read' | 'replied';
    sender_ip: string | null;
    telegram_notified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedMessages {
    data: ContactMessage[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    from: number | null;
    to: number | null;
}

// -----------------------------------------------------------------------
// Props & Page Layout
// -----------------------------------------------------------------------
const props = defineProps<{
    messages: PaginatedMessages;
    filters: {
        search?: string;
        status?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Inbox',
                href: inboxIndex(),
            },
        ],
    },
});

// -----------------------------------------------------------------------
// Search & Filter
// -----------------------------------------------------------------------
const searchQuery = ref(props.filters.search ?? '');
const statusQuery = ref(props.filters.status ?? '_all');

let debounceTimeout: any = null;

const handleSearchInput = () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
};

const handleStatusChange = (val: string) => {
    statusQuery.value = val;
    applyFilters();
};

const applyFilters = () => {
    const query: any = {};
    if (searchQuery.value) {
        query.search = searchQuery.value;
    }
    if (statusQuery.value && statusQuery.value !== '_all') {
        query.status = statusQuery.value;
    }

    router.get(inboxIndex().url, query, {
        preserveState: true,
        replace: true,
    });
};

// -----------------------------------------------------------------------
// Detail Sheet (Slide-out)
// -----------------------------------------------------------------------
const sheetOpen = ref(false);
const selectedMessage = ref<ContactMessage | null>(null);

const openDetail = (message: ContactMessage) => {
    selectedMessage.value = message;
    sheetOpen.value = true;

    // If message is unread, mark it as read immediately
    if (message.status === 'unread') {
        router.patch(markAsRead(message.id).url, {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Update local status so the UI re-renders instantly
                if (selectedMessage.value && selectedMessage.value.id === message.id) {
                    selectedMessage.value.status = 'read';
                }
                const found = props.messages.data.find(m => m.id === message.id);
                if (found) {
                    found.status = 'read';
                }
            }
        });
    }
};

// -----------------------------------------------------------------------
// Actions (Mark as Replied / Delete)
// -----------------------------------------------------------------------
const isActionLoading = ref(false);

const markReplied = () => {
    if (!selectedMessage.value) return;
    isActionLoading.value = true;

    router.patch(markAsReplied(selectedMessage.value.id).url, {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedMessage.value) {
                selectedMessage.value.status = 'replied';
            }
            const found = props.messages.data.find(m => m.id === selectedMessage.value?.id);
            if (found) {
                found.status = 'replied';
            }
            isActionLoading.value = false;
        },
        onError: () => {
            isActionLoading.value = false;
        }
    });
};

const deleteDialogOpen = ref(false);
const messageToDelete = ref<ContactMessage | null>(null);

const openDelete = (message: ContactMessage) => {
    messageToDelete.value = message;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!messageToDelete.value) return;
    isActionLoading.value = true;

    router.delete(destroy(messageToDelete.value.id).url, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
            // If the deleted message was currently open in the sheet, close the sheet
            if (selectedMessage.value?.id === messageToDelete.value?.id) {
                sheetOpen.value = false;
                selectedMessage.value = null;
            }
            messageToDelete.value = null;
            isActionLoading.value = false;
        },
        onError: () => {
            isActionLoading.value = false;
        }
    });
};

// -----------------------------------------------------------------------
// Helper Formatter
// -----------------------------------------------------------------------
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadge = (status: 'unread' | 'read' | 'replied') => {
    switch (status) {
        case 'unread':
            return { label: 'Belum Dibaca', variant: 'destructive' as const };
        case 'read':
            return { label: 'Dibaca', variant: 'secondary' as const };
        case 'replied':
            return { label: 'Direspons', variant: 'outline' as const };
    }
};

const unreadCount = computed(() => {
    return props.messages.data.filter(m => m.status === 'unread').length;
});
</script>

<template>
    <Head title="Inbox Pesan" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-bold tracking-tight text-foreground">
                Inbox Pesan
            </h1>
            <p class="text-sm text-muted-foreground">
                Kelola pesan masuk dan pertanyaan bisnis yang dikirimkan oleh pengunjung situs portofolio Anda.
            </p>
        </div>

        <!-- ================================================================
             TOOLBAR
        ================================================================ -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search & Filters -->
            <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                <!-- Search input -->
                <div class="relative max-w-sm flex-1">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="inbox-search"
                        v-model="searchQuery"
                        @input="handleSearchInput"
                        placeholder="Cari pengirim, subjek, atau pesan..."
                        class="pl-9"
                    />
                </div>

                <!-- Status Filter Dropdown -->
                <div class="w-full sm:w-[180px]">
                    <Select :model-value="statusQuery" @update:model-value="handleStatusChange">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="_all">Semua Status</SelectItem>
                            <SelectItem value="unread">Belum Dibaca</SelectItem>
                            <SelectItem value="read">Sudah Dibaca</SelectItem>
                            <SelectItem value="replied">Direspons</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Stats Badge -->
            <div class="flex items-center gap-2 shrink-0">
                <Badge variant="outline" class="gap-1.5 text-muted-foreground">
                    {{ messages.total }} Total Pesan
                </Badge>
            </div>
        </div>

        <!-- ================================================================
             INBOX LIST (GRID TABLE)
        ================================================================ -->
        <div class="rounded-xl border border-sidebar-border/70 bg-card overflow-hidden shadow-sm">
            <!-- Table Header -->
            <div class="hidden md:grid grid-cols-[1.2fr_1fr_1.1fr_120px_90px] items-center gap-4 border-b border-sidebar-border/50 bg-muted/30 px-6 py-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Pengirim & Subjek</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Email</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Diterima</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-center">Status</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
            </div>

            <!-- Empty State -->
            <div
                v-if="messages.data.length === 0"
                class="flex flex-col items-center justify-center gap-4 py-20 text-center"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-muted/50 text-muted-foreground">
                    <Inbox class="h-8 w-8 text-neutral-400" />
                </div>
                <div>
                    <p class="font-semibold text-foreground text-lg">
                        {{ searchQuery ? 'Tidak ada pesan ditemukan' : 'Inbox Kosong' }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground max-w-sm mx-auto">
                        {{ searchQuery
                            ? 'Kata kunci Anda tidak cocok dengan pengirim, subjek, atau pesan mana pun.'
                            : 'Selamat! Semua komunikasi terkendali. Belum ada pesan masuk saat ini.' }}
                    </p>
                </div>
            </div>

            <!-- Rows -->
            <div v-else class="divide-y divide-sidebar-border/30">
                <div
                    v-for="msg in messages.data"
                    :key="msg.id"
                    @click="openDetail(msg)"
                    class="grid grid-cols-1 md:grid-cols-[1.2fr_1fr_1.1fr_120px_90px] items-start md:items-center gap-3 md:gap-4 px-6 py-4 transition-all duration-150 hover:bg-muted/30 cursor-pointer"
                    :class="{
                        'bg-primary/[0.02] border-l-2 border-primary': msg.status === 'unread'
                    }"
                >
                    <!-- Column 1: Sender Name & Subject -->
                    <div class="flex flex-col min-w-0">
                        <span
                            class="text-sm text-foreground truncate"
                            :class="{ 'font-semibold': msg.status === 'unread' }"
                        >
                            {{ msg.name }}
                        </span>
                        <span class="text-xs text-muted-foreground truncate mt-0.5">
                            {{ msg.subject || '(Tanpa Subjek)' }}
                        </span>
                    </div>

                    <!-- Column 2: Email -->
                    <div class="text-sm text-muted-foreground md:truncate">
                        {{ msg.email }}
                    </div>

                    <!-- Column 3: Received Date -->
                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                        <Calendar class="h-3.5 w-3.5 shrink-0" />
                        {{ formatDate(msg.created_at) }}
                    </div>

                    <!-- Column 4: Status Badge -->
                    <div class="flex md:justify-center">
                        <Badge
                            :variant="getStatusBadge(msg.status).variant"
                            class="text-[10px] uppercase font-semibold tracking-wider shrink-0"
                            :class="{
                                'bg-green-100 text-green-800 border-green-200 dark:bg-green-950/30 dark:text-green-400': msg.status === 'replied'
                            }"
                        >
                            {{ getStatusBadge(msg.status).label }}
                        </Badge>
                    </div>

                    <!-- Column 5: Action (Trash / View) -->
                    <div class="flex items-center justify-end gap-2" @click.stop>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                            title="Hapus Pesan"
                            @click="openDelete(msg)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 PAGINATION
            ================================================================ -->
            <div
                v-if="messages.last_page > 1"
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-t border-sidebar-border/30 bg-muted/10 px-6 py-4"
            >
                <span class="text-xs text-muted-foreground">
                    Menampilkan {{ messages.from ?? 0 }} - {{ messages.to ?? 0 }} dari {{ messages.total }} pesan
                </span>

                <div class="flex items-center gap-1">
                    <Button
                        v-for="(link, i) in messages.links"
                        :key="i"
                        as-child
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        :disabled="!link.url"
                        class="h-8 min-w-[32px] px-2"
                    >
                        <component
                            :is="link.url ? 'Link' : 'span'"
                            :href="link.url || undefined"
                            v-html="link.label"
                            preserve-state
                            :class="{ 'opacity-50 pointer-events-none': !link.url }"
                        />
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================
         DETAIL SLIDE-OUT PANEL (SHEET)
    ================================================================ -->
    <Sheet v-model:open="sheetOpen">
        <SheetContent class="w-full sm:max-w-lg flex flex-col h-full bg-background">
            <SheetHeader class="border-b border-sidebar-border/50 pb-4 shrink-0">
                <div class="flex items-center gap-2 mb-2">
                    <Badge
                        v-if="selectedMessage"
                        :variant="getStatusBadge(selectedMessage.status).variant"
                        class="text-[10px] uppercase tracking-wider"
                        :class="{
                            'bg-green-100 text-green-800 border-green-200 dark:bg-green-950/30 dark:text-green-400': selectedMessage.status === 'replied'
                        }"
                    >
                        {{ getStatusBadge(selectedMessage.status).label }}
                    </Badge>
                </div>
                <SheetTitle class="text-lg font-bold text-foreground truncate">
                    Detail Pesan
                </SheetTitle>
                <SheetDescription class="text-xs text-muted-foreground">
                    Tinjau informasi detail pengirim dan isi pesan.
                </SheetDescription>
            </SheetHeader>

            <div v-if="selectedMessage" class="flex-1 overflow-y-auto py-6 space-y-6">
                <!-- Info Section -->
                <div class="space-y-4">
                    <!-- Sender Name -->
                    <div class="flex gap-3 items-start">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <User class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs text-muted-foreground">Pengirim</span>
                            <h4 class="text-sm font-semibold text-foreground break-words mt-0.5">
                                {{ selectedMessage.name }}
                            </h4>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="flex gap-3 items-start">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Mail class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs text-muted-foreground">Email</span>
                            <div class="mt-0.5 flex items-center gap-1.5">
                                <a
                                    :href="`mailto:${selectedMessage.email}`"
                                    class="text-sm font-semibold text-primary hover:underline break-all"
                                >
                                    {{ selectedMessage.email }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Date Received -->
                    <div class="flex gap-3 items-start">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <Calendar class="h-5 w-5" />
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">Diterima</span>
                            <h4 class="text-sm font-semibold text-foreground mt-0.5">
                                {{ formatDate(selectedMessage.created_at) }}
                            </h4>
                        </div>
                    </div>

                    <!-- IP Address -->
                    <div class="flex gap-3 items-start">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <AlertCircle class="h-5 w-5" />
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">IP Pengirim</span>
                            <h4 class="text-sm font-semibold text-muted-foreground mt-0.5">
                                {{ selectedMessage.sender_ip || 'Tidak Diketahui' }}
                            </h4>
                        </div>
                    </div>

                    <!-- Telegram Status -->
                    <div class="flex gap-3 items-start">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                            :class="selectedMessage.telegram_notified_at ? 'bg-green-100 text-green-600 dark:bg-green-950/20' : 'bg-neutral-100 text-neutral-400 dark:bg-neutral-900/30'"
                        >
                            <Send class="h-4 w-4" />
                        </div>
                        <div>
                            <span class="text-xs text-muted-foreground">Notifikasi Telegram</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <CheckCircle2 v-if="selectedMessage.telegram_notified_at" class="h-4 w-4 text-green-600" />
                                <AlertCircle v-else class="h-4 w-4 text-neutral-400" />
                                <span
                                    class="text-xs font-medium"
                                    :class="selectedMessage.telegram_notified_at ? 'text-green-600' : 'text-neutral-500'"
                                >
                                    {{ selectedMessage.telegram_notified_at ? `Terkirim (${formatDate(selectedMessage.telegram_notified_at)})` : 'Tidak Terkirim (Muted / Skipped)' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-sidebar-border/50 my-6"></div>

                <!-- Message Content Section -->
                <div class="space-y-2">
                    <span class="text-xs text-muted-foreground">Subjek</span>
                    <h3 class="text-base font-bold text-foreground">
                        {{ selectedMessage.subject || '(Tanpa Subjek)' }}
                    </h3>

                    <div class="mt-4">
                        <span class="text-xs text-muted-foreground block mb-2">Isi Pesan</span>
                        <div class="rounded-lg border border-sidebar-border/50 bg-muted/30 p-4 font-sans text-sm leading-relaxed text-foreground whitespace-pre-wrap max-h-[350px] overflow-y-auto">
                            {{ selectedMessage.message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sheet Footer Actions -->
            <div v-if="selectedMessage" class="border-t border-sidebar-border/50 pt-4 flex items-center justify-between gap-3 shrink-0">
                <Button
                    variant="outline"
                    class="text-muted-foreground hover:text-destructive hover:bg-destructive/10 border-transparent hover:border-destructive/20"
                    @click="openDelete(selectedMessage)"
                    :disabled="isActionLoading"
                >
                    <Trash2 class="h-4 w-4 mr-2" />
                    Hapus
                </Button>

                <div class="flex items-center gap-2">
                    <Button
                        v-if="selectedMessage.status !== 'replied'"
                        @click="markReplied"
                        class="gap-2 bg-green-600 hover:bg-green-700 text-white dark:bg-green-700 dark:hover:bg-green-800"
                        :disabled="isActionLoading"
                    >
                        <Loader2 v-if="isActionLoading" class="h-4 w-4 animate-spin" />
                        <CheckSquare v-else class="h-4 w-4" />
                        Tandai Direspons
                    </Button>
                    <Button
                        variant="secondary"
                        @click="sheetOpen = false"
                    >
                        Tutup
                    </Button>
                </div>
            </div>
        </SheetContent>
    </Sheet>

    <!-- ================================================================
         DELETE CONFIRMATION DIALOG
    ================================================================ -->
    <Dialog v-model:open="deleteDialogOpen">
        <DialogContent class="sm:max-w-md bg-background">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus Pesan</DialogTitle>
                <DialogDescription class="text-sm text-muted-foreground mt-2">
                    Apakah Anda yakin ingin menghapus pesan dari <strong class="text-foreground">{{ messageToDelete?.name }}</strong>? Tindakan ini bersifat permanen dan data pesan akan dihapus dari database.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="mt-4 gap-2 sm:gap-0">
                <Button
                    variant="outline"
                    @click="deleteDialogOpen = false"
                    :disabled="isActionLoading"
                >
                    Batal
                </Button>
                <Button
                    variant="destructive"
                    @click="confirmDelete"
                    :disabled="isActionLoading"
                >
                    <Loader2 v-if="isActionLoading" class="h-4 w-4 animate-spin mr-2" />
                    <Trash2 v-else class="h-4 w-4 mr-2" />
                    Ya, Hapus Permanen
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
