<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Home } from '@lucide/vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

type Props = {
    breadcrumbs: BreadcrumbItemType[];
};

const props = defineProps<Props>();

// Computed breadcrumbs to ensure Dashboard is always the root item
const computedBreadcrumbs = computed(() => {
    const items = [...props.breadcrumbs];

    // Helper to extract string URL from href (string or RouteDefinition object)
    const getUrl = (href: any): string => {
        if (typeof href === 'string') return href;
        if (href && typeof href === 'object' && href.url) return href.url;
        return '';
    };

    const dashboardUrl = getUrl(dashboard());
    const hasDashboard = items.length > 0 && getUrl(items[0].href) === dashboardUrl;

    if (!hasDashboard) {
        items.unshift({
            title: 'Dashboard',
            href: dashboard(),
        });
    }

    // Attach visual Home icon to the first (Dashboard) breadcrumb item
    return items.map((item, idx) => {
        if (idx === 0) {
            return {
                ...item,
                icon: Home,
            };
        }
        return item;
    });
});
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList>
            <template v-for="(item, index) in computedBreadcrumbs" :key="index">
                <BreadcrumbItem>
                    <!-- Current Page (Last Item) -->
                    <template v-if="index === computedBreadcrumbs.length - 1">
                        <BreadcrumbPage class="flex items-center gap-1.5 font-medium text-foreground dark:text-neutral-200">
                            <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 text-neutral-500 dark:text-neutral-400" />
                            <span>{{ item.title }}</span>
                        </BreadcrumbPage>
                    </template>
                    <!-- Navigable Parent Links -->
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link
                                :href="item.href"
                                class="flex items-center gap-1.5 hover:text-foreground dark:hover:text-neutral-200 transition-colors duration-150"
                            >
                                <component :is="item.icon" v-if="item.icon" class="h-3.5 w-3.5 text-muted-foreground dark:text-neutral-500" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <!-- Separator -->
                <BreadcrumbSeparator v-if="index !== computedBreadcrumbs.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
