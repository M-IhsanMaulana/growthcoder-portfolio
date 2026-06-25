<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh grid-cols-1 overflow-hidden bg-background lg:grid-cols-2"
    >
        <!-- Left Side: Form Container -->
        <div
            class="flex flex-col justify-center px-6 py-12 sm:px-8 lg:px-20 xl:px-24"
        >
            <div class="mx-auto w-full max-w-sm">
                <!-- Mobile Branding Logo (only visible on mobile screens) -->
                <div
                    class="mb-8 flex items-center justify-center gap-2 lg:hidden"
                >
                    <Link
                        :href="home()"
                        class="flex items-center gap-2 font-semibold"
                    >
                        <AppLogoIcon
                            class="size-8 fill-current text-foreground"
                        />
                        <span class="text-lg font-bold tracking-tight">{{
                            name
                        }}</span>
                    </Link>
                </div>

                <div class="mb-6 flex flex-col gap-2 text-center lg:text-left">
                    <h1
                        class="text-3xl font-semibold tracking-tight text-foreground"
                        v-if="title"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>

                <slot />
            </div>
        </div>

        <!-- Right Side: Premium Dark Panel with animated gradient background, logo, and micro-interactions -->
        <div
            class="relative hidden h-full flex-col justify-between overflow-hidden border-l border-zinc-800/80 bg-zinc-950 p-10 text-white lg:flex"
        >
            <!-- Animated Radial/Linear Glow Effects -->
            <div
                class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(120,119,198,0.15),rgba(255,255,255,0))]"
            />
            <div class="bg-grid-pattern absolute inset-0" />

            <!-- Animated Glowing Mesh Gradients -->
            <div
                class="absolute -top-[40%] -left-[40%] h-[80%] w-[80%] animate-pulse rounded-full bg-indigo-500/10 blur-[120px] duration-[8000ms]"
            />
            <div
                class="absolute -right-[40%] -bottom-[40%] h-[80%] w-[80%] animate-pulse rounded-full bg-violet-600/10 blur-[120px] duration-[10000ms]"
            />

            <!-- Branding Header -->
            <div class="relative z-20 flex items-center justify-between">
                <Link
                    :href="home()"
                    class="group flex items-center gap-2 text-lg font-medium tracking-tight"
                >
                    <AppLogoIcon
                        class="size-9 fill-current text-white transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6"
                    />
                    <span
                        class="bg-gradient-to-r from-white via-zinc-200 to-zinc-400 bg-clip-text text-xl font-bold text-transparent"
                        >{{ name }}</span
                    >
                </Link>
                <div
                    class="rounded-full border border-zinc-800 bg-zinc-900/50 px-2.5 py-1 font-mono text-xs text-zinc-400 backdrop-blur-md select-none"
                >
                    CMS v1.0.0
                </div>
            </div>

            <!-- Visual Content & Micro-interactions -->
            <div
                class="relative z-20 my-auto flex flex-col items-center gap-6 text-center"
            >
                <!-- Visual Card Showcase -->
                <div
                    class="group relative transition-all duration-500 select-none hover:scale-[1.02]"
                >
                    <!-- Outer Glow border -->
                    <div
                        class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 opacity-20 blur-xl transition duration-1000 group-hover:opacity-40"
                    />

                    <!-- Main Card -->
                    <div
                        class="relative flex max-w-sm flex-col gap-4 rounded-2xl border border-zinc-800 bg-zinc-900/80 p-8 text-left shadow-2xl backdrop-blur-xl"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-shield-check"
                                >
                                    <path
                                        d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 .76-.97l8-2a1 1 0 0 1 .48 0l8 2A1 1 0 0 1 20 6z"
                                    />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-white">
                                    Secure Portal
                                </h3>
                                <p class="text-xs text-zinc-400">
                                    Authorized Personnel Only
                                </p>
                            </div>
                        </div>
                        <p
                            class="font-sans text-xs leading-relaxed text-zinc-300"
                        >
                            Welcome to the admin center of your personal
                            portfolio. Manage your projects, study cases, and
                            publish educational contents securely.
                        </p>
                        <!-- Interactive status indicators -->
                        <div
                            class="mt-1 flex items-center justify-between border-t border-zinc-800/80 pt-4 text-xs"
                        >
                            <span class="font-mono text-zinc-500"
                                >System Integrity</span
                            >
                            <span
                                class="flex items-center gap-1.5 font-medium text-emerald-400"
                            >
                                <span
                                    class="h-2 w-2 rounded-full bg-emerald-400"
                                />
                                Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Details -->
            <div
                class="relative z-20 flex items-center justify-between font-sans text-xs text-zinc-500"
            >
                <p>&copy; {{ new Date().getFullYear() }} growthcoder.id</p>
                <div class="flex gap-4">
                    <span class="transition-colors hover:text-zinc-400"
                        >Documentation</span
                    >
                    <span class="transition-colors hover:text-zinc-400"
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
