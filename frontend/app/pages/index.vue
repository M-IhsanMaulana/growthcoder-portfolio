<template>
  <div class="space-y-16 lg:space-y-24 pb-16 overflow-hidden">
    <!-- Hero Section -->
    <SectionsHero />

    <!-- Technologies I Work With Ticker -->
    <SectionsTechStack />

    <!-- Services Highlight Section -->
    <SectionsServices />

    <!-- Featured Projects Section -->
    <SectionsProjects />

    <!-- Blog & News Section -->
    <SectionsBlog />

    <!-- Contact CTA Section -->
    <SectionsContactCta />
  </div>
</template>

<script setup lang="ts">
const { settings, fetchSettings } = useSettings()
await fetchSettings()

useSeoMeta({
  title: 'Home',
  description: () => settings.value?.default_meta_desc || 'Premium Software Developer Portfolio of Muhammad Ihsan Maulana. Full-Stack Developer specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
  ogTitle: () => settings.value?.owner_full_name ? `${settings.value.owner_full_name}${settings.value.meta_title_suffix || ' | growthcoder.id'}` : 'Muhammad Ihsan Maulana | growthcoder.id',
  ogDescription: () => settings.value?.default_meta_desc || 'Premium Software Developer Portfolio of Muhammad Ihsan Maulana. Full-Stack Developer specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
  ogImage: () => settings.value?.default_og_image?.urls?.medium || settings.value?.default_og_image?.urls?.original || settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
  twitterCard: 'summary_large_image',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id' }
  ],
  script: [
    {
      type: 'application/ld+json',
      innerHTML: JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': [
          {
            '@type': 'WebSite',
            '@id': 'https://growthcoder.id/#website',
            'url': 'https://growthcoder.id',
            'name': settings.value?.site_name || 'growthcoder.id',
            'publisher': {
              '@id': 'https://growthcoder.id/#person'
            }
          },
          {
            '@type': 'Person',
            '@id': 'https://growthcoder.id/#person',
            'name': settings.value?.owner_full_name || 'Muhammad Ihsan Maulana',
            'jobTitle': settings.value?.owner_title || 'Full-Stack Developer',
            'url': 'https://growthcoder.id',
            'image': settings.value?.profile_photo?.urls?.original || 'https://growthcoder.id/portrait.png',
            'sameAs': [
              settings.value?.social_linkedin,
              settings.value?.social_github,
              settings.value?.social_telegram,
              settings.value?.social_instagram,
              settings.value?.social_twitter
            ].filter(Boolean)
          }
        ]
      })
    }
  ]
})
</script>
