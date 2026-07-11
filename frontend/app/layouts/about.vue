<template>
  <div class="flex flex-col min-h-screen hero-gradient bg-zinc-50 dark:bg-zinc-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <!-- Header / Navbar -->
    <SectionsNavbar />

    <!-- Sub Navigation & Page Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 flex flex-col">
      <!-- Sub Navigation Bar (Premium Pills Style) -->
      <div class="flex justify-center mb-12">
        <nav class="flex gap-1.5 p-1.5 bg-white/70 dark:bg-zinc-900/60 backdrop-blur-md rounded-2xl border border-gray-200/50 dark:border-zinc-800/40 shadow-md max-w-full overflow-x-auto">
          <NuxtLink 
            v-for="item in menuItems" 
            :key="item.path" 
            :to="item.path"
            class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-300 whitespace-nowrap cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
            active-class="!bg-brand-purple !text-white dark:!bg-brand-purple dark:!text-white shadow-lg shadow-brand-purple/20 font-bold"
          >
            {{ item.name }}
          </NuxtLink>
        </nav>
      </div>

      <!-- Child Slot -->
      <div class="flex-grow">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <SectionsFooter />
  </div>
</template>

<script setup lang="ts">
const menuItems = [
  { name: 'Tentang Saya', path: '/about' },
  { name: 'Pengalaman', path: '/about/experience' },
  { name: 'Pendidikan', path: '/about/education' },
  { name: 'Keahlian', path: '/about/skills' },
]

const { fetchSettings } = useSettings()

// Fetch site settings on server-side to prevent flickering during client hydration
await fetchSettings()
</script>
