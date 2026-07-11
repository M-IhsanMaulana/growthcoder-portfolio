<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import MediaLibraryModal from '@/components/MediaLibraryModal.vue';
import CKEditor from '@/components/CKEditor.vue';
import {
    Check,
    Sliders,
    User,
    Sparkles,
    Share2,
    Globe,
    FileText,
    ImageIcon,
    X,
    UploadCloud,
    Key,
    Copy,
    Info,
    Plus,
    Trash2,
    RefreshCw
} from '@lucide/vue';

interface AboutStat {
    value: string;
    label: string;
    emoji: string;
}

const props = defineProps<{
    settings: {
        id: number;
        owner_full_name: string;
        owner_title: string | null;
        profile_photo_id: number | null;
        profile_photo: any | null;
        hero_headline: string;
        hero_subheadline: string | null;
        hero_cta_text: string | null;
        hero_cta_url: string | null;
        cv_file_path: string | null;
        cv_file_url: string | null;
        api_key: string | null;
        social_linkedin: string | null;
        social_github: string | null;
        social_telegram: string | null;
        social_instagram: string | null;
        social_twitter: string | null;
        contact_email: string | null;
        site_name: string;
        meta_title_suffix: string | null;
        default_meta_desc: string | null;
        default_og_image_id: number | null;
        google_analytics_id: string | null;
        google_site_verification: string | null;
        default_og_image: any | null;
        // About fields
        about_bio: string | null;
        about_location: string | null;
        about_specialities: string[] | null;
        about_stats: AboutStat[] | null;
    };
}>();

// Active Tab
const activeTab = ref('profile');

// Forms Setup
const settingsForm = useForm({
    owner_full_name: props.settings.owner_full_name || '',
    owner_title: props.settings.owner_title || '',
    profile_photo_id: props.settings.profile_photo_id || null,
    hero_headline: props.settings.hero_headline || '',
    hero_subheadline: props.settings.hero_subheadline || '',
    hero_cta_text: props.settings.hero_cta_text || '',
    hero_cta_url: props.settings.hero_cta_url || '',
    api_key: props.settings.api_key || '',
    social_linkedin: props.settings.social_linkedin || '',
    social_github: props.settings.social_github || '',
    social_telegram: props.settings.social_telegram || '',
    social_instagram: props.settings.social_instagram || '',
    social_twitter: props.settings.social_twitter || '',
    contact_email: props.settings.contact_email || '',
    site_name: props.settings.site_name || '',
    meta_title_suffix: props.settings.meta_title_suffix || '',
    default_meta_desc: props.settings.default_meta_desc || '',
    default_og_image_id: props.settings.default_og_image_id || null,
    google_analytics_id: props.settings.google_analytics_id || '',
    google_site_verification: props.settings.google_site_verification || '',
    // About fields
    about_bio: props.settings.about_bio || '',
    about_location: props.settings.about_location || '',
    about_specialities: props.settings.about_specialities ?? [] as string[],
    about_stats: props.settings.about_stats ?? [] as AboutStat[],
});

// --- About: Specialities chip input ---
const specialityInput = ref('');

const addSpeciality = () => {
    const trimmed = specialityInput.value.trim();
    if (trimmed && !settingsForm.about_specialities.includes(trimmed)) {
        settingsForm.about_specialities.push(trimmed);
    }
    specialityInput.value = '';
};

const removeSpeciality = (index: number) => {
    settingsForm.about_specialities.splice(index, 1);
};

const onSpecialityKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addSpeciality();
    }
};

// --- About: Stats repeater ---
const addStat = () => {
    settingsForm.about_stats.push({ value: '', label: '', emoji: '' });
};

const removeStat = (index: number) => {
    settingsForm.about_stats.splice(index, 1);
};

// --- About: Sync stats from real DB ---
const isSyncing = ref(false);

const syncAboutStats = () => {
    isSyncing.value = true;
    router.post('/admin-cms/global-settings/sync-about-stats', {}, {
        onFinish: () => {
            isSyncing.value = false;
        },
    });
};

// API Key management
const copied = ref(false);

const generateApiKey = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 40; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    settingsForm.api_key = 'gc_' + result;
};

const copyApiKey = () => {
    if (!settingsForm.api_key) return;
    navigator.clipboard.writeText(settingsForm.api_key).then(() => {
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    });
};

const cvForm = useForm({
    cv_file: null as File | null,
});

// Profile Photo Management
const profilePhotoOpen = ref(false);
const selectedProfilePhoto = ref<any | null>(props.settings.profile_photo);

const selectProfilePhoto = (media: any) => {
    selectedProfilePhoto.value = media;
    settingsForm.profile_photo_id = media.id;
    profilePhotoOpen.value = false;
};

const removeProfilePhoto = () => {
    selectedProfilePhoto.value = null;
    settingsForm.profile_photo_id = null;
};

// OG Image Management
const ogImageOpen = ref(false);
const selectedOgImage = ref<any | null>(props.settings.default_og_image);

const selectOgImage = (media: any) => {
    selectedOgImage.value = media;
    settingsForm.default_og_image_id = media.id;
    ogImageOpen.value = false;
};

const removeOgImage = () => {
    selectedOgImage.value = null;
    settingsForm.default_og_image_id = null;
};

// CV File Management
const cvInput = ref<HTMLInputElement | null>(null);
const cvFileName = ref('');

const onCvFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        cvForm.cv_file = target.files[0];
        cvFileName.value = target.files[0].name;
    }
};

const submitCv = () => {
    cvForm.post('/admin-cms/global-settings/cv', {
        onSuccess: () => {
            cvForm.reset();
            cvFileName.value = '';
            if (cvInput.value) {
                cvInput.value.value = '';
            }
        }
    });
};

// Save Settings Form
const submitSettings = () => {
    settingsForm.put('/admin-cms/global-settings', {
        onSuccess: () => {
            // Updated successfully
        }
    });
};
</script>

<template>
    <Head title="Pengaturan Global" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto max-w-5xl mx-auto w-full">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-sidebar-border/70 pb-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Pengaturan Global & SEO</h1>
                <p class="text-xs text-muted-foreground">
                    Kelola data identitas profil, teks hero, link media sosial, SEO meta data, dan file resume CV Anda.
                </p>
            </div>
        </div>

        <!-- Custom tabs navigation -->
        <div class="flex border-b border-sidebar-border/70 gap-1 md:gap-2 overflow-x-auto pb-px">
            <button
                type="button"
                @click="activeTab = 'profile'"
                :class="activeTab === 'profile' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <User class="h-4 w-4" />
                Profil & Identitas
            </button>
            <button
                type="button"
                @click="activeTab = 'hero'"
                :class="activeTab === 'hero' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <Sparkles class="h-4 w-4" />
                Beranda & Hero
            </button>
            <button
                type="button"
                @click="activeTab = 'socials'"
                :class="activeTab === 'socials' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <Share2 class="h-4 w-4" />
                Tautan Sosial
            </button>
            <button
                type="button"
                @click="activeTab = 'seo'"
                :class="activeTab === 'seo' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <Globe class="h-4 w-4" />
                SEO & Metadata
            </button>
            <button
                type="button"
                @click="activeTab = 'security'"
                :class="activeTab === 'security' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <Key class="h-4 w-4" />
                Keamanan API
            </button>
            <button
                type="button"
                @click="activeTab = 'cv'"
                :class="activeTab === 'cv' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <FileText class="h-4 w-4" />
                Unggah CV
            </button>
            <button
                type="button"
                @click="activeTab = 'about'"
                :class="activeTab === 'about' ? 'border-primary text-primary font-bold' : 'border-transparent text-muted-foreground hover:text-foreground'"
                class="pb-3 px-3 border-b-2 text-sm font-semibold transition-all cursor-pointer flex items-center gap-2 whitespace-nowrap"
            >
                <Info class="h-4 w-4" />
                Halaman About
            </button>
        </div>

        <!-- Forms Container -->
        <div class="grid grid-cols-1 gap-6">
            
            <!-- TEXT AND LINKS SETTINGS FORM -->
            <form v-show="activeTab !== 'cv' && activeTab !== 'about'" @submit.prevent="submitSettings" class="space-y-6">
                
                <!-- PROFILE TAB CONTENT -->
                <div v-show="activeTab === 'profile'" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <User class="h-4 w-4 text-primary" />
                        Informasi Identitas Diri
                    </h2>
                    <p class="text-xs text-muted-foreground">Detail nama lengkap, deskripsi peran profesional, dan foto profil Anda yang akan ditampilkan di seluruh situs.</p>
                    <hr class="border-sidebar-border/50" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Profile Photo Picker -->
                        <div class="space-y-2">
                            <Label class="font-semibold text-xs text-foreground">Foto Profil</Label>
                            <div class="flex flex-col items-center justify-center border border-dashed border-sidebar-border rounded-xl p-4 bg-muted-foreground/5 relative min-h-[200px] gap-3">
                                <div v-if="selectedProfilePhoto" class="relative group">
                                    <img 
                                        :src="selectedProfilePhoto.urls?.medium || selectedProfilePhoto.urls?.original" 
                                        alt="Profile Photo" 
                                        class="h-32 w-32 object-cover rounded-full border border-sidebar-border shadow-md"
                                    />
                                    <button 
                                        type="button" 
                                        @click="removeProfilePhoto"
                                        class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-sm cursor-pointer"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <div v-else class="text-center space-y-2 flex flex-col items-center">
                                    <div class="h-16 w-16 rounded-full bg-neutral-200/50 dark:bg-neutral-800 flex items-center justify-center text-muted-foreground border">
                                        <User class="h-8 w-8" />
                                    </div>
                                    <p class="text-[10px] text-muted-foreground">Belum ada foto terpilih</p>
                                </div>
                                <Button 
                                    type="button" 
                                    size="sm" 
                                    variant="outline" 
                                    @click="profilePhotoOpen = true"
                                    class="mt-2 text-xs cursor-pointer"
                                >
                                    Pilih Foto
                                </Button>
                            </div>
                            <InputError :message="settingsForm.errors.profile_photo_id" />
                        </div>

                        <!-- Name and Title -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="grid gap-2">
                                <Label for="owner_full_name" class="font-semibold text-xs text-foreground">Nama Lengkap <span class="text-red-500">*</span></Label>
                                <Input
                                    id="owner_full_name"
                                    v-model="settingsForm.owner_full_name"
                                    placeholder="Masukkan nama lengkap Anda"
                                    required
                                />
                                <InputError :message="settingsForm.errors.owner_full_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="owner_title" class="font-semibold text-xs text-foreground">Jabatan / Peran Profesional</Label>
                                <Input
                                    id="owner_title"
                                    v-model="settingsForm.owner_title"
                                    placeholder="Contoh: Full-Stack Developer & Automation Specialist"
                                />
                                <InputError :message="settingsForm.errors.owner_title" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HERO SECTION TAB CONTENT -->
                <div v-show="activeTab === 'hero'" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <Sparkles class="h-4 w-4 text-primary" />
                        Tampilan Hero Beranda
                    </h2>
                    <p class="text-xs text-muted-foreground">Sesuaikan kalimat pembuka (headline) dan ajakan bertindak (CTA) utama pada halaman beranda Anda.</p>
                    <hr class="border-sidebar-border/50" />

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="hero_headline" class="font-semibold text-xs text-foreground">Headline Utama <span class="text-red-500">*</span></Label>
                            <Input
                                id="hero_headline"
                                v-model="settingsForm.hero_headline"
                                placeholder="Masukkan kalimat headline utama hero beranda"
                                required
                            />
                            <InputError :message="settingsForm.errors.hero_headline" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="hero_subheadline" class="font-semibold text-xs text-foreground">Sub-headline / Tagline</Label>
                            <textarea
                                id="hero_subheadline"
                                v-model="settingsForm.hero_subheadline"
                                placeholder="Masukkan deskripsi pelengkap headline"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                            <InputError :message="settingsForm.errors.hero_subheadline" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="hero_cta_text" class="font-semibold text-xs text-foreground">Teks Tombol CTA</Label>
                                <Input
                                    id="hero_cta_text"
                                    v-model="settingsForm.hero_cta_text"
                                    placeholder="Contoh: Hubungi Saya, Lihat Proyek"
                                />
                                <InputError :message="settingsForm.errors.hero_cta_text" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="hero_cta_url" class="font-semibold text-xs text-foreground">Link URL Tombol CTA</Label>
                                <Input
                                    id="hero_cta_url"
                                    v-model="settingsForm.hero_cta_url"
                                    placeholder="Contoh: /projects, /contact, atau URL eksternal"
                                />
                                <InputError :message="settingsForm.errors.hero_cta_url" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOCIAL LINKS TAB CONTENT -->
                <div v-show="activeTab === 'socials'" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <Share2 class="h-4 w-4 text-primary" />
                        Tautan Media Sosial & Kontak
                    </h2>
                    <p class="text-xs text-muted-foreground">Tautan jejaring profesional yang akan ditampilkan di header, footer, dan menu kontak Anda.</p>
                    <hr class="border-sidebar-border/50" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="social_linkedin" class="font-semibold text-xs text-foreground">LinkedIn URL</Label>
                            <Input
                                id="social_linkedin"
                                v-model="settingsForm.social_linkedin"
                                placeholder="https://linkedin.com/in/username"
                            />
                            <InputError :message="settingsForm.errors.social_linkedin" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="social_github" class="font-semibold text-xs text-foreground">GitHub URL</Label>
                            <Input
                                id="social_github"
                                v-model="settingsForm.social_github"
                                placeholder="https://github.com/username"
                            />
                            <InputError :message="settingsForm.errors.social_github" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="social_telegram" class="font-semibold text-xs text-foreground">Telegram URL</Label>
                            <Input
                                id="social_telegram"
                                v-model="settingsForm.social_telegram"
                                placeholder="https://t.me/username"
                            />
                            <InputError :message="settingsForm.errors.social_telegram" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="social_instagram" class="font-semibold text-xs text-foreground">Instagram URL</Label>
                            <Input
                                id="social_instagram"
                                v-model="settingsForm.social_instagram"
                                placeholder="https://instagram.com/username"
                            />
                            <InputError :message="settingsForm.errors.social_instagram" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="social_twitter" class="font-semibold text-xs text-foreground">Twitter / X URL</Label>
                            <Input
                                id="social_twitter"
                                v-model="settingsForm.social_twitter"
                                placeholder="https://x.com/username"
                            />
                            <InputError :message="settingsForm.errors.social_twitter" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="contact_email" class="font-semibold text-xs text-foreground">Email Kontak Resmi</Label>
                            <Input
                                id="contact_email"
                                type="email"
                                v-model="settingsForm.contact_email"
                                placeholder="yourname@domain.com"
                            />
                            <InputError :message="settingsForm.errors.contact_email" />
                        </div>
                    </div>
                </div>

                <!-- SEO METADATA TAB CONTENT -->
                <div v-show="activeTab === 'seo'" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <Globe class="h-4 w-4 text-primary" />
                        SEO & Metadata Fallback
                    </h2>
                    <p class="text-xs text-muted-foreground">Konfigurasi SEO bawaan yang digunakan untuk mesin pencari Google dan kartu social media sharing.</p>
                    <hr class="border-sidebar-border/50" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Default OG Image Picker -->
                        <div class="space-y-2">
                            <Label class="font-semibold text-xs text-foreground">Default OG Image</Label>
                            <div class="flex flex-col items-center justify-center border border-dashed border-sidebar-border rounded-xl p-4 bg-muted-foreground/5 relative min-h-[160px] gap-3">
                                <div v-if="selectedOgImage" class="relative group w-full">
                                    <img 
                                        :src="selectedOgImage.urls?.medium || selectedOgImage.urls?.original" 
                                        alt="Default OG Image" 
                                        class="h-28 w-full object-cover rounded-lg border border-sidebar-border shadow-sm"
                                    />
                                    <button 
                                        type="button" 
                                        @click="removeOgImage"
                                        class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow-sm cursor-pointer"
                                    >
                                        <X class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <div v-else class="text-center space-y-2 flex flex-col items-center">
                                    <ImageIcon class="h-8 w-8 text-muted-foreground" />
                                    <p class="text-[10px] text-muted-foreground">Belum ada gambar terpilih</p>
                                </div>
                                <Button 
                                    type="button" 
                                    size="sm" 
                                    variant="outline" 
                                    @click="ogImageOpen = true"
                                    class="mt-2 text-xs cursor-pointer"
                                >
                                    Pilih Gambar
                                </Button>
                            </div>
                            <InputError :message="settingsForm.errors.default_og_image_id" />
                        </div>

                        <!-- Site name, suffix, and meta description -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="grid gap-2">
                                <Label for="site_name" class="font-semibold text-xs text-foreground">Nama Situs <span class="text-red-500">*</span></Label>
                                <Input
                                    id="site_name"
                                    v-model="settingsForm.site_name"
                                    placeholder="Masukkan nama situs utama (contoh: growthcoder.id)"
                                    required
                                />
                                <InputError :message="settingsForm.errors.site_name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="meta_title_suffix" class="font-semibold text-xs text-foreground">Akhiran Judul Tab (Title Suffix)</Label>
                                <Input
                                    id="meta_title_suffix"
                                    v-model="settingsForm.meta_title_suffix"
                                    placeholder="Contoh: | growthcoder.id"
                                />
                                <InputError :message="settingsForm.errors.meta_title_suffix" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="default_meta_desc" class="font-semibold text-xs text-foreground">Deskripsi Meta Default (Max 160 Karakter)</Label>
                                <textarea
                                    id="default_meta_desc"
                                    v-model="settingsForm.default_meta_desc"
                                    placeholder="Masukkan deskripsi singkat ringkasan situs Anda untuk hasil penelusuran Google."
                                    rows="3"
                                    maxlength="160"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                ></textarea>
                                <span class="text-[10px] text-muted-foreground text-right block">{{ settingsForm.default_meta_desc?.length || 0 }}/160 karakter</span>
                                <InputError :message="settingsForm.errors.default_meta_desc" />
                            </div>

                            <div class="grid gap-2 border-t border-sidebar-border/50 pt-4">
                                <h3 class="text-xs font-bold text-foreground">Integrasi Analytics & Search Console</h3>
                                <p class="text-[10px] text-muted-foreground">Tambahkan integrasi Google Analytics 4 dan kode verifikasi Google Search Console.</p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="google_analytics_id" class="font-semibold text-xs text-foreground">Google Analytics 4 Measurement ID</Label>
                                <Input
                                    id="google_analytics_id"
                                    v-model="settingsForm.google_analytics_id"
                                    placeholder="Contoh: G-XXXXXXXXXX"
                                />
                                <InputError :message="settingsForm.errors.google_analytics_id" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="google_site_verification" class="font-semibold text-xs text-foreground">Google Search Console Verification Code</Label>
                                <Input
                                    id="google_site_verification"
                                    v-model="settingsForm.google_site_verification"
                                    placeholder="Masukkan kode verifikasi (isi dari atribut content di meta tag)"
                                />
                                <InputError :message="settingsForm.errors.google_site_verification" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API SECURITY TAB CONTENT -->
                <div v-show="activeTab === 'security'" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <Key class="h-4 w-4 text-primary" />
                        Keamanan & API Key
                    </h2>
                    <p class="text-xs text-muted-foreground flex flex-col gap-1">
                        <span>Konfigurasikan kunci otorisasi (API Key) untuk mengamankan data API backend growthcoder.id.</span>
                        <span class="text-red-500 font-semibold">Penting: Jangan bagikan API Key ini kepada siapapun! Pastikan frontend Nuxt Anda menyertakan key ini di header HTTP <code>X-API-Key</code>.</span>
                    </p>
                    <hr class="border-sidebar-border/50" />

                    <div class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="api_key" class="font-semibold text-xs text-foreground">API Key Token</Label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <Input
                                        id="api_key"
                                        type="text"
                                        v-model="settingsForm.api_key"
                                        placeholder="Masukkan atau buat token baru..."
                                        class="pr-10"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <Key class="h-4 w-4 text-muted-foreground" />
                                    </div>
                                </div>
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    @click="generateApiKey"
                                    class="text-xs cursor-pointer flex items-center gap-1.5"
                                >
                                    <Sparkles class="h-3.5 w-3.5" />
                                    Generate
                                </Button>
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    @click="copyApiKey"
                                    class="text-xs cursor-pointer flex items-center gap-1.5 min-w-[80px] justify-center"
                                >
                                    <Copy class="h-3.5 w-3.5" v-if="!copied" />
                                    <Check class="h-3.5 w-3.5 text-green-600" v-else />
                                    {{ copied ? 'Tersalin' : 'Salin' }}
                                </Button>
                            </div>
                            <span class="text-[10px] text-muted-foreground">Format yang direkomendasikan adalah diawali prefix <code>gc_</code> diikuti dengan string acak (misal: gc_abcdef12345...).</span>
                            <InputError :message="settingsForm.errors.api_key" />
                        </div>
                    </div>
                </div>

                <!-- SAVE BAR -->
                <div class="flex items-center justify-between border-t border-sidebar-border/70 pt-4 mt-6">
                    <span class="text-xs text-muted-foreground italic">
                        {{ settingsForm.isDirty ? '* Ada input belum disimpan' : 'Semua perubahan terinput' }}
                    </span>
                    <Button
                        type="submit"
                        :disabled="settingsForm.processing"
                        class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer h-10 px-6 shadow-sm flex items-center gap-2"
                    >
                        <Check class="h-4 w-4" v-if="!settingsForm.processing" />
                        {{ settingsForm.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </Button>
                </div>

            </form>

            <!-- ABOUT TAB CONTENT (SEPARATE FORM) -->
            <form v-show="activeTab === 'about'" @submit.prevent="submitSettings" class="space-y-6">

                <!-- Bio / Biografi Section -->
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <Info class="h-4 w-4 text-primary" />
                        Biografi & Narasi Profil
                    </h2>
                    <p class="text-xs text-muted-foreground">Tulis biografi profesional Anda yang akan ditampilkan di halaman About. Mendukung format teks kaya (heading, list, bold, dll).</p>
                    <hr class="border-sidebar-border/50" />

                    <!-- Bio Rich Text -->
                    <div class="grid gap-2">
                        <Label class="font-semibold text-xs text-foreground">Biografi / Bio</Label>
                        <CKEditor
                            v-model="settingsForm.about_bio"
                            placeholder="Ceritakan tentang diri Anda, latar belakang, dan passion Anda dalam dunia teknologi..."
                        />
                        <InputError :message="settingsForm.errors.about_bio" />
                    </div>

                    <!-- Location -->
                    <div class="grid gap-2">
                        <Label for="about_location" class="font-semibold text-xs text-foreground">Lokasi</Label>
                        <Input
                            id="about_location"
                            v-model="settingsForm.about_location"
                            placeholder="Contoh: Indonesia, Bandung"
                        />
                        <InputError :message="settingsForm.errors.about_location" />
                    </div>

                    <!-- Specialities Chip Input -->
                    <div class="grid gap-2">
                        <Label class="font-semibold text-xs text-foreground">Spesialisasi (Keahlian Utama)</Label>
                        <p class="text-[10px] text-muted-foreground">Tambahkan tag keahlian utama Anda. Tekan Enter atau koma untuk menambah.</p>

                        <!-- Chip Tags Display -->
                        <div class="flex flex-wrap gap-1.5 min-h-[36px] p-2 rounded-md border border-input bg-transparent">
                            <span
                                v-for="(spec, idx) in settingsForm.about_specialities"
                                :key="idx"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-primary/10 text-primary text-xs font-medium border border-primary/20"
                            >
                                {{ spec }}
                                <button
                                    type="button"
                                    @click="removeSpeciality(idx)"
                                    class="text-primary/60 hover:text-red-500 transition-colors cursor-pointer ml-0.5"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </span>
                            <input
                                v-model="specialityInput"
                                @keydown="onSpecialityKeydown"
                                @blur="addSpeciality"
                                type="text"
                                placeholder="Ketik spesialisasi lalu Enter..."
                                class="flex-1 min-w-[160px] bg-transparent text-xs outline-none placeholder:text-muted-foreground"
                            />
                        </div>
                        <InputError :message="settingsForm.errors.about_specialities" />
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                                <Sliders class="h-4 w-4 text-primary" />
                                Statistik Pencapaian
                            </h2>
                            <p class="text-xs text-muted-foreground mt-1">Angka-angka yang ditampilkan di halaman About (contoh: 15+ Projects, 3+ Years Learning).</p>
                        </div>
                        <!-- Sync Button -->
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="syncAboutStats"
                            :disabled="isSyncing"
                            class="text-xs cursor-pointer flex items-center gap-1.5 shrink-0"
                        >
                            <RefreshCw class="h-3.5 w-3.5" :class="{ 'animate-spin': isSyncing }" />
                            {{ isSyncing ? 'Menyinkronkan...' : 'Sinkronisasi dari Data Real' }}
                        </Button>
                    </div>
                    <p class="text-[10px] text-muted-foreground bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-2.5">
                        💡 Tombol sinkronisasi hanya memperbarui nilai <strong>Projects</strong> dan <strong>Technologies</strong> dari database secara otomatis. Stat lain (Years Learning, Passion) tetap manual.
                    </p>
                    <hr class="border-sidebar-border/50" />

                    <!-- Stats Repeater -->
                    <div class="space-y-3">
                        <div
                            v-for="(stat, idx) in settingsForm.about_stats"
                            :key="idx"
                            class="flex items-center gap-3 p-3 rounded-xl border border-sidebar-border bg-muted/30 hover:bg-muted/50 transition-colors"
                        >
                            <!-- Emoji -->
                            <div class="grid gap-1 w-16 shrink-0">
                                <Label class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wide">Emoji</Label>
                                <Input
                                    v-model="stat.emoji"
                                    placeholder="📁"
                                    class="text-center text-base h-9"
                                />
                            </div>
                            <!-- Value -->
                            <div class="grid gap-1 w-28 shrink-0">
                                <Label class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wide">Nilai</Label>
                                <Input
                                    v-model="stat.value"
                                    placeholder="15+"
                                    class="h-9"
                                />
                            </div>
                            <!-- Label -->
                            <div class="grid gap-1 flex-1">
                                <Label class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wide">Label</Label>
                                <Input
                                    v-model="stat.label"
                                    placeholder="Projects Completed"
                                    class="h-9"
                                />
                            </div>
                            <!-- Remove -->
                            <button
                                type="button"
                                @click="removeStat(idx)"
                                class="mt-4 p-1.5 rounded-lg text-muted-foreground hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer shrink-0"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Empty state -->
                        <div
                            v-if="settingsForm.about_stats.length === 0"
                            class="text-center py-8 text-xs text-muted-foreground border border-dashed border-sidebar-border rounded-xl"
                        >
                            Belum ada statistik. Tambahkan dengan tombol di bawah.
                        </div>

                        <!-- Add Button -->
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addStat"
                            class="w-full text-xs cursor-pointer flex items-center gap-1.5 border-dashed"
                        >
                            <Plus class="h-3.5 w-3.5" />
                            Tambah Statistik
                        </Button>
                    </div>
                </div>

                <!-- SAVE BAR -->
                <div class="flex items-center justify-between border-t border-sidebar-border/70 pt-4 mt-6">
                    <span class="text-xs text-muted-foreground italic">
                        {{ settingsForm.isDirty ? '* Ada input belum disimpan' : 'Semua perubahan terinput' }}
                    </span>
                    <Button
                        type="submit"
                        :disabled="settingsForm.processing"
                        class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer h-10 px-6 shadow-sm flex items-center gap-2"
                    >
                        <Check class="h-4 w-4" v-if="!settingsForm.processing" />
                        {{ settingsForm.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </Button>
                </div>

            </form>

            <!-- UPLOAD CV TAB CONTENT (SEPARATE FORM) -->
            <div v-show="activeTab === 'cv'" class="space-y-6">
                <form @submit.prevent="submitCv" class="rounded-xl border border-sidebar-border bg-card p-5 space-y-4 shadow-xs">
                    <h2 class="text-sm font-bold text-foreground flex items-center gap-2">
                        <FileText class="h-4 w-4 text-primary" />
                        Unggah Resume / CV Terbaru
                    </h2>
                    <p class="text-xs text-muted-foreground">Unggah file CV/Resume Anda dalam format PDF dengan ukuran maksimal 5MB. File yang baru akan menggantikan CV yang lama.</p>
                    <hr class="border-sidebar-border/50" />

                    <div class="space-y-6">
                        <!-- Current CV Status -->
                        <div class="p-4 bg-neutral-50/50 dark:bg-neutral-900/50 border border-sidebar-border rounded-xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center border border-red-200 dark:border-red-800">
                                    <FileText class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-foreground">File CV Saat Ini</h3>
                                    <p class="text-[10px] text-muted-foreground">
                                        {{ settings.cv_file_path ? 'cv-latest.pdf' : 'Belum ada file CV diunggah' }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="settings.cv_file_url">
                                <a 
                                    :href="settings.cv_file_url" 
                                    target="_blank" 
                                    class="text-xs text-primary font-semibold hover:underline flex items-center gap-1 cursor-pointer"
                                >
                                    Lihat Preview PDF
                                </a>
                            </div>
                        </div>

                        <!-- Upload File Input -->
                        <div class="grid gap-2">
                            <Label for="cv_file" class="font-semibold text-xs text-foreground">Pilih File PDF CV Baru</Label>
                            <div class="flex flex-col items-center justify-center border border-dashed border-sidebar-border rounded-xl p-8 bg-muted-foreground/5 relative gap-2 text-center">
                                <UploadCloud class="h-10 w-10 text-muted-foreground" />
                                <span class="text-xs font-semibold text-foreground">Klik tombol di bawah atau seret file PDF ke sini</span>
                                <span class="text-[10px] text-muted-foreground">Hanya menerima format .pdf (Maksimal 5MB)</span>
                                
                                <input
                                    id="cv_file"
                                    type="file"
                                    ref="cvInput"
                                    accept=".pdf"
                                    @change="onCvFileChange"
                                    class="hidden"
                                />

                                <Button 
                                    type="button" 
                                    size="sm" 
                                    variant="outline" 
                                    @click="cvInput?.click()"
                                    class="mt-2 cursor-pointer"
                                >
                                    Pilih File PDF
                                </Button>

                                <span v-if="cvFileName" class="text-xs font-semibold text-green-600 mt-2 block">
                                    File Terpilih: {{ cvFileName }}
                                </span>
                            </div>
                            <InputError :message="cvForm.errors.cv_file" />
                        </div>
                    </div>

                    <!-- Upload Action Bar -->
                    <div class="flex items-center justify-end border-t border-sidebar-border/70 pt-4 mt-6">
                        <Button
                            type="submit"
                            :disabled="cvForm.processing || !cvForm.cv_file"
                            class="bg-primary text-white hover:bg-primary/90 font-medium cursor-pointer h-10 px-6 shadow-sm flex items-center gap-2 disabled:opacity-50"
                        >
                            <UploadCloud class="h-4 w-4" v-if="!cvForm.processing" />
                            {{ cvForm.processing ? 'Mengunggah...' : 'Unggah CV' }}
                        </Button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Media Library Modals -->
        <MediaLibraryModal
            :open="profilePhotoOpen"
            @update:open="profilePhotoOpen = $event"
            @select="selectProfilePhoto"
        />

        <MediaLibraryModal
            :open="ogImageOpen"
            @update:open="ogImageOpen = $event"
            @select="selectOgImage"
        />
    </div>
</template>
