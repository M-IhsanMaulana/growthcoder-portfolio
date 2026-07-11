<template>
  <ul class="space-y-2.5">
    <li v-for="item in items" :key="item.id" class="flex flex-col">
      <a 
        @click.prevent="scrollToHeading(item.id)"
        :href="'#' + item.id"
        class="block py-0.5 text-xs transition-all duration-300 relative pl-4 select-none cursor-pointer"
        :class="activeId === item.id 
          ? 'text-brand-purple dark:text-indigo-400 font-extrabold translate-x-1' 
          : 'text-zinc-650 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white font-medium'"
      >
        <!-- Indicator dot -->
        <span 
          class="absolute left-0 top-1.5 rounded-full transition-all duration-300"
          :class="activeId === item.id 
            ? 'w-1.5 h-1.5 bg-brand-purple dark:bg-indigo-400' 
            : 'w-1 h-1 bg-zinc-300 dark:bg-zinc-800'"
        ></span>
        {{ item.text }}
      </a>
      
      <!-- Recursive nested list -->
      <div v-if="item.children && item.children.length" class="pl-4 mt-2 border-l border-zinc-150 dark:border-zinc-850 ml-1.5 space-y-2">
        <TocNode :items="item.children" :active-id="activeId" />
      </div>
    </li>
  </ul>
</template>

<script setup lang="ts">
defineProps<{
  items: any[]
  activeId?: string
}>()

const scrollToHeading = (id: string) => {
  const el = document.getElementById(id)
  if (el) {
    const yOffset = -90 // Offset for header navbar
    const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset
    window.scrollTo({ top: y, behavior: 'smooth' })
    // Update hash in URL
    history.pushState(null, '', `#${id}`)
  }
}
</script>
