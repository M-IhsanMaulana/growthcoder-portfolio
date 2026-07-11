<template>
  <div class="flex flex-col min-h-screen hero-gradient relative">
    <!-- Interactive Glow Cursor Background -->
    <UiGlowCursor />

    <!-- Header / Navbar -->
    <SectionsNavbar class="relative z-50" />

    <!-- Page Content Slot -->
    <main class="flex-grow relative z-10">
      <slot />
    </main>

    <!-- Footer -->
    <SectionsFooter class="relative z-10" />
  </div>
</template>

<script setup lang="ts">
const { fetchSettings } = useSettings()

// Fetch site settings on server-side to prevent flickering during client hydration
await fetchSettings()
</script>
