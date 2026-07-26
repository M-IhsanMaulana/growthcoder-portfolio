<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
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
    CheckCircle2,
    Copy,
    Check,
    Globe,
    Eye,
    X,
    MessageSquare,
    Filter
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

interface StatusCounts {
    total: number;
    unread: number;
    read: number;
    replied: number;
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
    statusCounts?: StatusCounts;
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
    }, 350);
};

const clearSearch = () => {
    searchQuery.value = '';
    applyFilters();
};

const handleStatusChange = (val: string) => {
    statusQuery.value = val;
    applyFilters();
};

const setStatusFilter = (status: string) => {
    statusQuery.value = status;
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

const copiedEmail = ref(false);
const copyEmail = (email: string) => {
    navigator.clipboard.writeText(email);
    copiedEmail.value = true;
    setTimeout(() => {
        copiedEmail.value = false;
    }, 2000);
};

const copiedMessage = ref(false);
const copyMessage = (text: string) => {
    navigator.clipboard.writeText(text);
    copiedMessage.value = true;
    setTimeout(() => {
        copiedMessage.value = false;
    }, 2000);
};

const getInitials = (name: string) => {
    if (!name) return 'U';
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return parts[0].substring(0, 2).toUpperCase();
};

const getAvatarBgColor = (name: string) => {
    const colors = [
        'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
        'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20',
        'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

const openDetail = (message: ContactMessage) => {
    selectedMessage.value = message;
    sheetOpen.value = true;

    // If message is unread, mark it as read immediately
    if (message.status === 'unread') {
        router.patch(markAsRead(message.id).url, {}, {
            preserveScroll: true,
            onSuccess: () => {
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

const markReplied = (msg?: ContactMessage) => {
    const target = msg || selectedMessage.value;
    if (!target) return;
    isActionLoading.value = true;

    router.patch(markAsReplied(target.id).url, {}, {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedMessage.value && selectedMessage.value.id === target.id) {
                selectedMessage.value.status = 'replied';
            }
            const found = props.messages.data.find(m => m.id === target.id);
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

const openDelete = (message: ContactMessage, e?: Event) => {
    if (e) e.stopPropagation();
    messageToDelete.value = message;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!messageToDelete.value) return;
    isActionLoading.value = true;

    router.delete(destroy(messageToDelete.value.id).url, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
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
            return {
                label: 'Belum Dibaca',
                class: 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border-amber-500/30'
            };
        case 'read':
            return {
                label: 'Dibaca',
                class: 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20'
            };
        case 'replied':
            return {
                label: 'Direspons',
                class: 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30'
            };
    }
};

const counts = computed(() => {
    return props.statusCounts || {
        total: props.messages.total || 0,
        unread: props.messages.data.filter(m => m.status === 'unread').length,
        read: props.messages.data.filter(m => m.status === 'read').length,
        replied: props.messages.data.filter(m => m.status === 'replied').length,
    };
});
</script>

<template>
    <Head title="Inbox Pesan" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ================================================================
             PAGE HEADER
        ================================================================ -->
        <div class="flex flex-col gap-1 border-b border-border/70 pb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Inbox Pesan
                    </h1>
                    <p class="text-sm text-muted-foreground mt-0.5">
                        Kelola pesan masuk dan pertanyaan bisnis yang dikirimkan oleh pengunjung situs portofolio Anda.
                    </p>
                </div>
            </div>
        </div>

        <!-- ================================================================
             STATUS FILTER TABS & TOOLBAR
        ================================================================ -->
        <div class="flex flex-col gap-4">
            <!-- Filter Pills Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <button
                        @click="setStatusFilter('_all')"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 border select-none',
                            statusQuery === '_all'
                                ? 'bg-primary text-white border-primary shadow-2xs'
                                : 'bg-card border-border/80 text-foreground hover:bg-muted/50'
                        ]"
                    >
                        <span>Semua</span>
                        <span
                            :class="[
                                'px-1.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors',
                                statusQuery === '_all'
                                    ? 'bg-white/20 text-white border-transparent'
                                    : 'bg-secondary text-secondary-foreground border-border/60'
                            ]"
                        >
                            {{ counts.total }}
                        </span>
                    </button>

                    <button
                        @click="setStatusFilter('unread')"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 border select-none',
                            statusQuery === 'unread'
                                ? 'bg-amber-600 text-white border-amber-600 shadow-2xs'
                                : 'bg-card border-border/80 text-foreground hover:bg-muted/50'
                        ]"
                    >
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Belum Dibaca</span>
                        </span>
                        <span
                            :class="[
                                'px-1.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors',
                                statusQuery === 'unread'
                                    ? 'bg-white/20 text-white border-transparent'
                                    : 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/20'
                            ]"
                        >
                            {{ counts.unread }}
                        </span>
                    </button>

                    <button
                        @click="setStatusFilter('read')"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 border select-none',
                            statusQuery === 'read'
                                ? 'bg-slate-800 text-white border-slate-800 dark:bg-slate-700 dark:border-slate-700 shadow-2xs'
                                : 'bg-card border-border/80 text-foreground hover:bg-muted/50'
                        ]"
                    >
                        <span>Sudah Dibaca</span>
                        <span
                            :class="[
                                'px-1.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors',
                                statusQuery === 'read'
                                    ? 'bg-white/20 text-white border-transparent'
                                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200/80 dark:border-slate-700/80'
                            ]"
                        >
                            {{ counts.read }}
                        </span>
                    </button>

                    <button
                        @click="setStatusFilter('replied')"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-2 border select-none',
                            statusQuery === 'replied'
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs'
                                : 'bg-card border-border/80 text-foreground hover:bg-muted/50'
                        ]"
                    >
                        <span>Direspons</span>
                        <span
                            :class="[
                                'px-1.5 py-0.5 rounded-full text-[10px] font-bold border transition-colors',
                                statusQuery === 'replied'
                                    ? 'bg-white/20 text-white border-transparent'
                                    : 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/20'
                            ]"
                        >
                            {{ counts.replied }}
                        </span>
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-72">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="inbox-search"
                        v-model="searchQuery"
                        @input="handleSearchInput"
                        placeholder="Cari pengirim, subjek, pesan..."
                        class="pl-9 pr-8 h-9 text-xs bg-card border-border/80 focus-visible:ring-1"
                    />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground p-0.5 rounded-full"
                    >
                        <X class="h-3.5 w-3.5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================
             INBOX LIST (TABLE / CARDS)
        ================================================================ -->
        <div class="rounded-xl border border-border/70 bg-card overflow-hidden shadow-2xs">
            <!-- Table Header -->
            <div class="hidden md:grid grid-cols-[1.4fr_1fr_1.1fr_120px_90px] items-center gap-4 border-b border-border/60 bg-muted/20 px-6 py-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Pengirim & Subjek</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Email</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Diterima</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-center">Status</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground text-right">Aksi</span>
            </div>

            <!-- Empty State -->
            <div
                v-if="messages.data.length === 0"
                class="flex flex-col items-center justify-center gap-3 py-16 text-center border-b border-border/40"
            >
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary mb-1">
                    <Inbox class="h-7 w-7" />
                </div>
                <div>
                    <p class="font-bold text-foreground text-base">
                        {{ searchQuery ? 'Tidak Ada Pesan Ditemukan' : 'Inbox Kosong' }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground max-w-sm mx-auto">
                        {{ searchQuery
                            ? 'Kata kunci pencarian Anda tidak cocok dengan pengirim atau isi pesan manapun.'
                            : 'Belum ada pesan masuk dari pengunjung situs portofolio Anda saat ini.' }}
                    </p>
                </div>
                <Button v-if="searchQuery || statusQuery !== '_all'" @click="searchQuery = ''; setStatusFilter('_all')" variant="outline" size="sm" class="h-8 text-xs mt-2">
                    Bersihkan Filter
                </Button>
            </div>

            <!-- Rows -->
            <div v-else class="divide-y divide-border/60">
                <div
                    v-for="msg in messages.data"
                    :key="msg.id"
                    @click="openDetail(msg)"
                    class="group grid grid-cols-1 md:grid-cols-[1.4fr_1fr_1.1fr_120px_90px] items-start md:items-center gap-3 md:gap-4 px-6 py-3.5 transition-all duration-150 hover:bg-muted/20 cursor-pointer"
                    :class="{
                        'bg-amber-500/[0.03] dark:bg-amber-500/[0.05]': msg.status === 'unread'
                    }"
                >
                    <!-- Column 1: Sender Initials Avatar, Name & Subject -->
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Avatar Initials -->
                        <div class="relative shrink-0">
                            <div
                                :class="[
                                    'flex h-9 w-9 items-center justify-center rounded-lg text-xs font-bold border transition-transform duration-200 group-hover:scale-105',
                                    getAvatarBgColor(msg.name)
                                ]"
                            >
                                {{ getInitials(msg.name) }}
                            </div>
                            <!-- Unread Indicator Dot -->
                            <span v-if="msg.status === 'unread'" class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-amber-500 ring-2 ring-background"></span>
                        </div>

                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xs text-foreground truncate"
                                    :class="{ 'font-bold text-foreground': msg.status === 'unread', 'font-medium text-foreground/90': msg.status !== 'unread' }"
                                >
                                    {{ msg.name }}
                                </span>
                            </div>
                            <span class="text-[11px] text-muted-foreground truncate font-normal">
                                {{ msg.subject || '(Tanpa Subjek)' }}
                            </span>
                        </div>
                    </div>

                    <!-- Column 2: Email -->
                    <div class="text-xs text-muted-foreground truncate font-mono">
                        {{ msg.email }}
                    </div>

                    <!-- Column 3: Received Date -->
                    <div class="text-xs text-muted-foreground flex items-center gap-1.5">
                        <Calendar class="h-3.5 w-3.5 shrink-0 text-muted-foreground/70" />
                        <span>{{ formatDate(msg.created_at) }}</span>
                    </div>

                    <!-- Column 4: Status Badge -->
                    <div class="flex md:justify-center">
                        <span
                            :class="[
                                'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                getStatusBadge(msg.status).class
                            ]"
                        >
                            <span v-if="msg.status === 'unread'" class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <CheckCircle2 v-else-if="msg.status === 'replied'" class="h-3 w-3 text-emerald-500" />
                            <span>{{ getStatusBadge(msg.status).label }}</span>
                        </span>
                    </div>

                    <!-- Column 5: Actions -->
                    <div class="flex items-center justify-end gap-1" @click.stop>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-primary hover:bg-primary/10"
                            title="Buka Pesan"
                            @click="openDetail(msg)"
                        >
                            <Eye class="h-4 w-4" />
                        </Button>
                        <Button
                            v-if="msg.status !== 'replied'"
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-emerald-600 hover:bg-emerald-500/10"
                            title="Tandai Sudah Direspons"
                            @click="markReplied(msg)"
                        >
                            <CheckSquare class="h-4 w-4" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                            title="Hapus Pesan"
                            @click="openDelete(msg, $event)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- ================================================================
                 PAGINATION CONTROLS
            ================================================================ -->
            <div
                v-if="messages.total > 0"
                class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-border/70 bg-card p-4"
            >
                <div class="text-xs text-muted-foreground">
                    Menampilkan <span class="font-bold text-foreground">{{ messages.from ?? 0 }}</span> - <span class="font-bold text-foreground">{{ messages.to ?? 0 }}</span> dari <span class="font-bold text-foreground">{{ messages.total }}</span> pesan
                </div>

                <div v-if="messages.last_page > 1" class="flex items-center gap-1">
                    <Button
                        v-for="(link, i) in messages.links"
                        :key="i"
                        as-child
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        :disabled="!link.url"
                        :class="[
                            'h-8 min-w-[32px] px-2 text-xs font-semibold border-border/70',
                            link.active ? 'bg-primary text-white border-primary shadow-2xs' : 'text-foreground hover:bg-muted'
                        ]"
                    >
                        <Link v-if="link.url" :href="link.url" preserve-scroll>
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else v-html="link.label"></span>
                    </Button>
                </div>
            </div>
        </div>

        <!-- ================================================================
             DETAIL SLIDE-OVER SHEET
        ================================================================ -->
        <Sheet v-model:open="sheetOpen">
            <SheetContent class="w-full sm:max-w-lg md:max-w-xl flex flex-col h-full bg-background p-0 gap-0 border-l border-border/70 shadow-2xl">
                <!-- Header -->
                <SheetHeader class="px-6 py-5 border-b border-border/60 bg-muted/10 shrink-0">
                    <div class="flex items-center justify-between gap-3 pr-6">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                v-if="selectedMessage"
                                :class="[
                                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold border',
                                    getAvatarBgColor(selectedMessage.name)
                                ]"
                            >
                                {{ getInitials(selectedMessage.name) }}
                            </div>
                            <div class="min-w-0">
                                <SheetTitle class="text-base font-bold text-foreground truncate">
                                    {{ selectedMessage?.name }}
                                </SheetTitle>
                                <SheetDescription class="text-xs text-muted-foreground truncate">
                                    {{ selectedMessage?.email }}
                                </SheetDescription>
                            </div>
                        </div>
                        <span
                            v-if="selectedMessage"
                            :class="[
                                'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border shrink-0',
                                getStatusBadge(selectedMessage.status).class
                            ]"
                        >
                            <span>{{ getStatusBadge(selectedMessage.status).label }}</span>
                        </span>
                    </div>
                </SheetHeader>

                <!-- Scrollable Body -->
                <div v-if="selectedMessage" class="flex-1 overflow-y-auto p-6 space-y-5">
                    <!-- Subject Card -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-1">
                        <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block">Subjek Pesan</span>
                        <h3 class="text-sm font-bold text-foreground">
                            {{ selectedMessage.subject || '(Tanpa Subjek)' }}
                        </h3>
                    </div>

                    <!-- Sender Specs Card -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-3">
                        <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider block">Informasi Pengirim</span>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 space-y-1">
                                <span class="text-[10px] text-muted-foreground block">Email</span>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="font-semibold text-foreground font-mono text-[11px] truncate">{{ selectedMessage.email }}</span>
                                    <button @click="copyEmail(selectedMessage.email)" class="text-muted-foreground hover:text-foreground p-0.5" title="Salin Email">
                                        <Check v-if="copiedEmail" class="h-3.5 w-3.5 text-emerald-500" />
                                        <Copy v-else class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </div>

                            <div class="rounded-lg border border-border/50 bg-muted/20 p-2.5 space-y-1">
                                <span class="text-[10px] text-muted-foreground block">Waktu Diterima</span>
                                <span class="font-semibold text-foreground block text-[11px]">{{ formatDate(selectedMessage.created_at) }}</span>
                            </div>

                            <div v-if="selectedMessage.sender_ip" class="rounded-lg border border-border/50 bg-muted/20 p-2.5 space-y-1 col-span-2">
                                <span class="text-[10px] text-muted-foreground block">Alamat IP Pengirim</span>
                                <span class="font-semibold font-mono text-foreground text-[11px]">{{ selectedMessage.sender_ip }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content Body -->
                    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Isi Pesan</span>
                            <button @click="copyMessage(selectedMessage.message)" class="text-xs text-primary hover:underline flex items-center gap-1 font-medium">
                                <Check v-if="copiedMessage" class="h-3.5 w-3.5 text-emerald-500" />
                                <Copy v-else class="h-3.5 w-3.5" />
                                <span>{{ copiedMessage ? 'Tersalin' : 'Salin Pesan' }}</span>
                            </button>
                        </div>
                        <div class="p-3.5 rounded-lg border border-border/50 bg-muted/20 text-xs text-foreground leading-relaxed whitespace-pre-wrap font-sans">
                            {{ selectedMessage.message }}
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar -->
                <div v-if="selectedMessage" class="border-t border-border/60 bg-muted/10 p-4 flex items-center justify-between gap-3 shrink-0">
                    <Button
                        @click="openDelete(selectedMessage)"
                        variant="outline"
                        size="sm"
                        class="text-destructive hover:text-destructive hover:bg-destructive/10 border-destructive/20 h-9"
                    >
                        <Trash2 class="mr-1.5 h-4 w-4" />
                        Hapus Pesan
                    </Button>

                    <div class="flex items-center gap-2">
                        <a
                            :href="`mailto:${selectedMessage.email}?subject=Re: ${encodeURIComponent(selectedMessage.subject || '')}`"
                            target="_blank"
                            class="inline-flex items-center justify-center rounded-md text-xs font-semibold h-9 px-3 bg-primary text-white hover:bg-brand-primary-hover shadow-2xs transition-colors gap-1.5"
                        >
                            <Send class="h-3.5 w-3.5" />
                            <span>Balas Email</span>
                        </a>

                        <Button
                            v-if="selectedMessage.status !== 'replied'"
                            @click="markReplied()"
                            :disabled="isActionLoading"
                            variant="outline"
                            size="sm"
                            class="h-9 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 font-medium"
                        >
                            <Loader2 v-if="isActionLoading" class="mr-1.5 h-4 w-4 animate-spin" />
                            <CheckSquare v-else class="mr-1.5 h-4 w-4" />
                            <span>Tandai Direspons</span>
                        </Button>
                    </div>
                </div>
            </SheetContent>
        </Sheet>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-md bg-card border-border">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2 text-red-600">
                        <AlertCircle class="h-5 w-5" />
                        Konfirmasi Hapus Pesan
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus pesan dari <strong>{{ messageToDelete?.name }}</strong> secara permanen?
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="flex gap-2 sm:gap-0">
                    <Button variant="outline" @click="deleteDialogOpen = false" :disabled="isActionLoading">
                        Batal
                    </Button>
                    <Button variant="destructive" @click="confirmDelete" :disabled="isActionLoading">
                        <Loader2 v-if="isActionLoading" class="mr-2 h-4 w-4 animate-spin" />
                        Hapus Pesan
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
