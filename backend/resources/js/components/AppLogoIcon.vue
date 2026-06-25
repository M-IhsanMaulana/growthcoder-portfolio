<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import type { HTMLAttributes } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();

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
    <img
        :src="logoUrl"
        alt="GrowthCoder Logo Icon"
        :class="className"
        v-bind="$attrs"
        class="object-contain"
    />
</template>
