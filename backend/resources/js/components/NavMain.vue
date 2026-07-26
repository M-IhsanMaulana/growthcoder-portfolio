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

<<template>
    <SidebarGroup class="px-2.5 py-1">
        <SidebarGroupLabel class="mb-1 text-[11px] font-bold tracking-wider text-neutral-400/90 dark:text-neutral-500 uppercase px-2 py-1.5 select-none">
            Platform
        </SidebarGroupLabel>
        <SidebarMenu class="space-y-1">
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
                                class="relative flex h-10 w-full items-center gap-3 rounded-lg px-3 text-sm font-medium transition-all duration-200 outline-none focus:outline-none focus:ring-0 focus-visible:ring-0 group-data-[collapsible=icon]:px-0 group-data-[collapsible=icon]:justify-center hover:bg-neutral-100/80 text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/60 dark:hover:text-neutral-100 data-[active=true]:bg-primary/10 data-[active=true]:text-primary data-[active=true]:font-semibold dark:data-[active=true]:bg-primary/20 dark:data-[active=true]:text-white"
                            >
                                <component
                                    v-if="item.icon"
                                    :is="item.icon"
                                    :class="[
                                        hasActiveChild(item)
                                            ? 'text-primary dark:text-primary'
                                            : 'text-neutral-400 group-hover/collapsible:text-neutral-700 dark:text-neutral-500 dark:group-hover/collapsible:text-neutral-200',
                                        'size-4.5 shrink-0 transition-colors duration-200',
                                    ]"
                                />
                                <span class="truncate">{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto size-4 shrink-0 text-neutral-400 transition-transform duration-200 group-hover/collapsible:text-neutral-700 group-data-[state=open]/collapsible:rotate-90 dark:text-neutral-500 dark:group-hover/collapsible:text-neutral-200"
                                />
                                <!-- Active indicator for parent if child active -->
                                <span
                                    v-if="hasActiveChild(item)"
                                    class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-primary shadow-xs"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent class="transition-all duration-200">
                            <SidebarMenuSub class="ml-3.5 my-1 space-y-1 border-l border-neutral-200/80 dark:border-neutral-800/80 pl-2.5">
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
                                        class="h-9 w-full rounded-lg px-2.5 text-sm transition-all duration-150 outline-none focus:outline-none focus:ring-0 focus-visible:ring-0 data-[active=true]:bg-primary/12 data-[active=true]:text-primary data-[active=true]:font-semibold dark:data-[active=true]:bg-primary/25 dark:data-[active=true]:text-white text-neutral-600 hover:bg-neutral-100/70 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/50 dark:hover:text-neutral-100"
                                    >
                                        <Link :href="subItem.href" class="flex items-center gap-2.5 w-full">
                                            <!-- Active Bullet dot for child item -->
                                            <span
                                                v-if="subItem.href && isCurrentUrl(subItem.href)"
                                                class="size-1.5 rounded-full bg-primary shrink-0 transition-all duration-200 shadow-xs"
                                            />
                                            <component
                                                v-else-if="subItem.icon"
                                                :is="subItem.icon"
                                                :class="[
                                                    'size-4 shrink-0 transition-colors duration-200 text-neutral-400 group-hover:text-neutral-700 dark:text-neutral-500 dark:group-hover:text-neutral-200',
                                                ]"
                                            />
                                            <span class="truncate">{{ subItem.title }}</span>
                                            <span
                                                v-if="subItem.badge"
                                                class="ml-auto inline-flex h-4.5 min-w-[18px] items-center justify-center rounded-full bg-primary/15 text-primary text-[10px] font-bold px-1.5 dark:bg-primary/30 dark:text-primary-foreground"
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
                        class="relative h-10 w-full rounded-lg transition-all duration-200 outline-none focus:outline-none focus:ring-0 focus-visible:ring-0"
                    >
                        <Link
                            v-if="item.href"
                            :href="item.href"
                            :class="[
                                isCurrentUrl(item.href)
                                    ? 'bg-primary/10 text-primary font-semibold shadow-2xs dark:bg-primary/20 dark:text-white'
                                    : 'text-neutral-600 hover:bg-neutral-100/80 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800/60 dark:hover:text-neutral-100',
                                'flex items-center gap-3 w-full h-full rounded-lg px-3 text-sm font-medium',
                            ]"
                        >
                            <component
                                :is="item.icon"
                                :class="[
                                    isCurrentUrl(item.href)
                                        ? 'text-primary dark:text-primary'
                                        : 'text-neutral-400 group-hover:text-neutral-700 dark:text-neutral-500 dark:group-hover:text-neutral-200',
                                    'size-4.5 shrink-0 transition-colors duration-200',
                                ]"
                            />
                            <span class="truncate">{{ item.title }}</span>
                            <span
                                v-if="item.badge"
                                class="ml-auto inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-primary text-white text-[10px] font-semibold px-1.5 shadow-xs"
                            >
                                {{ item.badge }}
                            </span>
                            <!-- Modern vertical active indicator line -->
                            <span
                                v-if="isCurrentUrl(item.href)"
                                class="absolute left-0 top-1/2 h-5 w-1 -translate-y-1/2 rounded-r-full bg-primary shadow-xs"
                            />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
