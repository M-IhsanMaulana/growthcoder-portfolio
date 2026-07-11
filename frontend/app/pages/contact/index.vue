<template>
  <div class="min-h-screen bg-zinc-50 dark:bg-[#09090B] pb-24 relative overflow-hidden grid-bg-subtle">

    <!-- Decorative glowing orbs -->
    <div class="absolute top-10 left-10 w-96 h-96 bg-brand-purple/10 dark:bg-brand-purple/5 blur-3xl rounded-full pointer-events-none"></div>
    <div class="absolute bottom-20 right-10 w-80 h-80 bg-brand-green/10 dark:bg-brand-green/5 blur-3xl rounded-full pointer-events-none"></div>

    <!-- ─── HERO HEADER SECTION ─────────────────────────────────────────── -->
    <div ref="heroSection" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 opacity-0 -translate-y-3 relative z-10">
      <div class="space-y-4 max-w-3xl">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/30 shadow-xs">
          <span class="w-1.5 h-1.5 rounded-full bg-brand-purple animate-pulse"></span>
          <span class="text-[10px] font-bold tracking-wider text-brand-purple dark:text-indigo-400 uppercase">Hubungi Saya</span>
        </div>

        <!-- Heading -->
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-[1.2] text-zinc-900 dark:text-white">
          Ada Ide Menarik? <span class="bg-gradient-to-r from-brand-purple to-brand-green bg-clip-text text-transparent">Mari Berdiskusi</span>
        </h1>

        <!-- Divider -->
        <div class="w-20 h-1 bg-gradient-to-r from-brand-purple to-brand-green rounded-full shadow-sm"></div>

        <!-- Subtitle -->
        <p class="text-sm md:text-base text-zinc-550 dark:text-zinc-400 leading-relaxed font-light mt-2 max-w-2xl">
          Pintu kolaborasi selalu terbuka. Kirimkan pesan Anda mengenai proyek baru, ide bisnis, tawaran kerja, atau sekadar menyapa dan berdiskusi.
        </p>
      </div>
    </div>

    <!-- ─── MAIN CONTENT GRID ───────────────────────────────────────────── -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT COLUMN: Contact Info & Socials -->
        <div ref="infoColumn" class="lg:col-span-5 space-y-6 opacity-0 -translate-x-4">
          
          <!-- Email Info Card -->
          <div class="glass-card-premium rounded-3xl p-6 border border-zinc-200/50 dark:border-zinc-800/40 relative overflow-hidden group hover:scale-[1.01] transition-all duration-350">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-brand-purple/5 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-brand-purple/10 flex items-center justify-center text-brand-purple flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div class="space-y-1">
                <span class="text-[10px] font-bold tracking-widest text-zinc-400 dark:text-zinc-500 uppercase">Kirim Email</span>
                <h3 class="text-base font-bold text-zinc-800 dark:text-zinc-200">
                  {{ settings?.contact_email || 'ihsan@growthcoder.id' }}
                </h3>
                <p class="text-xs text-zinc-550 dark:text-zinc-400 font-light">
                  Kirim kapan saja, saya akan membalas secepat mungkin.
                </p>
                <div class="pt-2 flex items-center gap-2">
                  <a
                    :href="`mailto:${settings?.contact_email || 'ihsan@growthcoder.id'}`"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-purple dark:text-indigo-400 hover:underline transition-all"
                  >
                    <span>Kirim langsung</span>
                    <i class="pi pi-arrow-up-right text-[10px]"></i>
                  </a>
                  <span class="text-zinc-300 dark:text-zinc-800">|</span>
                  <button
                    @click="copyEmail"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-brand-purple dark:hover:text-indigo-400 transition-colors cursor-pointer"
                  >
                    <i class="pi" :class="copied ? 'pi-check text-emerald-500' : 'pi-copy'"></i>
                    <span>{{ copied ? 'Tersalin!' : 'Salin alamat' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Location Info Card -->
          <div class="glass-card-premium rounded-3xl p-6 border border-zinc-200/50 dark:border-zinc-800/40 relative overflow-hidden group hover:scale-[1.01] transition-all duration-350">
            <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-brand-green/5 group-hover:scale-125 transition-transform duration-500"></div>
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-2xl bg-brand-green/10 flex items-center justify-center text-brand-green flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <div class="space-y-1">
                <span class="text-[10px] font-bold tracking-widest text-zinc-400 dark:text-zinc-500 uppercase">Lokasi</span>
                <h3 class="text-base font-bold text-zinc-800 dark:text-zinc-200">
                  {{ settings?.about_location || 'Bandung, Indonesia' }}
                </h3>
                <p class="text-xs text-zinc-550 dark:text-zinc-400 font-light">
                  Tersedia untuk proyek jarak jauh (remote) secara global atau pertemuan lokal.
                </p>
              </div>
            </div>
          </div>

          <!-- Quick Response Guarantee Card -->
          <div class="p-6 rounded-3xl bg-brand-purple/5 border border-brand-purple/10 dark:border-zinc-900/60 relative overflow-hidden">
            <h4 class="text-xs font-bold text-brand-purple dark:text-indigo-400 uppercase tracking-wider mb-1">Respons Terjamin</h4>
            <p class="text-xs text-zinc-550 dark:text-zinc-450 leading-relaxed font-light">
              Setiap pesan yang masuk melalui formulir ini diteruskan secara real-time ke bot Telegram pribadi saya. 
              Saya biasanya menanggapi pesan serius dalam kurun waktu kurang dari 24 jam.
            </p>
          </div>

          <!-- Social Links Area -->
          <div class="space-y-3 pt-2">
            <h4 class="text-xs font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">Temukan Saya di Media Sosial</h4>
            <div class="flex flex-wrap gap-3">
              <a 
                v-if="settings?.social_github" 
                :href="settings.social_github" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 hover:border-brand-purple/40 dark:hover:border-brand-purple/40 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:shadow-xs transition-all duration-300 cursor-pointer"
              >
                <i class="pi pi-github text-sm"></i>
                <span>GitHub</span>
              </a>

              <a 
                v-if="settings?.social_linkedin" 
                :href="settings.social_linkedin" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 hover:border-brand-purple/40 dark:hover:border-brand-purple/40 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:shadow-xs transition-all duration-300 cursor-pointer"
              >
                <i class="pi pi-linkedin text-sm"></i>
                <span>LinkedIn</span>
              </a>

              <a 
                v-if="settings?.social_telegram" 
                :href="settings.social_telegram" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 hover:border-brand-purple/40 dark:hover:border-brand-purple/40 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:shadow-xs transition-all duration-300 cursor-pointer"
              >
                <i class="pi pi-telegram text-sm"></i>
                <span>Telegram</span>
              </a>

              <a 
                v-if="settings?.social_instagram" 
                :href="settings.social_instagram" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 hover:border-brand-purple/40 dark:hover:border-brand-purple/40 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:shadow-xs transition-all duration-300 cursor-pointer"
              >
                <i class="pi pi-instagram text-sm"></i>
                <span>Instagram</span>
              </a>

              <a 
                v-if="settings?.social_twitter" 
                :href="settings.social_twitter" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800/80 hover:border-brand-purple/40 dark:hover:border-brand-purple/40 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:text-brand-purple dark:hover:text-indigo-400 hover:shadow-xs transition-all duration-300 cursor-pointer"
              >
                <i class="pi pi-twitter text-sm"></i>
                <span>Twitter/X</span>
              </a>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Contact Form Card -->
        <div ref="formColumn" class="lg:col-span-7 opacity-0 translate-x-4">
          <div class="glass-card-premium rounded-3xl p-6 sm:p-8 space-y-6 relative overflow-hidden border border-zinc-200/50 dark:border-zinc-800/40 shadow-xl">
            
            <!-- Success Overlay Morph -->
            <transition name="fade-scale" mode="out-in">
              <div v-if="successMessage" class="py-10 text-center space-y-6 flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-500 text-3xl animate-bounce">
                  <i class="pi pi-check-circle"></i>
                </div>
                <div class="space-y-2">
                  <h3 class="text-2xl font-black text-zinc-900 dark:text-white">Pesan Berhasil Terkirim!</h3>
                  <p class="text-sm text-zinc-550 dark:text-zinc-450 font-light max-w-md mx-auto leading-relaxed">
                    {{ successMessage }}
                  </p>
                </div>
                <button 
                  class="bg-brand-purple hover:bg-brand-navy-hover text-white px-6 py-3 rounded-2xl text-xs font-bold transition-all shadow-md shadow-brand-purple/20 cursor-pointer"
                  @click="resetForm"
                >
                  <i class="pi pi-refresh mr-1.5"></i>
                  <span>Kirim Pesan Lain</span>
                </button>
              </div>

              <!-- Contact Form -->
              <form v-else @submit.prevent="handleSubmit" class="space-y-5">
                <div class="space-y-1">
                  <h2 class="text-lg font-black text-zinc-900 dark:text-white">Kirim Pesan</h2>
                  <p class="text-xs text-zinc-500 dark:text-zinc-400 font-light">
                    Silakan isi detail formulir di bawah ini dengan benar.
                  </p>
                </div>

                <!-- General Error Banner -->
                <div 
                  v-if="generalError" 
                  class="p-4 bg-red-500/5 border border-red-500/15 rounded-2xl text-xs text-red-500 font-medium flex items-start gap-2.5"
                >
                  <i class="pi pi-exclamation-triangle mt-0.5"></i>
                  <span>{{ generalError }}</span>
                </div>

                <!-- Honeypot Field (Visually hidden for human, filled by bots) -->
                <div class="absolute left-[-9999px] opacity-0 pointer-events-none" aria-hidden="true">
                  <label for="website">Leave this field empty</label>
                  <input 
                    id="website" 
                    v-model="website" 
                    type="text" 
                    name="website" 
                    tabindex="-1" 
                    autocomplete="off" 
                  />
                </div>

                <!-- Form Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <!-- Name Field -->
                  <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Nama Lengkap *</label>
                    <input 
                      id="name"
                      v-model="name"
                      type="text"
                      placeholder="Masukkan nama Anda"
                      required
                      class="w-full !px-4 !py-3 rounded-2xl bg-white dark:bg-zinc-900 border text-xs font-medium text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none transition-all duration-300 shadow-xs"
                      :class="errors.name 
                        ? 'border-red-500/60 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' 
                        : 'border-zinc-200 dark:border-zinc-800 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/15'"
                    />
                    <p v-if="errors.name" class="text-[10px] text-red-500 font-semibold">{{ errors.name }}</p>
                  </div>

                  <!-- Email Field -->
                  <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Alamat Email *</label>
                    <input 
                      id="email"
                      v-model="email"
                      type="email"
                      placeholder="nama@email.com"
                      required
                      class="w-full !px-4 !py-3 rounded-2xl bg-white dark:bg-zinc-900 border text-xs font-medium text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none transition-all duration-300 shadow-xs"
                      :class="errors.email 
                        ? 'border-red-500/60 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' 
                        : 'border-zinc-200 dark:border-zinc-800 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/15'"
                    />
                    <p v-if="errors.email" class="text-[10px] text-red-500 font-semibold">{{ errors.email }}</p>
                  </div>
                </div>

                <!-- Subject Field -->
                <div class="space-y-1.5">
                  <label for="subject" class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Subjek Pesan</label>
                  <input 
                    id="subject"
                    v-model="subject"
                    type="text"
                    placeholder="Apa hal yang ingin didiskusikan?"
                    class="w-full !px-4 !py-3 rounded-2xl bg-white dark:bg-zinc-900 border text-xs font-medium text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none transition-all duration-300 shadow-xs border-zinc-200 dark:border-zinc-800 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/15"
                  />
                  <p v-if="errors.subject" class="text-[10px] text-red-500 font-semibold">{{ errors.subject }}</p>
                </div>

                <!-- Message Field -->
                <div class="space-y-1.5">
                  <div class="flex justify-between items-center">
                    <label for="message" class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Isi Pesan *</label>
                    <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">{{ message.length }}/5000</span>
                  </div>
                  <textarea 
                    id="message"
                    v-model="message"
                    rows="6"
                    placeholder="Tuliskan detail pesan Anda di sini..."
                    required
                    maxlength="5000"
                    class="w-full !px-4 !py-3 rounded-2xl bg-white dark:bg-zinc-900 border text-xs font-medium text-zinc-800 dark:text-zinc-200 placeholder-zinc-400 focus:outline-none transition-all duration-300 shadow-xs resize-none"
                    :class="errors.message 
                      ? 'border-red-500/60 focus:border-red-500 focus:ring-4 focus:ring-red-500/10' 
                      : 'border-zinc-200 dark:border-zinc-800 focus:border-brand-purple focus:ring-4 focus:ring-brand-purple/15'"
                  ></textarea>
                  <p v-if="errors.message" class="text-[10px] text-red-500 font-semibold">{{ errors.message }}</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                  <button 
                    type="submit"
                    :disabled="loading"
                    class="w-full !py-3.5 rounded-2xl bg-brand-purple hover:bg-brand-navy-hover text-white text-xs font-extrabold flex items-center justify-center gap-2 transition-all duration-300 shadow-md shadow-brand-purple/20 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed hover:-translate-y-0.5 active:translate-y-0"
                  >
                    <span v-if="loading" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                    <i v-else class="pi pi-send"></i>
                    <span>{{ loading ? 'Mengirim...' : 'Kirim Pesan' }}</span>
                  </button>
                </div>
              </form>
            </transition>
          </div>
        </div>

      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

definePageMeta({ layout: 'default' })

// ── Settings ──────────────────────────────────────────────────────────
const { settings, fetchSettings } = useSettings()
await fetchSettings()

useSeoMeta({
  title: 'Kontak',
  description: 'Hubungi Muhammad Ihsan Maulana untuk kolaborasi proyek, diskusi teknis, atau peluang karir.',
  ogTitle: 'Kontak | growthcoder.id',
  ogDescription: 'Hubungi Muhammad Ihsan Maulana untuk kolaborasi proyek, diskusi teknis, atau peluang karir.',
  ogImage: settings.value?.profile_photo?.urls?.medium || 'https://growthcoder.id/logo-gc-dark.png',
  ogType: 'website',
})

useHead({
  link: [
    { rel: 'canonical', href: 'https://growthcoder.id/contact' }
  ],
  script: [
    {
      type: 'application/ld+json',
      innerHTML: JSON.stringify({
        '@context': 'https://schema.org',
        '@graph': [
          {
            '@type': 'ContactPage',
            '@id': 'https://growthcoder.id/contact/#webpage',
            'url': 'https://growthcoder.id/contact',
            'name': 'Kontak - growthcoder.id',
            'description': 'Hubungi Muhammad Ihsan Maulana untuk kolaborasi proyek, diskusi teknis, atau peluang karir.',
            'mainEntity': {
              '@type': 'Person',
              'name': settings.value?.owner_full_name || 'Muhammad Ihsan Maulana',
              'email': settings.value?.contact_email || 'ihsan@growthcoder.id',
              'url': 'https://growthcoder.id'
            }
          }
        ]
      })
    }
  ]
})

// ── Form State ────────────────────────────────────────────────────────
const name = ref('')
const email = ref('')
const subject = ref('')
const message = ref('')
const website = ref('') // Honeypot spam trap
const errors = ref<Record<string, string>>({})
const generalError = ref('')
const successMessage = ref('')
const loading = ref(false)
const copied = ref(false)

// Copy Email Utility
const copyEmail = () => {
  const emailText = settings.value?.contact_email || 'ihsan@growthcoder.id'
  if (process.client) {
    navigator.clipboard.writeText(emailText)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2500)
  }
}

// Reset Form State
const resetForm = () => {
  name.value = ''
  email.value = ''
  subject.value = ''
  message.value = ''
  website.value = ''
  errors.value = {}
  generalError.value = ''
  successMessage.value = ''
}

// Submit Form Handler
const handleSubmit = async () => {
  // Clear previous errors
  errors.value = {}
  generalError.value = ''
  successMessage.value = ''

  // Client-side simple validation checks
  let hasClientError = false
  if (!name.value.trim()) {
    errors.value.name = 'Nama lengkap wajib diisi.'
    hasClientError = true
  }
  if (!email.value.trim()) {
    errors.value.email = 'Alamat email wajib diisi.'
    hasClientError = true
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = 'Format alamat email tidak valid.'
    hasClientError = true
  }
  if (!message.value.trim()) {
    errors.value.message = 'Isi pesan wajib diisi.'
    hasClientError = true
  } else if (message.value.length < 10) {
    errors.value.message = 'Isi pesan minimal harus 10 karakter.'
    hasClientError = true
  }

  if (hasClientError) return

  loading.value = true
  try {
    const config = useRuntimeConfig()
    
    // Honeypot client bypass block
    if (website.value) {
      // Simulate success to trick bots
      await new Promise(resolve => setTimeout(resolve, 1500))
      successMessage.value = 'Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda.'
      resetForm()
      return
    }

    const response = await $fetch<any>('/contact', {
      method: 'POST',
      baseURL: config.public.apiBase,
      headers: {
        'Accept': 'application/json',
        'X-API-Key': config.public.apiKey,
      },
      body: {
        name: name.value,
        email: email.value,
        subject: subject.value,
        message: message.value,
        website: website.value,
      }
    })

    successMessage.value = response.message || 'Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda.'
  } catch (err: any) {
    console.error('Submit contact form error:', err)
    if (err.status === 422 && err.data?.errors) {
      // Backend validation failed
      const backendErrors: Record<string, string[]> = err.data.errors
      for (const [key, val] of Object.entries(backendErrors)) {
        errors.value[key] = val[0]
      }
    } else {
      // Server error / network error
      generalError.value = err.data?.message || 'Terjadi kesalahan sistem. Silakan coba kembali beberapa saat lagi.'
    }
  } finally {
    loading.value = false
  }
}

// ── Animations (GSAP) ──────────────────────────────────────────────────
const { $gsap } = useNuxtApp()
const heroSection = ref<HTMLElement | null>(null)
const infoColumn = ref<HTMLElement | null>(null)
const formColumn = ref<HTMLElement | null>(null)

const initAnimations = () => {
  if (!$gsap) return

  // Hero Section slide in from top
  if (heroSection.value) {
    $gsap.to(heroSection.value, { 
      opacity: 1, 
      y: 0, 
      duration: 0.65, 
      ease: 'power3.out', 
      delay: 0.05 
    })
  }

  // Info Column slide in from left
  if (infoColumn.value) {
    $gsap.to(infoColumn.value, { 
      opacity: 1, 
      x: 0, 
      duration: 0.65, 
      ease: 'power2.out',
      delay: 0.15 
    })
  }

  // Form Column slide in from right
  if (formColumn.value) {
    $gsap.to(formColumn.value, { 
      opacity: 1, 
      x: 0, 
      duration: 0.65, 
      ease: 'power2.out',
      delay: 0.25 
    })
  }
}

onMounted(() => {
  nextTick(() => {
    initAnimations()
  })
})
</script>

<style scoped>
@reference "../../assets/css/main.css";

/* Slide & scale morph transition for success card */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.35s ease-in-out;
}
.fade-scale-enter-from {
  opacity: 0;
  transform: scale(0.97) translateY(5px);
}
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.97) translateY(-5px);
}
</style>
