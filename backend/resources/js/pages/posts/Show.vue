<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    ArrowLeft,
    Edit2,
    Eye,
    Calendar,
    BookOpen,
    Globe,
    Layers,
    Share2,
    Monitor,
    Tablet,
    Smartphone,
    TrendingUp,
    Users
} from '@lucide/vue';
import { Chart, registerables } from 'chart.js';
import { index as postsIndex, edit as postsEdit } from '@/routes/posts';

// Register all Chart.js components
Chart.register(...registerables);

const props = defineProps<{
    post: any;
    stats: {
        total_views: number;
        unique_visitors: number;
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
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Daftar Artikel',
                href: postsIndex(),
            },
            {
                title: 'Detail & Statistik',
                href: '#',
            },
        ],
    },
});

// Canvas chart references
const viewsChartCanvas = ref<HTMLCanvasElement | null>(null);
const deviceChartCanvas = ref<HTMLCanvasElement | null>(null);
let viewsChart: Chart | null = null;
let deviceChart: Chart | null = null;

onMounted(() => {
    // 1. Views Over Time Line Chart
    if (viewsChartCanvas.value) {
        viewsChart = new Chart(viewsChartCanvas.value, {
            type: 'line',
            data: {
                labels: props.stats.views_over_time.labels,
                datasets: [{
                    label: 'Page Views',
                    data: props.stats.views_over_time.values,
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
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
                        grid: { color: 'rgba(156, 163, 175, 0.08)' },
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

    // 2. Device Share Donut Chart
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
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        padding: 10,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        cornerRadius: 8,
                    }
                },
                cutout: '65%'
            }
        });
    }
});

// Helper: Format Date
const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Statistik - ${props.post.title}`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto w-full">
        <!-- Header & Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border/70 pb-4">
            <div class="flex items-center gap-3">
                <Link :href="postsIndex()">
                    <Button variant="ghost" size="icon" class="h-9 w-9 rounded-xl border border-border/80 cursor-pointer">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-foreground">Detail & Analitik Artikel</h1>
                    <p class="text-xs text-muted-foreground line-clamp-1 max-w-xl">
                        {{ props.post.title }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a :href="`/admin-cms/posts/${props.post.id}/preview`" target="_blank">
                    <Button variant="outline" class="cursor-pointer h-9 text-xs flex items-center gap-1.5 border-border/80">
                        <Eye class="h-3.5 w-3.5" />
                        Preview Artikel
                    </Button>
                </a>
                <Link :href="postsEdit(props.post.id)">
                    <Button class="bg-primary text-white hover:bg-primary/90 cursor-pointer h-9 text-xs flex items-center gap-1.5 font-semibold px-4 shadow-xs">
                        <Edit2 class="h-3.5 w-3.5" />
                        Edit Konten
                    </Button>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Left Sidebar: Meta Information -->
            <div class="rounded-2xl border border-border/70 bg-card p-6 space-y-6 lg:col-span-1 shadow-2xs">
                <div>
                    <h3 class="font-bold text-sm text-foreground mb-4 flex items-center gap-2">
                        <FileText class="h-4 w-4 text-primary" />
                        Informasi Artikel
                    </h3>
                    <div class="space-y-3.5 text-xs font-medium divide-y divide-border/40">
                        <div class="pt-0 pb-3 flex items-center justify-between gap-2">
                            <span class="text-muted-foreground">Status</span>
                            <!-- Draft Badge -->
                            <span v-if="props.post.status === 'draft'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-500/10 text-slate-600 border border-slate-500/20 dark:text-slate-400">
                                Draft
                            </span>
                            <!-- Published Badge -->
                            <span v-else-if="props.post.status === 'published'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-600 border border-emerald-500/30 dark:text-emerald-400">
                                Published
                            </span>
                            <!-- Scheduled Badge -->
                            <span v-else-if="props.post.status === 'scheduled'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/15 text-amber-600 border border-amber-500/30 dark:text-amber-400">
                                Scheduled
                            </span>
                        </div>
                        <div class="py-3 flex items-center justify-between gap-2">
                            <span class="text-muted-foreground">Kategori</span>
                            <div class="flex flex-wrap gap-1 justify-end max-w-[180px]">
                                <span v-for="cat in props.post.categories" :key="cat.id" class="px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20 text-[11px] font-semibold">
                                    {{ cat.name }}
                                </span>
                            </div>
                        </div>
                        <div class="py-3 flex items-center justify-between gap-2">
                            <span class="text-muted-foreground">Mulai Dibuat</span>
                            <span class="text-foreground font-mono text-[11px]">{{ formatDate(props.post.created_at) }}</span>
                        </div>
                        <div class="py-3 flex items-center justify-between gap-2" v-if="props.post.status === 'published'">
                            <span class="text-muted-foreground">Tanggal Rilis</span>
                            <span class="text-foreground font-mono text-[11px]">{{ formatDate(props.post.published_at) }}</span>
                        </div>
                        <div class="py-3 flex items-center justify-between gap-2" v-if="props.post.status === 'scheduled'">
                            <span class="text-muted-foreground">Jadwal Rilis</span>
                            <span class="text-amber-600 dark:text-amber-400 font-mono text-[11px]">{{ formatDate(props.post.scheduled_at) }}</span>
                        </div>
                        <div class="py-3 flex items-center justify-between gap-2">
                            <span class="text-muted-foreground">Waktu Baca</span>
                            <span class="text-foreground font-semibold flex items-center gap-1">
                                <BookOpen class="h-3.5 w-3.5 text-muted-foreground" />
                                {{ props.post.reading_time || 1 }} menit
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="border-t border-border/70 pt-5">
                    <h3 class="font-bold text-sm text-foreground mb-4 flex items-center gap-2">
                        <Globe class="h-4 w-4 text-primary" />
                        Metadata SEO
                    </h3>
                    <div class="space-y-4 text-xs">
                        <div class="grid gap-1.5">
                            <span class="font-bold text-muted-foreground text-[10px] uppercase tracking-wider">Meta Title</span>
                            <p class="text-foreground bg-muted/20 p-3 rounded-xl border border-border/70 break-words font-medium">
                                {{ props.post.meta_title || props.post.title }}
                            </p>
                        </div>
                        <div class="grid gap-1.5">
                            <span class="font-bold text-muted-foreground text-[10px] uppercase tracking-wider">Meta Description</span>
                            <p class="text-foreground bg-muted/20 p-3 rounded-xl border border-border/70 break-words leading-relaxed">
                                {{ props.post.meta_description || props.post.excerpt }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Area: Dashboard Analytics -->
            <div class="lg:col-span-2 space-y-6">
                <!-- KPI Widgets Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Total Views Card -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Total Views</span>
                            <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground">
                                {{ props.stats.total_views }}
                            </h2>
                        </div>
                        <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center">
                            <TrendingUp class="h-6 w-6" />
                        </div>
                    </div>

                    <!-- Unique Visitors Card -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 flex items-center justify-between shadow-2xs">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">Unique Visitors</span>
                            <h2 class="text-3xl font-extrabold tracking-tight mt-1 text-foreground">
                                {{ props.stats.unique_visitors }}
                            </h2>
                        </div>
                        <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 dark:text-emerald-400 flex items-center justify-center">
                            <Users class="h-6 w-6" />
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Daily Views Line Chart -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 md:col-span-2 flex flex-col h-[320px] shadow-2xs">
                        <h3 class="font-bold text-sm text-foreground mb-3 flex items-center gap-2">
                            <TrendingUp class="h-4 w-4 text-primary" />
                            Tren Kunjungan (30 Hari Terakhir)
                        </h3>
                        <div class="flex-1 relative">
                            <canvas ref="viewsChartCanvas"></canvas>
                        </div>
                    </div>

                    <!-- Device Share Donut Chart -->
                    <div class="rounded-2xl border border-border/70 bg-card p-6 md:col-span-1 flex flex-col h-[320px] shadow-2xs">
                        <h3 class="font-bold text-sm text-foreground mb-3 flex items-center gap-2">
                            <Monitor class="h-4 w-4 text-primary" />
                            Persentase Perangkat
                        </h3>
                        <div class="flex-1 relative flex items-center justify-center">
                            <canvas ref="deviceChartCanvas"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Traffic Referral Sources Table -->
                <div class="rounded-2xl border border-border/70 bg-card p-6 shadow-2xs space-y-4">
                    <h3 class="font-bold text-sm text-foreground flex items-center gap-2">
                        <Share2 class="h-4 w-4 text-primary" />
                        Sumber Trafik Utama (Referrer)
                    </h3>
                    <div v-if="props.stats.top_referrers.length > 0" class="border border-border/70 rounded-xl overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-border/70 bg-muted/20 text-[11px] font-bold uppercase tracking-wider text-muted-foreground">
                                    <th class="p-3 w-12 text-center">#</th>
                                    <th class="p-3">Asal Referrer / Host</th>
                                    <th class="p-3 text-right w-28">Jumlah Hits</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/40 text-xs font-medium">
                                <tr v-for="(refSource, idx) in props.stats.top_referrers" :key="refSource.referrer" class="hover:bg-muted/20 transition-colors">
                                    <td class="p-3 text-center text-muted-foreground font-mono text-xs">{{ idx + 1 }}</td>
                                    <td class="p-3 text-foreground break-all">
                                        <span v-if="refSource.referrer === 'direct'" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20 font-semibold text-[11px]">
                                            <Globe class="h-3 w-3" />
                                            Direct / Pencarian Langsung
                                        </span>
                                        <span v-else class="text-foreground font-medium">{{ refSource.referrer }}</span>
                                    </td>
                                    <td class="p-3 text-right font-bold text-foreground font-mono">
                                        {{ refSource.count }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-xs text-muted-foreground p-8 border border-dashed border-border/70 rounded-xl text-center bg-muted/10">
                        Belum ada data referal trafik yang tercatat.
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
