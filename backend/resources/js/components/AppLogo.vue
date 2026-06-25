<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance } = useAppearance();
const isMounted = ref(false);

onMounted(() => {
    isMounted.value = true;
});

const logoUrl = computed(() => {
    return isMounted.value && resolvedAppearance.value === 'dark'
        ? '/storage/logo/logo-gc-light.png'
        : '/storage/logo/logo-gc-dark.png';
});
</script>

<template>
    <div
        class="flex w-full items-center justify-start overflow-hidden group-data-[collapsible=icon]:justify-center"
    >
        <!-- Full Logo: shown when sidebar is expanded -->
        <img
            :src="logoUrl"
            alt="GrowthCoder Logo"
            class="h-10 w-auto max-w-[200px] object-contain transition-all duration-300 group-data-[collapsible=icon]:hidden"
        />

        <!-- Collapsed Badge: shown when sidebar is collapsed -->
        <div
            class="hidden h-9 w-9 items-center justify-center rounded-xl border border-sidebar-border/30 bg-gradient-to-br from-primary to-brand-secondary text-xs font-black tracking-wider text-white shadow-md transition-transform duration-200 group-data-[collapsible=icon]:flex hover:scale-105"
        >
            GC
        </div>
    </div>
</template>
