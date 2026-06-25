<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Sun, Moon } from '@lucide/vue';
import { ref, onMounted } from 'vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';

const { resolvedAppearance, updateAppearance } = useAppearance();

const isMounted = ref(false);
onMounted(() => {
    isMounted.value = true;
});

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-slate-50 p-6 transition-colors duration-500 md:p-10 dark:bg-zinc-950"
    >
        <!-- Theme Toggle Button -->
        <button
            @click="
                updateAppearance(
                    resolvedAppearance === 'dark' ? 'light' : 'dark',
                )
            "
            class="absolute top-6 right-6 z-50 flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-white/40 bg-white/60 text-zinc-700 shadow-sm backdrop-blur-xl transition-all duration-300 hover:scale-105 dark:border-zinc-800/40 dark:bg-zinc-900/60 dark:text-zinc-300"
            :aria-label="
                isMounted && resolvedAppearance === 'dark'
                    ? 'Switch to light mode'
                    : 'Switch to dark mode'
            "
        >
            <Sun
                v-if="isMounted && resolvedAppearance === 'dark'"
                class="size-5"
            />
            <Moon v-else class="size-5" />
        </button>

        <!-- Soft Glowing Background Mesh Elements (Adaptive) -->
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.15),rgba(255,255,255,0))] dark:bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.25),rgba(255,255,255,0))]"
        />
        <div
            class="bg-grid-pattern absolute inset-0 opacity-70 dark:opacity-40"
        />

        <!-- Glowing Mesh Blurs -->
        <div
            class="absolute top-[10%] left-[10%] h-[300px] w-[300px] animate-pulse rounded-full bg-brand-primary/10 blur-[100px] duration-[8000ms] dark:bg-brand-primary/15"
        />
        <div
            class="absolute right-[10%] bottom-[10%] h-[300px] w-[300px] animate-pulse rounded-full bg-brand-accent/10 blur-[100px] duration-[10000ms] dark:bg-brand-accent/15"
        />

        <div class="relative z-10 flex w-full max-w-md flex-col gap-6">
            <!-- Glassmorphism Card Container -->
            <Card
                class="relative overflow-hidden rounded-2xl border border-white/40 bg-white/60 shadow-[0_8px_32px_0_rgba(0,0,0,0.08)] backdrop-blur-xl transition-all duration-300 dark:border-zinc-800/40 dark:bg-zinc-900/60 dark:shadow-[0_8px_32px_0_rgba(0,0,0,0.37)]"
            >
                <!-- Glowing Top Accent Line -->
                <div
                    class="absolute top-0 right-0 left-0 h-1.5 bg-gradient-to-r from-brand-primary via-brand-secondary to-brand-accent"
                />

                <CardHeader
                    class="flex flex-col items-center gap-4 px-8 pt-8 pb-4 text-center"
                >
                    <!-- Branding Logo -->
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-3 transition-transform duration-300 select-none hover:scale-[1.02]"
                    >
                        <img
                            :src="
                                isMounted && resolvedAppearance === 'dark'
                                    ? '/storage/logo/logo-gc-light.png'
                                    : '/storage/logo/logo-gc-dark.png'
                            "
                            alt="Logo"
                            class="h-16 w-auto object-contain"
                        />
                    </Link>

                    <div class="mt-2 space-y-1.5">
                        <CardTitle
                            class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50"
                            >{{ title }}</CardTitle
                        >
                        <CardDescription
                            class="text-sm text-zinc-500 dark:text-zinc-400"
                        >
                            {{ description }}
                        </CardDescription>
                    </div>
                </CardHeader>

                <CardContent class="px-8 pt-2 pb-8">
                    <slot />
                </CardContent>
            </Card>

            <!-- Footer Details -->
            <div
                class="flex items-center justify-between px-2 font-sans text-xs text-zinc-400 select-none dark:text-zinc-500"
            >
                <p>&copy; {{ new Date().getFullYear() }} growthcoder.id</p>
                <div class="flex gap-4">
                    <span
                        class="transition-colors hover:text-zinc-600 dark:hover:text-zinc-400"
                        >Documentation</span
                    >
                    <span
                        class="transition-colors hover:text-zinc-600 dark:hover:text-zinc-400"
                        >Privacy</span
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-grid-pattern {
    background-size: 24px 24px;
    background-image:
        linear-gradient(to right, rgba(0, 0, 0, 0.03) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
}
:global(.dark) .bg-grid-pattern {
    background-image:
        linear-gradient(
            to right,
            rgba(255, 255, 255, 0.02) 1px,
            transparent 1px
        ),
        linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.02) 1px,
            transparent 1px
        );
}
</style>
