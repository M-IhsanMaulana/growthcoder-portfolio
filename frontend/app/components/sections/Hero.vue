<template>
  <section class="relative w-full min-h-[calc(100vh-64px)] flex flex-col justify-center items-center overflow-hidden grid-bg-subtle py-12 lg:py-0">
    <!-- Background Radial Glows -->
    <div class="absolute top-[20%] left-[20%] w-[350px] h-[350px] rounded-full bg-purple-600/10 dark:bg-purple-600/15 blur-[100px] pointer-events-none z-0"></div>
    <div class="absolute bottom-[20%] right-[15%] w-[450px] h-[450px] rounded-full bg-blue-600/10 dark:bg-blue-600/15 blur-[120px] pointer-events-none z-0"></div>

    <!-- Glowing Particles -->
    <div 
      v-for="n in 12" 
      :key="'particle-' + n" 
      class="absolute rounded-full bg-white opacity-20 pointer-events-none animate-pulse z-0" 
      :style="getParticleStyle(n)"
    ></div>

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8">
      <!-- Left Column: Content -->
      <div class="max-w-2xl flex-1 space-y-8 text-left">
        <!-- Availability Badge -->
        <div class="animate-left-item opacity-0">
          <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium bg-emerald-500/5 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse mr-2"></span>
            Available for freelance &amp; collaboration
          </span>
        </div>
        
        <!-- Large Headline -->
        <h1 class="animate-left-item opacity-0 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.1] text-zinc-900 dark:text-white">
          <template v-if="settingsPending">
            <Skeleton width="14rem" height="3.5rem" class="mb-2" />
            <Skeleton width="20rem" height="3.5rem" />
          </template>
          <template v-else>
            Hi, I'm <span class="text-zinc-800 dark:text-zinc-300 font-bold">{{ displayName }}</span>.<br />
            <span class="text-blue-purple-gradient">{{ settings?.owner_title || 'Software Developer' }}</span> &<br />
            Informatics Student.
          </template>
        </h1>
        
        <!-- Descriptive Paragraph -->
        <div class="animate-left-item opacity-0">
          <template v-if="settingsPending">
            <div class="space-y-2">
              <Skeleton width="100%" height="1rem" />
              <Skeleton width="92%" height="1rem" />
              <Skeleton width="75%" height="1rem" />
            </div>
          </template>
          <p v-else class="text-base sm:text-lg text-zinc-600 dark:text-zinc-400 leading-relaxed font-normal">
            {{ settings?.hero_subheadline || 'Informatics student and software developer specializing in building modern web applications, backend systems, robust REST APIs, and native mobile applications.' }}
          </p>
        </div>
                <!-- Action Buttons -->
        <div class="animate-left-item opacity-0 flex flex-wrap items-center gap-4">
          <NuxtLink 
            :to="settings?.hero_cta_url || '/proyek'" 
            class="inline-flex items-center justify-center px-7 py-3.5 font-semibold rounded-xl bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100 border border-transparent shadow-lg transition-all duration-300 hover:-translate-y-0.5 cursor-pointer"
          >
            {{ settings?.hero_cta_text || 'View Projects' }}
          </NuxtLink>
          <NuxtLink 
            to="/contact" 
            class="inline-flex items-center justify-center px-7 py-3.5 font-semibold rounded-xl border border-zinc-200 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-all duration-300 hover:-translate-y-0.5 cursor-pointer"
          >
            Contact Me
          </NuxtLink>
        </div>

        <!-- Social Media Links -->
        <div class="animate-left-item opacity-0 flex items-center space-x-5 pt-2">
          <!-- GitHub -->
          <a 
            v-if="settingsPending || settings?.social_github"
            :href="settings?.social_github || '#'" 
            target="_blank" 
            rel="noopener noreferrer" 
            class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-300" 
            aria-label="GitHub"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
            </svg>
          </a>
          <!-- LinkedIn -->
          <a 
            v-if="settingsPending || settings?.social_linkedin"
            :href="settings?.social_linkedin || '#'" 
            target="_blank" 
            rel="noopener noreferrer" 
            class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-300" 
            aria-label="LinkedIn"
          >
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
          </a>
          <!-- Email -->
          <a 
            v-if="settingsPending || settings?.contact_email"
            :href="settings?.contact_email ? `mailto:${settings.contact_email}` : '#'" 
            class="text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors duration-300" 
            aria-label="Email"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </a>
        </div>

        <!-- Statistics Layout -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-4 border-t border-zinc-100 dark:border-zinc-900">
          <!-- Projects stat -->
          <div class="animate-stat-item opacity-0">
            <template v-if="statsPending">
              <Skeleton width="4rem" height="2.25rem" class="mb-1" />
              <Skeleton width="3.5rem" height="0.75rem" />
            </template>
            <template v-else>
              <h3 class="text-3xl font-extrabold text-zinc-900 dark:text-white">{{ projectsCount }}+</h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-wider font-semibold">Projects</p>
            </template>
          </div>
          <!-- Technologies stat -->
          <div class="animate-stat-item opacity-0">
            <template v-if="statsPending">
              <Skeleton width="4rem" height="2.25rem" class="mb-1" />
              <Skeleton width="5rem" height="0.75rem" />
            </template>
            <template v-else>
              <h3 class="text-3xl font-extrabold text-zinc-900 dark:text-white">{{ technologiesCount }}+</h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-wider font-semibold">Technologies</p>
            </template>
          </div>
          <!-- Extra stats from about_stats (Years Learning, Passion) -->
          <div 
            v-for="(stat, index) in extraStats" 
            :key="index"
            class="animate-stat-item opacity-0"
          >
            <template v-if="statsPending">
              <Skeleton width="4rem" height="2.25rem" class="mb-1" />
              <Skeleton width="4.5rem" height="0.75rem" />
            </template>
            <template v-else>
              <h3 class="text-3xl font-extrabold text-zinc-900 dark:text-white">{{ stat.value }}</h3>
              <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-wider font-semibold">{{ stat.label }}</p>
            </template>
          </div>
        </div>
      </div>

      <!-- Right Column: Premium Floating Portrait Card & Badges -->
      <div class="flex-1 flex items-center justify-center relative w-full min-h-[420px] lg:min-h-[500px]">
        <!-- Soft Glow behind Graphic -->
        <div class="absolute w-80 h-80 rounded-full bg-gradient-to-tr from-purple-500 to-blue-500 opacity-25 blur-[120px] pointer-events-none"></div>

        <!-- Programming Illustration (No Box, No Code Editor) -->
        <div class="animate-portrait-card opacity-0 relative w-full max-w-[340px] sm:max-w-[450px] lg:max-w-[490px] flex items-center justify-center z-10">
          <NuxtImg 
            src="/programming.svg" 
            alt="Programming Illustration"
            class="w-full h-auto object-contain drop-shadow-[0_10px_40px_rgba(99,102,241,0.12)] hover:scale-[1.02] transition-transform duration-700 ease-out"
            loading="eager"
            fetchpriority="high"
          />
        </div>

        <!-- Floating Stack Badges around the Illustration -->
        <!-- Laravel Badge -->
        <div class="animate-float-badge opacity-0 absolute top-[10%] left-[2%] sm:left-[8%] z-20 animate-float-1">
          <div class="glass-card-premium rounded-2xl p-2.5 flex items-center justify-center shadow-lg border border-zinc-200/30 dark:border-white/5 w-12 h-12" title="Laravel">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-6 h-6 fill-[#FF2D20]"><path d="M568.6 179.8C568.5 179.6 568.4 179.3 568.3 179.1C568.2 178.7 568 178.3 567.8 177.9C567.6 177.7 567.5 177.4 567.3 177.2C567.1 176.9 566.8 176.6 566.6 176.3C566.4 176.1 566.1 175.9 565.8 175.7C565.5 175.5 565.2 175.2 564.9 175L468.6 119.5C467.4 118.8 466 118.4 464.6 118.4C463.2 118.4 461.8 118.8 460.6 119.5L364.3 175C364 175.2 363.7 175.4 363.4 175.7C363.1 175.9 362.9 176.1 362.6 176.3C362.3 176.6 362.1 176.9 361.9 177.2C361.7 177.4 361.5 177.6 361.4 177.9C361.2 178.3 361 178.7 360.9 179.1C360.8 179.3 360.7 179.5 360.6 179.8C360.4 180.5 360.3 181.2 360.3 181.9L360.3 287.1L280.1 333.3L280.1 127.4C280.1 126.7 280 126 279.8 125.3C279.7 125.1 279.6 124.9 279.5 124.6C279.4 124.2 279.2 123.8 279 123.4C278.9 123.1 278.6 122.9 278.5 122.7C278.3 122.4 278 122.1 277.8 121.8C277.6 121.6 277.3 121.4 277 121.2C276.7 121 276.4 120.7 276.1 120.5L179.8 65.1C178.6 64.4 177.2 64 175.8 64C174.4 64 173 64.4 171.8 65.1L75.5 120.5C75.2 120.7 74.9 120.9 74.6 121.2C74.3 121.4 74.1 121.6 73.8 121.8C73.5 122.1 73.3 122.4 73.1 122.7C72.9 123 72.7 123.2 72.5 123.4C72.3 123.8 72.1 124.2 72 124.6C71.9 124.8 71.8 125 71.7 125.3C71.5 126 71.4 126.7 71.4 127.4L71.4 457.1C71.4 458.5 71.8 459.9 72.5 461.1C73.2 462.3 74.2 463.3 75.4 464L268 574.9C268.4 575.1 268.9 575.3 269.3 575.4C269.5 575.6 269.7 575.6 269.9 575.7C271.2 576.1 272.7 576.1 274 575.7C274.2 575.6 274.4 575.5 274.6 575.5C275.1 575.3 275.6 575.2 276 574.9L468.6 464.1C469.8 463.4 470.8 462.4 471.5 461.2C472.2 460 472.6 458.6 472.6 457.2L472.6 351.9L564.8 298.8C566 298.1 567 297.1 567.7 295.8C568.4 294.5 568.8 293.2 568.8 291.8L568.8 182C568.8 181.3 568.7 180.6 568.6 179.9zM175.8 81.3L256 127.4L175.8 173.6L95.6 127.4L175.8 81.2zM264 141.3L264 342.6C230.8 361.7 204.1 377.1 183.8 388.8L183.8 187.5C217 168.4 243.7 153 264 141.3zM264 554.1L87.5 452.5L87.5 141.3C107.8 153 134.6 168.4 167.7 187.5L167.7 402.7C167.7 403 167.8 403.3 167.8 403.6C167.8 404 167.9 404.4 168 404.8C168.1 405.1 168.2 405.4 168.4 405.7C168.5 406 168.7 406.4 168.8 406.7C169 407 169.2 407.2 169.4 407.5C169.6 407.8 169.8 408.1 170.1 408.3C170.3 408.5 170.6 408.7 170.9 408.9C171.2 408.9 171.5 409.4 171.8 409.6L264 461.8L264.1 554.2zM272 447.9L192 402.6C246.7 371.1 305.5 337.3 368.3 301.1L448.4 347.2C419 364 360.2 397.5 272 447.9zM456.5 452.5L280 554.1L280 461.8C381.4 404 440.2 370.4 456.5 361L456.5 452.4zM456.5 333.4C436.2 321.8 409.4 306.4 376.3 287.3L376.3 195.9C396.6 207.6 423.4 223 456.5 242.1L456.5 333.4zM464.5 228.1L384.3 181.9L464.5 135.7L544.7 181.8L464.5 228zM472.5 333.4L472.5 242.1C505.7 223 532.5 207.6 552.8 195.9L552.8 287.3L472.5 333.5z"/></svg>
          </div>
        </div>

        <!-- NextJS Badge -->
        <div class="animate-float-badge opacity-0 absolute top-[5%] right-[5%] sm:right-[10%] z-20 animate-float-2">
          <div class="glass-card-premium rounded-2xl p-2.5 flex items-center justify-center shadow-lg border border-zinc-200/30 dark:border-white/5 w-12 h-12" title="Next.js">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48" class="fill-zinc-800 dark:fill-white transition-colors duration-300">
              <path d="M18.974,31.5c0,0.828-0.671,1.5-1.5,1.5s-1.5-0.672-1.5-1.5v-14c0-0.653,0.423-1.231,1.045-1.43 c0.625-0.198,1.302,0.03,1.679,0.563l16.777,23.704C40.617,36.709,44,30.735,44,24c0-11-9-20-20-20S4,13,4,24s9,20,20,20 c3.192,0,6.206-0.777,8.89-2.122L18.974,22.216V31.5z M28.974,16.5c0-0.828,0.671-1.5,1.5-1.5s1.5,0.672,1.5,1.5v13.84l-3-4.227 V16.5z"></path>
            </svg>
          </div>
        </div>

        <!-- Vue Badge -->
        <div class="animate-float-badge opacity-0 absolute bottom-[20%] left-[-15px] sm:left-[0px] z-20 animate-float-3">
          <div class="glass-card-premium rounded-2xl p-2.5 flex items-center justify-center shadow-lg border border-zinc-200/30 dark:border-white/5 w-12 h-12" title="Vue.js">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 48 48">
              <polygon fill="#81c784" points="23.987,17 18.734,8 2.974,8 23.987,44 45,8 29.24,8"></polygon><polygon fill="#455a64" points="29.24,8 23.987,17 18.734,8 11.146,8 23.987,30 36.828,8"></polygon>
            </svg>
          </div>
        </div>

        <!-- GitHub Badge -->
        <div class="animate-float-badge opacity-0 absolute bottom-[10%] right-[5%] sm:right-[12%] z-20 animate-float-1">
          <div class="glass-card-premium rounded-2xl p-2.5 flex items-center justify-center shadow-lg border border-zinc-200/30 dark:border-white/5 w-12 h-12" title="GitHub">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-6 h-6 fill-zinc-800 dark:fill-white transition-colors duration-300"><path d="M280.5 426.5C214.5 418.5 168 371 168 309.5C168 284.5 177 257.5 192 239.5C185.5 223 186.5 188 194 173.5C214 171 241 181.5 257 196C276 190 296 187 320.5 187C345 187 365 190 383 195.5C398.5 181.5 426 171 446 173.5C453 187 454 222 447.5 239C463.5 258 472 283.5 472 309.5C472 371 425.5 417.5 358.5 426C375.5 437 387 461 387 488.5L387 540.5C387 555.5 399.5 564 414.5 558C505 523.5 576 433 576 321C576 179.5 461 64 319.5 64C178 64 64 179.5 64 321C64 432 134.5 524 229.5 558.5C243 563.5 256 554.5 256 541L256 501C249 504 240 506 232 506C199 506 179.5 488 165.5 454.5C160 441 154 433 142.5 431.5C136.5 431 134.5 428.5 134.5 425.5C134.5 419.5 144.5 415 154.5 415C169 415 181.5 424 194.5 442.5C204.5 457 215 463.5 227.5 463.5C240 463.5 248 459 259.5 447.5C268 439 274.5 431.5 280.5 426.5z"/></svg>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, computed, ref } from 'vue'

const { $gsap } = useNuxtApp()

// Fetch site settings via shared composable
const { settings, fetchSettings } = useSettings()
const settingsPending = ref(!settings.value)

// Fetch projects and technologies for stat counts
const { data: projectsResponse, pending: projectsPending } = await useFetchAPI<any>('/projects')
const { data: technologiesResponse, pending: technologiesPending } = await useFetchAPI<any>('/technologies')

// Derived: loading state for stats (all three must resolve)
const statsPending = computed(() => settingsPending.value || projectsPending.value || technologiesPending.value)

// Derived: display name (first name or full name from API)
const displayName = computed(() => {
  const full = settings.value?.owner_full_name
  if (!full) return 'Developer'
  // Show the last word as a "short name" — e.g. "Muhammad Ihsan Maulana" → "Maulana"
  // Change this logic if a nickname field is added in the future
  return full.split(' ').pop() ?? full
})

// Derived: project count
const projectsCount = computed(() => {
  const data = projectsResponse.value?.data
  return Array.isArray(data) ? data.length : 0
})

// Derived: technology count
const technologiesCount = computed(() => {
  const data = technologiesResponse.value?.data
  return Array.isArray(data) ? data.length : 0
})

// Derived: extra stats from about_stats (index 0 = Years, index 1 = Passion, etc.)
const extraStats = computed<Array<{ value: string; label: string }>>(() => {
  const stats = settings.value?.about_stats
  if (Array.isArray(stats) && stats.length > 0) {
    return stats.slice(0, 2) // up to 2 extra stats to fill the 4-col grid
  }
  // Fallback defaults
  return [
    { value: '3+', label: 'Years Learning' },
    { value: '100%', label: 'Passion' },
  ]
})

// Generates dynamic styles for glow particles
const getParticleStyle = (n: number) => {
  const sizes = [3, 4, 5, 6, 4];
  const lefts = [8, 25, 48, 72, 88, 15, 38, 62, 80, 52, 94, 30];
  const tops = [12, 35, 78, 24, 85, 63, 45, 90, 15, 68, 40, 55];
  const durations = [3, 5, 4, 6, 7];
  
  return {
    width: `${sizes[n % sizes.length]}px`,
    height: `${sizes[n % sizes.length]}px`,
    left: `${lefts[n % lefts.length]}%`,
    top: `${tops[n % tops.length]}%`,
    animationDuration: `${durations[n % durations.length]}s`,
    animationDelay: `${n * 0.4}s`
  }
}

onMounted(async () => {
  // Fetch settings if not yet loaded
  if (!settings.value) {
    await fetchSettings()
    settingsPending.value = false
  } else {
    settingsPending.value = false
  }

  if (!$gsap) return

  const tl = $gsap.timeline({ delay: 0.15 })

  // Slide up and fade in the left items
  tl.fromTo(".animate-left-item", 
    { opacity: 0, y: 30 },
    { opacity: 1, y: 0, duration: 0.8, stagger: 0.12, ease: "power4.out" }
  )

  // Stagger entry for stats
  tl.fromTo(".animate-stat-item",
    { opacity: 0, scale: 0.85, y: 15 },
    { opacity: 1, scale: 1, y: 0, duration: 0.6, stagger: 0.08, ease: "back.out(1.3)" },
    "-=0.4"
  )

  // 3D-like tilt & scale entry for portrait card
  tl.fromTo(".animate-portrait-card",
    { opacity: 0, scale: 0.9, rotateY: 20, rotateX: 10 },
    { opacity: 1, scale: 1, rotateY: 0, rotateX: 0, duration: 1.2, ease: "power3.out" },
    "-=0.8"
  )

  // Scale in floating tech badges
  tl.fromTo(".animate-float-badge",
    { opacity: 0, scale: 0 },
    { opacity: 1, scale: 1, duration: 0.6, stagger: 0.05, ease: "back.out(1.5)" },
    "-=0.8"
  )
})
</script>

<style scoped>
/* Perspective for 3D rotation effects on entrance */
.animate-portrait-card {
  transform-style: preserve-3d;
  perspective: 1000px;
}
</style>
