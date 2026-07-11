<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();

// Helper to determine if a menu item with subitems has any active child
const hasActiveChild = (item: NavItem): boolean => {
    if (!item.items) {
        return false;
    }

    return item.items.some(
        (subItem) =>
            typeof subItem.href === 'string' && isCurrentUrl(subItem.href),
    );
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- If item has sub-items, render collapsible dropdown/submenu -->
                <Collapsible
                    v-if="item.items && item.items.length > 0"
                    as-child
                    :default-open="hasActiveChild(item)"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton
                                :tooltip="item.title"
                                :is-active="hasActiveChild(item)"
                                class="relative h-10! cursor-pointer text-neutral-600 transition-all duration-200 hover:text-neutral-900 data-[active=true]:bg-primary/10 data-[active=true]:font-semibold data-[active=true]:text-primary dark:text-neutral-400 dark:hover:text-neutral-100 dark:data-[active=true]:bg-primary/20 dark:data-[active=true]:text-white"
                            >
                                <component
                                    v-if="item.icon"
                                    :is="item.icon"
                                    :class="[
                                        hasActiveChild(item)
                                            ? 'text-primary dark:text-primary'
                                            : 'text-neutral-500 group-hover:text-neutral-800 dark:text-neutral-400 dark:group-hover:text-neutral-100',
                                    ]"
                                />
                                <span>{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto h-4 w-4 text-neutral-500 transition-transform duration-200 group-hover/collapsible:text-neutral-800 group-data-[state=open]/collapsible:rotate-90 dark:text-neutral-400 dark:group-hover/collapsible:text-neutral-100"
                                />
                                <!-- Active indicator for parent if child active -->
                                <span
                                    v-if="hasActiveChild(item)"
                                    class="absolute top-1/2 left-0 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-primary"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="subItem in item.items"
                                    :key="subItem.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="
                                            subItem.href
                                                ? isCurrentUrl(subItem.href)
                                                : false
                                        "
                                        class="h-9! text-neutral-600 transition-all duration-200 hover:text-neutral-900 data-[active=true]:bg-primary/10 data-[active=true]:font-semibold data-[active=true]:text-primary dark:text-neutral-400 dark:hover:text-neutral-100 dark:data-[active=true]:bg-primary/20 dark:data-[active=true]:text-white"
                                    >
                                        <Link :href="subItem.href">
                                            <component
                                                v-if="subItem.icon"
                                                :is="subItem.icon"
                                                :class="[
                                                    subItem.href &&
                                                    isCurrentUrl(subItem.href)
                                                        ? 'text-primary dark:text-primary'
                                                        : 'text-neutral-500 dark:text-neutral-400',
                                                ]"
                                            />
                                            <span>{{ subItem.title }}</span>
                                            <span
                                                v-if="subItem.badge"
                                                class="ml-auto inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-semibold text-white animate-pulse"
                                            >
                                                {{ subItem.badge }}
                                            </span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>
 
                <!-- Otherwise, render normal link button -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="item.href ? isCurrentUrl(item.href) : false"
                        :tooltip="item.title"
                        class="relative h-10! transition-all duration-200"
                    >
                        <Link
                            v-if="item.href"
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
                            <span
                                v-if="item.badge"
                                class="ml-auto inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-semibold text-white animate-pulse"
                            >
                                {{ item.badge }}
                            </span>
                            <!-- Modern vertical active indicator line -->
                            <span
                                v-if="isCurrentUrl(item.href)"
                                class="absolute top-1/2 left-0 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-primary"
                            />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
