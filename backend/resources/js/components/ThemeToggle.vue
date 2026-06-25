<script setup lang="ts">
import { Monitor, Moon, Sun } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const currentIcon = computed(() => {
    switch (appearance.value) {
        case 'light':
            return Sun;
        case 'dark':
            return Moon;
        default:
            return Monitor;
    }
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="h-9 w-9 cursor-pointer rounded-lg"
            >
                <component
                    :is="currentIcon"
                    class="h-[1.2rem] w-[1.2rem] transition-all"
                />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-36">
            <DropdownMenuItem
                @click="updateAppearance('light')"
                class="cursor-pointer"
            >
                <Sun class="mr-2 h-4 w-4" />
                <span>Light</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                @click="updateAppearance('dark')"
                class="cursor-pointer"
            >
                <Moon class="mr-2 h-4 w-4" />
                <span>Dark</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                @click="updateAppearance('system')"
                class="cursor-pointer"
            >
                <Monitor class="mr-2 h-4 w-4" />
                <span>System</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
