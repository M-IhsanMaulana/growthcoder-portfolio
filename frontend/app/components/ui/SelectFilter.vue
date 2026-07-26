<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

interface OptionItem {
  label: string;
  value: string | number;
}

const props = withDefaults(
  defineProps<{
    modelValue: string | number;
    options: OptionItem[];
    placeholder?: string;
    class?: string;
  }>(),
  {
    placeholder: 'Pilih...',
    class: '',
  }
);

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | number): void;
}>();

const isOpen = ref(false);
const containerRef = ref<HTMLElement | null>(null);

const selectedOption = computed(() => {
  return props.options.find((opt) => opt.value === props.modelValue) || props.options[0];
});

const toggleOpen = () => {
  isOpen.value = !isOpen.value;
};

const selectOption = (val: string | number) => {
  emit('update:modelValue', val);
  isOpen.value = false;
};

const handleClickOutside = (event: MouseEvent) => {
  if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
    isOpen.value = false;
  }
};

const handleKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <div
    ref="containerRef"
    class="inline-block text-left"
    :class="[props.class, isOpen ? 'relative z-50' : 'relative z-20']"
  >
    <!-- Trigger Button -->
    <button
      type="button"
      @click="toggleOpen"
      class="w-full h-10 px-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 hover:border-zinc-300 dark:hover:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 shadow-xs flex items-center justify-between gap-2.5 transition-all duration-200 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-brand-purple/20"
      :aria-expanded="isOpen"
    >
      <span class="truncate">{{ selectedOption?.label || placeholder }}</span>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="14"
        height="14"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2.5"
        class="text-zinc-400 transition-transform duration-200 shrink-0"
        :class="{ 'rotate-180 text-brand-purple dark:text-indigo-400': isOpen }"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 top-full mt-1.5 w-full min-w-[150px] z-[100] p-1.5 rounded-2xl bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-xl shadow-black/5 dark:shadow-black/40 space-y-0.5"
      >
        <button
          v-for="opt in options"
          :key="opt.value"
          type="button"
          @click="selectOption(opt.value)"
          class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-colors duration-150 text-left"
          :class="
            opt.value === modelValue
              ? 'bg-brand-purple/10 text-brand-purple dark:text-indigo-400 font-bold'
              : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800/80'
          "
        >
          <span>{{ opt.label }}</span>
          <svg
            v-if="opt.value === modelValue"
            xmlns="http://www.w3.org/2000/svg"
            width="14"
            height="14"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2.5"
            class="text-brand-purple dark:text-indigo-400 shrink-0"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
        </button>
      </div>
    </Transition>
  </div>
</template>
