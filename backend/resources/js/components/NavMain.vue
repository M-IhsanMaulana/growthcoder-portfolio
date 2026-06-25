<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="relative h-10! transition-all duration-200"
                >
                    <Link
                        :href="item.href"
                        :class="[
                            isCurrentUrl(item.href)
                                ? 'bg-primary/10 font-semibold text-primary dark:bg-primary/20 dark:text-white'
                                : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :class="[
                                isCurrentUrl(item.href)
                                    ? 'text-primary dark:text-primary'
                                    : 'text-neutral-500 group-hover:text-neutral-800 dark:text-neutral-400 dark:group-hover:text-neutral-100',
                            ]"
                        />
                        <span>{{ item.title }}</span>
                        <!-- Modern vertical active indicator line -->
                        <span
                            v-if="isCurrentUrl(item.href)"
                            class="absolute top-1/2 left-0 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-primary"
                        />
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
