<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Image, FolderOpen, Layers, Briefcase, Award, BookOpen, FileText, Handshake, Mail, Sliders, Terminal, Plug } from '@lucide/vue';
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
import { index as projectsIndex } from '@/routes/projects';
import { index as technologiesIndex } from '@/routes/technologies';
import { index as skillsIndex } from '@/routes/skills';
import { index as postsIndex } from '@/routes/posts';
import { index as categoriesIndex } from '@/routes/categories';
import { index as servicesIndex } from '@/routes/services';
import { index as workflowsIndex } from '@/routes/workflows';
import { index as developmentPhilosophiesIndex } from '@/routes/development-philosophies';
import { index as educationExperienceIndex } from '@/routes/education-experience';
import { index as inboxIndex } from '@/routes/inbox';
import { edit as globalSettingsEdit } from '@/routes/global-settings';
import { edit as integrationsEdit } from '@/routes/integrations';
import { index as apiDocsIndex } from '@/routes/api-docs';
import type { NavItem } from '@/types';

const page = usePage();

const mainNavItems = computed<NavItem[]>(() => [
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
        title: 'Inbox',
        href: inboxIndex(),
        icon: Mail,
        badge: page.props.unreadMessagesCount as number > 0 ? page.props.unreadMessagesCount as number : undefined,
    },
    {
        title: 'Projects',
        href: '#',
        icon: Briefcase,
        items: [
            {
                title: 'Projects',
                href: projectsIndex(),
                icon: Briefcase,
            },
            {
                title: 'Project Categories',
                href: projectCategoriesIndex(),
                icon: FolderOpen,
            },
        ],
    },
    {
        title: 'Blog',
        href: '#',
        icon: BookOpen,
        items: [
            {
                title: 'Artikel',
                href: postsIndex(),
                icon: FileText,
            },
            {
                title: 'Kategori Blog',
                href: categoriesIndex(),
                icon: FolderOpen,
            },
        ],
    },
    {
        title: 'Tech Stack',
        href: technologiesIndex(),
        icon: Layers,
    },
    {
        title: 'Skills',
        href: skillsIndex(),
        icon: Award,
    },
    {
        title: 'Services',
        href: servicesIndex(),
        icon: Handshake,
    },
    {
        title: 'My Workflow',
        href: workflowsIndex(),
        icon: Briefcase,
    },
    {
        title: 'Dev Philosophy',
        href: developmentPhilosophiesIndex(),
        icon: Award,
    },
    {
        title: 'Education & Experience',
        href: educationExperienceIndex(),
        icon: BookOpen,
    },
    {
        title: 'Pengaturan Global',
        href: globalSettingsEdit(),
        icon: Sliders,
    },
    {
        title: 'Integrations',
        href: integrationsEdit(),
        icon: Plug,
    },
    {
        title: 'Dokumentasi API',
        href: apiDocsIndex(),
        icon: Terminal,
    },
]);
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
