<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    LayoutGrid,
    FolderGit2,
    FileText,
    Mail,
    Layers,
    PlusCircle,
    Settings,
    ArrowRight,
    TrendingUp,
    Users,
    Monitor,
    Tablet,
    Smartphone,
    Share2,
    BookOpen,
    Eye,
    Globe,
    CheckCircle2,
    Calendar,
    User,
    ArrowUpRight,
    ExternalLink
} from '@lucide/vue';
import { Chart, registerables } from 'chart.js';
import { dashboard } from '@/routes';
import { index as projectsIndex, create as projectsCreate } from '@/routes/projects';
import { index as postsIndex, create as postsCreate } from '@/routes/posts';
import { index as inboxIndex, markAsRead } from '@/routes/inbox';
import { edit as globalSettingsEdit } from '@/routes/global-settings';
import { index as servicesIndex } from '@/routes/services';

// Register Chart.js components
Chart.register(...registerables);

interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject: string | null;
    message: string;
    status: 'unread' | 'read' | 'replied';
    created_at: string;
}

const props = defineProps<{
    stats: {
        total_projects: number;
        featured_projects: number;
        total_posts: number;
        published_posts: number;
        draft_posts: number;
        total_services: number;
        active_services: number;
        total_messages: number;
        unread_messages: number;
        total_media: number;
        total_blog_views: number;
        views_over_time: {
            labels: string[];
            values: number[];
        };
        device_share: {
            desktop: number;
            mobile: number;
            tablet: number;
        };
        top_referrers: Array<{
            referrer: string;
            count: number;
        }>;
        recent_messages: ContactMessage[];
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Muhammad Ihsan Maulana');

// Canvas chart references
const viewsChartCanvas = ref<HTMLCanvasElement | null>(null);
const deviceChartCanvas = ref<HTMLCanvasElement | null>(null);
let viewsChart: Chart | null = null;
let deviceChart: Chart | null = null;

onMounted(() => {
    // 1. Line Chart: Views Over Time
    if (viewsChartCanvas.value) {
        viewsChart = new Chart(viewsChartCanvas.value, {
            type: 'line',
            data: {
                labels: props.stats.views_over_time.labels,
                datasets: [{
                    label: 'Page Views',
                    data: props.stats.views_over_time.values,
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4f46e5',
                    pointHoverRadius: 6,
                    pointRadius: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(156, 163, 175, 0.05)' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart: Device Share
    if (deviceChartCanvas.value) {
        const deviceData = [
            props.stats.device_share.desktop,
            props.stats.device_share.mobile,
            props.stats.device_share.tablet
        ];

        deviceChart = new Chart(deviceChartCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablet'],
                datasets: [{
                    data: deviceData,
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b'], // Indigo, Emerald, Amber
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#9ca3af',
                            font: { size: 11 },
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        padding: 10,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    }
                },
                cutout: '70%'
            }
        });
    }
});

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getInitials = (name: string) => {
    if (!name) return '?';
    return name
        .trim()
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

const getStatusBadge = (status: 'unread' | 'read' | 'replied') => {
    switch (status) {
        case 'unread':
            return { label: 'Baru', variant: 'destructive' as const };
        case 'read':
            return { label: 'Dibaca', variant: 'secondary' as const };
        case 'replied':
            return { label: 'Direspons', variant: 'outline' as const };
    }
};

const markMessageAsRead = (id: number) => {
    router.patch(markAsRead(id).url, {}, {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="flex flex-1 flex-col gap-6 p-6">
        <!-- ==========================================
             WELCOME HEADER
             ========================================== -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-card p-6 rounded-2xl border border-border/70 shadow-2xs relative overflow-hidden">
            <div class="space-y-1 relative z-10">
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Selamat Datang Kembali, <span class="text-primary font-extrabold">{{ userName }}</span>! 👋
                </h1>
                <p class="text-sm text-muted-foreground">
                    Kelola portofolio, studi kasus, blog, dan inbox kontak Anda dengan mudah dari panel kendali terpadu ini.
                </p>
            </div>
            <div class="flex items-center gap-3 relative z-10">
                <a 
                    href="/" 
                    target="_blank" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-border bg-background hover:bg-muted/50 transition duration-200 text-foreground cursor-pointer shadow-2xs hover:-translate-y-0.5"
                >
                    <Globe class="h-4 w-4 text-primary" />
                    Lihat Web Publik
                    <ExternalLink class="h-3.5 w-3.5 text-muted-foreground" />
                </a>
            </div>
            <!-- Decorative Subtle Gradient Orb -->
            <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-primary/10 blur-3xl"></div>
        </div>

        <!-- ==========================================
             KPI STATS CARDS GRID
             ========================================== -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Projects Card -->
            <Link 
                :href="projectsIndex()"
                class="group flex flex-col justify-between p-5 rounded-2xl border border-border/70 border-t-4 border-t-indigo-500 bg-card hover:border-indigo-500/40 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Studi Kasus Proyek</span>
                        <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground">
                            {{ stats.total_projects }}
                        </h2>
                    </div>
                    <div class="p-3 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition duration-200">
                        <FolderGit2 class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-border/50 flex items-center justify-between text-xs text-muted-foreground">
                    <span class="font-medium text-foreground/80">{{ stats.featured_projects }} Unggulan / Utama</span>
                    <ArrowRight class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition duration-200 text-indigo-500" />
                </div>
            </Link>

            <!-- Blog Posts Card -->
            <Link 
                :href="postsIndex()"
                class="group flex flex-col justify-between p-5 rounded-2xl border border-border/70 border-t-4 border-t-emerald-500 bg-card hover:border-emerald-500/40 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Artikel Blog</span>
                        <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground">
                            {{ stats.total_posts }}
                        </h2>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition duration-200">
                        <FileText class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-border/50 flex items-center justify-between text-xs text-muted-foreground">
                    <span class="font-medium text-foreground/80">{{ stats.published_posts }} Rilis · {{ stats.draft_posts }} Draf</span>
                    <ArrowRight class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition duration-200 text-emerald-500" />
                </div>
            </Link>

            <!-- Inbox Messages Card -->
            <Link 
                :href="inboxIndex()"
                class="group flex flex-col justify-between p-5 rounded-2xl border border-border/70 border-t-4 border-t-rose-500 bg-card hover:-translate-y-0.5 hover:shadow-md transition-all duration-200"
                :class="stats.unread_messages > 0 ? 'bg-rose-500/5 dark:bg-rose-950/10' : ''"
            >
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Inbox Pesan</span>
                        <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground" :class="{ 'text-rose-600 dark:text-rose-400': stats.unread_messages > 0 }">
                            {{ stats.total_messages }}
                        </h2>
                    </div>
                    <div 
                        class="p-3 rounded-xl transition duration-200 group-hover:scale-110"
                        :class="stats.unread_messages > 0 ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400 animate-pulse' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'"
                    >
                        <Mail class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-border/50 flex items-center justify-between text-xs text-muted-foreground">
                    <span :class="{ 'font-semibold text-rose-600 dark:text-rose-400': stats.unread_messages > 0 }">
                        {{ stats.unread_messages }} pesan belum dibaca
                    </span>
                    <ArrowRight class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition duration-200 text-rose-500" />
                </div>
            </Link>

            <!-- Services & Media Card -->
            <Link 
                :href="servicesIndex()"
                class="group flex flex-col justify-between p-5 rounded-2xl border border-border/70 border-t-4 border-t-amber-500 bg-card hover:border-amber-500/40 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200"
            >
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Media & Layanan</span>
                        <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground">
                            {{ stats.total_media }}
                        </h2>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition duration-200">
                        <Layers class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-border/50 flex items-center justify-between text-xs text-muted-foreground">
                    <span class="font-medium text-foreground/80">{{ stats.total_services }} Layanan ({{ stats.active_services }} Aktif)</span>
                    <ArrowRight class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition duration-200 text-amber-500" />
                </div>
            </Link>
        </div>

        <!-- ==========================================
             ANALYTICS GRAPHS SECTION
             ========================================== -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Views Over Time Chart (Spans 2 columns on lg) -->
            <div class="lg:col-span-2 bg-card border border-border/70 rounded-2xl p-6 shadow-2xs flex flex-col justify-between h-[360px]">
                <div class="flex items-center justify-between border-b border-border/50 pb-3 mb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <TrendingUp class="h-4.5 w-4.5" />
                        </div>
                        <h3 class="font-bold text-sm text-foreground">
                            Grafik Kunjungan Artikel (30 Hari Terakhir)
                        </h3>
                    </div>
                    <div class="text-xs font-medium text-muted-foreground bg-muted/40 px-3 py-1 rounded-lg">
                        Total Views: <span class="font-bold text-primary">{{ stats.total_blog_views }}</span>
                    </div>
                </div>
                <div class="flex-1 relative mt-2">
                    <canvas ref="viewsChartCanvas"></canvas>
                </div>
            </div>

            <!-- Device Share Doughnut (Spans 1 column) -->
            <div class="bg-card border border-border/70 rounded-2xl p-6 shadow-2xs flex flex-col justify-between h-[360px]">
                <div class="flex items-center gap-2.5 border-b border-border/50 pb-3 mb-2">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <Monitor class="h-4.5 w-4.5" />
                    </div>
                    <h3 class="font-bold text-sm text-foreground">
                        Persentase Perangkat
                    </h3>
                </div>
                <div class="flex-1 relative flex items-center justify-center py-2">
                    <canvas ref="deviceChartCanvas"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-1 text-center text-[10px] mt-2 border-t border-border/50 pt-3">
                    <div class="flex flex-col items-center">
                        <span class="text-muted-foreground flex items-center justify-center gap-1">
                            <Monitor class="h-3 w-3 text-indigo-500" /> Desktop
                        </span>
                        <span class="font-semibold text-foreground text-xs mt-0.5">{{ stats.device_share.desktop }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-muted-foreground flex items-center justify-center gap-1">
                            <Smartphone class="h-3 w-3 text-emerald-500" /> Mobile
                        </span>
                        <span class="font-semibold text-foreground text-xs mt-0.5">{{ stats.device_share.mobile }}</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-muted-foreground flex items-center justify-center gap-1">
                            <Tablet class="h-3 w-3 text-amber-500" /> Tablet
                        </span>
                        <span class="font-semibold text-foreground text-xs mt-0.5">{{ stats.device_share.tablet }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             RECENT ACTIVITY & QUICK ACTIONS
             ========================================== -->
        <div class="grid gap-6 lg:grid-cols-3 items-start">
            <!-- Recent Contact Messages List (Spans 2 columns) -->
            <div class="lg:col-span-2 bg-card border border-border/70 rounded-2xl p-6 shadow-2xs flex flex-col gap-4">
                <div class="border-b border-border/50 pb-3.5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-primary/10 dark:bg-primary/20 text-primary">
                            <Mail class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-foreground tracking-tight">Pesan Masuk Terbaru</h3>
                            <p class="text-xs text-muted-foreground">Pesan yang baru saja dikirim oleh pengunjung melalui formulir kontak</p>
                        </div>
                    </div>
                    <Link 
                        :href="inboxIndex()" 
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary/80 bg-primary/5 hover:bg-primary/10 dark:bg-primary/15 dark:hover:bg-primary/25 px-3 py-1.5 rounded-xl transition duration-200 shrink-0"
                    >
                        Lihat Semua Inbox
                        <ArrowUpRight class="h-3.5 w-3.5" />
                    </Link>
                </div>

                <div v-if="stats.recent_messages.length > 0" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-border/60 text-muted-foreground/80 font-bold uppercase tracking-wider text-[11px] bg-slate-50/80 dark:bg-neutral-900/60">
                                <th class="py-2.5 px-3.5 rounded-l-xl">Pengirim</th>
                                <th class="py-2.5 px-3.5">Subjek & Pesan</th>
                                <th class="py-2.5 px-3.5 text-center">Waktu</th>
                                <th class="py-2.5 px-3.5 text-center">Status</th>
                                <th class="py-2.5 px-3.5 text-right rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/40 font-medium">
                            <tr 
                                v-for="msg in stats.recent_messages" 
                                :key="msg.id" 
                                class="hover:bg-muted/30 transition-colors group"
                            >
                                <td class="py-3 px-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200/80 dark:border-neutral-700/70">
                                            {{ getInitials(msg.name) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-foreground truncate max-w-[130px] group-hover:text-primary transition-colors">{{ msg.name }}</div>
                                            <div class="text-[11px] text-muted-foreground truncate max-w-[140px]">{{ msg.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 max-w-[240px]">
                                    <div class="font-semibold text-foreground truncate text-xs">{{ msg.subject || 'Tanpa Subjek' }}</div>
                                    <div class="text-muted-foreground truncate text-[11px] mt-0.5 line-clamp-1">{{ msg.message }}</div>
                                </td>
                                <td class="py-3 px-3.5 text-center text-muted-foreground text-[11px]">
                                    <span class="inline-flex items-center justify-center gap-1 text-slate-500 dark:text-neutral-400 font-mono text-[11px]">
                                        <Calendar class="h-3 w-3 text-slate-400 dark:text-neutral-500" />
                                        {{ formatDate(msg.created_at) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-center">
                                    <span 
                                        v-if="msg.status === 'unread'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/70 dark:border-rose-800/40"
                                    >
                                        <span class="size-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        Baru
                                    </span>
                                    <span 
                                        v-else-if="msg.status === 'read'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800/40"
                                    >
                                        <span class="size-1.5 rounded-full bg-indigo-500"></span>
                                        Dibaca
                                    </span>
                                    <span 
                                        v-else-if="msg.status === 'replied'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/70 dark:border-emerald-800/40"
                                    >
                                        <CheckCircle2 class="size-3 text-emerald-500" />
                                        Direspons
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button 
                                            v-if="msg.status === 'unread'"
                                            @click="markMessageAsRead(msg.id)"
                                            title="Tandai telah dibaca"
                                            class="p-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 transition-all cursor-pointer"
                                        >
                                            <CheckCircle2 class="h-4 w-4" />
                                        </button>
                                        <Link 
                                            :href="inboxIndex()"
                                            title="Buka Inbox Detail"
                                            class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-all"
                                        >
                                            <ArrowUpRight class="h-4 w-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="flex flex-col items-center justify-center p-12 text-center border border-dashed border-border/80 rounded-2xl bg-muted/10">
                    <div class="p-3 rounded-full bg-muted/20 text-muted-foreground/60 mb-3">
                        <Mail class="h-6 w-6" />
                    </div>
                    <p class="text-sm font-medium text-foreground mb-1">Belum ada pesan masuk</p>
                    <p class="text-xs text-muted-foreground">Pesan yang dikirim melalui formulir kontak akan tampil di sini.</p>
                </div>
            </div>

            <!-- Quick Actions & Top Referrers -->
            <div class="space-y-6">
                <!-- Quick Actions Card -->
                <div class="bg-card border border-border/70 rounded-2xl p-6 shadow-2xs">
                    <h3 class="font-bold text-sm text-foreground mb-4 border-b border-border/50 pb-3 flex items-center gap-2.5">
                        <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                            <PlusCircle class="h-4 w-4" />
                        </div>
                        Aksi Cepat
                    </h3>
                    <div class="grid grid-cols-2 gap-2.5">
                        <Link 
                            :href="postsCreate()"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-border/70 bg-muted/20 hover:border-emerald-500/40 hover:bg-emerald-500/5 hover:-translate-y-0.5 transition duration-200 gap-2 cursor-pointer text-center group"
                        >
                            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition duration-200">
                                <FileText class="h-5 w-5" />
                            </div>
                            <span class="text-xs font-semibold text-foreground">Tulis Artikel</span>
                        </Link>
                        <Link 
                            :href="projectsCreate()"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-border/70 bg-muted/20 hover:border-indigo-500/40 hover:bg-indigo-500/5 hover:-translate-y-0.5 transition duration-200 gap-2 cursor-pointer text-center group"
                        >
                            <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition duration-200">
                                <FolderGit2 class="h-5 w-5" />
                            </div>
                            <span class="text-xs font-semibold text-foreground">Tambah Proyek</span>
                        </Link>
                        <Link 
                            :href="inboxIndex()"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-border/70 bg-muted/20 hover:border-rose-500/40 hover:bg-rose-500/5 hover:-translate-y-0.5 transition duration-200 gap-2 cursor-pointer text-center group"
                        >
                            <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 group-hover:scale-110 transition duration-200">
                                <Mail class="h-5 w-5" />
                            </div>
                            <span class="text-xs font-semibold text-foreground">Buka Inbox</span>
                        </Link>
                        <Link 
                            :href="globalSettingsEdit()"
                            class="flex flex-col items-center justify-center p-4 rounded-xl border border-border/70 bg-muted/20 hover:border-amber-500/40 hover:bg-amber-500/5 hover:-translate-y-0.5 transition duration-200 gap-2 cursor-pointer text-center group"
                        >
                            <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition duration-200">
                                <Settings class="h-5 w-5" />
                            </div>
                            <span class="text-xs font-semibold text-foreground">Pengaturan</span>
                        </Link>
                    </div>
                </div>

                <!-- Top Referrers Card -->
                <div class="bg-card border border-border/70 rounded-2xl p-6 shadow-2xs">
                    <h3 class="font-bold text-sm text-foreground mb-4 border-b border-border/50 pb-3 flex items-center gap-2.5">
                        <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                            <Share2 class="h-4 w-4" />
                        </div>
                        Sumber Rujukan (Referrer)
                    </h3>
                    <div v-if="stats.top_referrers.length > 0" class="space-y-3">
                        <div 
                            v-for="(refSource, idx) in stats.top_referrers" 
                            :key="refSource.referrer"
                            class="flex items-center justify-between text-xs border-b border-border/30 pb-2.5 last:border-0 last:pb-0"
                        >
                            <div class="flex items-center gap-2 max-w-[170px] truncate">
                                <span class="font-mono text-muted-foreground w-4 text-[10px]">{{ idx + 1 }}.</span>
                                <span v-if="refSource.referrer === 'direct'" class="text-primary font-semibold flex items-center gap-1">
                                    <Globe class="h-3.5 w-3.5" /> Direct / Langsung
                                </span>
                                <span v-else class="text-foreground font-medium truncate">{{ refSource.referrer }}</span>
                            </div>
                            <span class="font-bold text-foreground bg-muted/60 px-2.5 py-0.5 rounded-md text-[11px]">{{ refSource.count }} hits</span>
                        </div>
                    </div>
                    <div v-else class="flex flex-col items-center justify-center p-6 text-center border border-dashed border-border/80 rounded-xl bg-muted/10">
                        <Share2 class="h-6 w-6 text-muted-foreground/60 mb-1" />
                        <p class="text-[11px] text-muted-foreground">Belum ada rujukan eksternal tercatat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
