<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Image, FolderOpen, Layers, Briefcase } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as mediaIndex } from '@/routes/media';
import { index as projectCategoriesIndex } from '@/routes/project-categories';
import { index as technologiesIndex } from '@/routes/technologies';
import { index as projectsIndex } from '@/routes/projects';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Media Library',
        href: mediaIndex(),
        icon: Image,
    },
    {
        title: 'Project Categories',
        href: projectCategoriesIndex(),
        icon: FolderOpen,
    },
    {
        title: 'Projects',
        href: projectsIndex(),
        icon: Briefcase,
    },
    {
        title: 'Tech Stack',
        href: technologiesIndex(),
        icon: Layers,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader
            class="h-16 justify-center border-b border-sidebar-border/50 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12"
        >
            <Link
                :href="dashboard()"
                class="flex items-center px-4 group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:px-0"
            >
                <AppLogo />
            </Link>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>
    </Sidebar>
    <slot />
</template>
