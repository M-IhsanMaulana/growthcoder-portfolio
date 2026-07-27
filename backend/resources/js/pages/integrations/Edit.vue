<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import {
    Check,
    BotMessageSquare,
    Eye,
    EyeOff,
    FlaskConical,
    Loader2,
    RotateCcw,
    ChevronDown,
    ChevronUp,
    MessageSquareText,
    Newspaper,
    AlertCircle,
    CheckCircle2,
} from '@lucide/vue';

interface TelegramSettings {
    enabled: boolean;
    bot_token: string;
    bot_token_set: boolean;
    chat_id: string;
    template_contact: string;
    template_blog_publish: string;
}

const props = defineProps<{
    telegram: TelegramSettings;
}>();

// ── Forms ──────────────────────────────────────────────────────
const telegramForm = useForm({
    enabled: props.telegram.enabled,
    bot_token: '',
    chat_id: props.telegram.chat_id ?? '',
    template_contact: props.telegram.template_contact ?? '',
    template_blog_publish: props.telegram.template_blog_publish ?? '',
});

// ── Visibility toggles ──────────────────────────────────────────
const showBotToken = ref(false);
const templateContactOpen = ref(true);
const templateBlogOpen = ref(false);

// ── Test connection ─────────────────────────────────────────────
const testStatus = ref<'idle' | 'loading' | 'success' | 'error'>('idle');
const testMessage = ref('');

const testTelegram = async () => {
    testStatus.value = 'loading';
    testMessage.value = '';

    try {
        const response = await fetch('/admin-cms/integrations/telegram/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
            },
        });

        const data = await response.json();

        if (data.success) {
            testStatus.value = 'success';
            testMessage.value = data.message;
        } else {
            testStatus.value = 'error';
            testMessage.value = data.message;
        }
    } catch {
        testStatus.value = 'error';
        testMessage.value = 'Terjadi kesalahan jaringan. Coba lagi.';
    }

    setTimeout(() => {
        if (testStatus.value !== 'loading') {
            testStatus.value = 'idle';
            testMessage.value = '';
        }
    }, 6000);
};

// ── Default templates ────────────────────────────────────────────
const DEFAULT_TEMPLATE_CONTACT = "🌐 *Notifikasi dari Growthcoder.id*\n\n📬 *Pesan Kontak Baru!*\n\n👤 *Nama:* {name}\n📧 *Email:* {email}\n📌 *Subjek:* {subject}\n\n💬 *Pesan:*\n{message}\n\n⏰ *Diterima:* {received_at}";
const DEFAULT_TEMPLATE_BLOG = "🌐 *Notifikasi dari Growthcoder.id*\n\n📝 *Artikel Baru Dipublish!*\n\n📌 *{title}*\n\n{excerpt}\n\n🏷️ *Kategori:* {categories}\n📅 *Dipublish:* {published_at}\n\n🔗 Baca selengkapnya: {url}";

const resetContactTemplate = () => {
    telegramForm.template_contact = DEFAULT_TEMPLATE_CONTACT;
};

const resetBlogTemplate = () => {
    telegramForm.template_blog_publish = DEFAULT_TEMPLATE_BLOG;
};

// ── Submit ───────────────────────────────────────────────────────
const submit = () => {
    telegramForm.put('/admin-cms/integrations/telegram');
};

// ── Status indicator ─────────────────────────────────────────────
const statusLabel = computed(() => {
    if (telegramForm.enabled && props.telegram.bot_token_set && props.telegram.chat_id) {
        return { text: 'Aktif', color: 'text-green-500', dot: 'bg-green-500' };
    }
    if (telegramForm.enabled) {
        return { text: 'Belum lengkap', color: 'text-yellow-500', dot: 'bg-yellow-500' };
    }
    return { text: 'Nonaktif', color: 'text-muted-foreground', dot: 'bg-muted-foreground/40' };
});
</script>

<template>
    <Head title="Integration Settings" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-y-auto w-full">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border/70 pb-4">
            <div>
                <h1 class="text-xl font-bold tracking-tight text-foreground">Integration Settings</h1>
                <p class="text-xs text-muted-foreground">
                    Kelola konfigurasi integrasi pihak ketiga seperti Telegram Bot untuk notifikasi.
                </p>
            </div>
        </div>

        <!-- Telegram Integration Card -->
        <div class="rounded-2xl border border-border/60 bg-card shadow-xs overflow-hidden">

            <!-- Card Header -->
            <div class="flex items-center justify-between gap-4 p-5 border-b border-border/60 bg-gradient-to-r from-[#2AABEE]/5 via-transparent to-transparent">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#2AABEE]/10 border border-[#2AABEE]/20">
                        <BotMessageSquare class="h-5 w-5 text-[#2AABEE]" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-foreground">Telegram Bot</h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span :class="['h-1.5 w-1.5 rounded-full', statusLabel.dot]" />
                            <span :class="['text-xs font-medium', statusLabel.color]">{{ statusLabel.text }}</span>
                        </div>
                    </div>
                </div>

                <!-- Enable toggle -->
                <label class="relative inline-flex cursor-pointer items-center gap-2 select-none">
                    <span class="text-xs text-muted-foreground font-medium">{{ telegramForm.enabled ? 'Aktif' : 'Nonaktif' }}</span>
                    <div
                        class="relative h-6 w-11 rounded-full transition-colors duration-300 cursor-pointer"
                        :class="telegramForm.enabled ? 'bg-[#2AABEE]' : 'bg-muted-foreground/20'"
                        @click="telegramForm.enabled = !telegramForm.enabled"
                    >
                        <span
                            class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-300"
                            :class="telegramForm.enabled ? 'translate-x-5' : 'translate-x-0'"
                        />
                    </div>
                </label>
            </div>

            <!-- Card Body -->
            <div class="p-5 space-y-6">

                <!-- Credentials Section -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Kredensial Bot</h3>

                    <!-- Bot Token -->
                    <div class="space-y-1.5">
                        <Label for="bot_token" class="text-xs font-semibold">
                            Bot Token
                            <span class="ml-1 text-muted-foreground font-normal">(dari @BotFather)</span>
                        </Label>
                        <div class="relative">
                            <Input
                                id="bot_token"
                                v-model="telegramForm.bot_token"
                                :type="showBotToken ? 'text' : 'password'"
                                :placeholder="telegram.bot_token_set ? 'Token tersimpan — isi untuk ganti' : 'Contoh: 1234567890:AAF...'"
                                autocomplete="off"
                                class="h-9 text-xs pr-10 font-mono"
                            />
                            <button
                                type="button"
                                @click="showBotToken = !showBotToken"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                            >
                                <Eye v-if="!showBotToken" class="h-3.5 w-3.5" />
                                <EyeOff v-else class="h-3.5 w-3.5" />
                            </button>
                        </div>
                        <p v-if="telegram.bot_token_set && !telegramForm.bot_token" class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1">
                            <CheckCircle2 class="h-3 w-3" />
                            Bot Token sudah tersimpan (terenkripsi)
                        </p>
                        <InputError :message="telegramForm.errors.bot_token" />
                    </div>

                    <!-- Chat ID -->
                    <div class="space-y-1.5">
                        <Label for="chat_id" class="text-xs font-semibold">
                            Chat ID / User ID
                            <span class="ml-1 text-muted-foreground font-normal">(gunakan @userinfobot untuk cek)</span>
                        </Label>
                        <Input
                            id="chat_id"
                            v-model="telegramForm.chat_id"
                            type="text"
                            placeholder="Contoh: 123456789 atau -100123456789"
                            class="h-9 text-xs font-mono"
                        />
                        <InputError :message="telegramForm.errors.chat_id" />
                    </div>

                    <!-- Test Connection -->
                    <div class="flex flex-col gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="testTelegram"
                            :disabled="testStatus === 'loading'"
                            class="w-fit h-8 text-xs gap-2 cursor-pointer"
                        >
                            <Loader2 v-if="testStatus === 'loading'" class="h-3.5 w-3.5 animate-spin" />
                            <FlaskConical v-else class="h-3.5 w-3.5" />
                            {{ testStatus === 'loading' ? 'Mengirim...' : 'Test Kirim Pesan' }}
                        </Button>

                        <!-- Test result feedback -->
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-1"
                        >
                            <div
                                v-if="testStatus === 'success' || testStatus === 'error'"
                                :class="[
                                    'flex items-start gap-2 rounded-lg p-3 text-xs',
                                    testStatus === 'success'
                                        ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800'
                                        : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800'
                                ]"
                            >
                                <CheckCircle2 v-if="testStatus === 'success'" class="h-3.5 w-3.5 mt-0.5 shrink-0" />
                                <AlertCircle v-else class="h-3.5 w-3.5 mt-0.5 shrink-0" />
                                <span>{{ testMessage }}</span>
                            </div>
                        </Transition>
                    </div>
                </div>

                <hr class="border-border/60" />

                <!-- Templates Section -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Template Pesan</h3>

                    <!-- Contact Message Template -->
                    <div class="rounded-xl border border-border/60 overflow-hidden">
                        <button
                            type="button"
                            @click="templateContactOpen = !templateContactOpen"
                            class="flex w-full items-center justify-between gap-3 p-3.5 hover:bg-muted/30 transition-colors text-left"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-violet-500/10">
                                    <MessageSquareText class="h-3.5 w-3.5 text-violet-500" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-foreground">Pesan Kontak</p>
                                    <p class="text-[10px] text-muted-foreground">Saat pengunjung mengirim pesan kontak</p>
                                </div>
                            </div>
                            <ChevronDown v-if="!templateContactOpen" class="h-4 w-4 text-muted-foreground shrink-0" />
                            <ChevronUp v-else class="h-4 w-4 text-muted-foreground shrink-0" />
                        </button>

                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-[600px]"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="opacity-100 max-h-[600px]"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div v-show="templateContactOpen" class="border-t border-border/60 p-4 space-y-3">
                                <!-- Variable chips -->
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="text-[10px] text-muted-foreground mr-1 self-center">Variabel:</span>
                                    <code v-for="v in ['{name}', '{email}', '{subject}', '{message}', '{received_at}']" :key="v"
                                        class="inline-flex items-center rounded-md bg-muted px-1.5 py-0.5 text-[10px] font-mono text-foreground/80 border border-border/50 cursor-pointer hover:bg-primary/10 hover:border-primary/30 transition-colors select-none"
                                        @click="telegramForm.template_contact += v"
                                    >{{ v }}</code>
                                </div>
                                <textarea
                                    id="template_contact"
                                    v-model="telegramForm.template_contact"
                                    rows="8"
                                    class="w-full rounded-lg border border-border/60 bg-background px-3 py-2.5 text-xs font-mono resize-y focus:outline-none focus:ring-2 focus:ring-primary/30 placeholder:text-muted-foreground"
                                    placeholder="Template pesan kontak..."
                                />
                                <div class="flex items-center justify-between">
                                    <InputError :message="telegramForm.errors.template_contact" />
                                    <button
                                        type="button"
                                        @click="resetContactTemplate"
                                        class="flex items-center gap-1 text-[10px] text-muted-foreground hover:text-foreground transition-colors ml-auto"
                                    >
                                        <RotateCcw class="h-3 w-3" />
                                        Reset ke default
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Blog Publish Template -->
                    <div class="rounded-xl border border-border/60 overflow-hidden">
                        <button
                            type="button"
                            @click="templateBlogOpen = !templateBlogOpen"
                            class="flex w-full items-center justify-between gap-3 p-3.5 hover:bg-muted/30 transition-colors text-left"
                        >
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/10">
                                    <Newspaper class="h-3.5 w-3.5 text-emerald-500" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-foreground">Blog Post Dipublish</p>
                                    <p class="text-[10px] text-muted-foreground">Saat artikel blog dipublish (langsung atau terjadwal)</p>
                                </div>
                            </div>
                            <ChevronDown v-if="!templateBlogOpen" class="h-4 w-4 text-muted-foreground shrink-0" />
                            <ChevronUp v-else class="h-4 w-4 text-muted-foreground shrink-0" />
                        </button>

                        <Transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-[600px]"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="opacity-100 max-h-[600px]"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div v-show="templateBlogOpen" class="border-t border-border/60 p-4 space-y-3">
                                <!-- Variable chips -->
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="text-[10px] text-muted-foreground mr-1 self-center">Variabel:</span>
                                    <code v-for="v in ['{title}', '{url}', '{excerpt}', '{categories}', '{published_at}']" :key="v"
                                        class="inline-flex items-center rounded-md bg-muted px-1.5 py-0.5 text-[10px] font-mono text-foreground/80 border border-border/50 cursor-pointer hover:bg-primary/10 hover:border-primary/30 transition-colors select-none"
                                        @click="telegramForm.template_blog_publish += v"
                                    >{{ v }}</code>
                                </div>
                                <textarea
                                    id="template_blog_publish"
                                    v-model="telegramForm.template_blog_publish"
                                    rows="8"
                                    class="w-full rounded-lg border border-border/60 bg-background px-3 py-2.5 text-xs font-mono resize-y focus:outline-none focus:ring-2 focus:ring-primary/30 placeholder:text-muted-foreground"
                                    placeholder="Template pesan blog publish..."
                                />
                                <div class="flex items-center justify-between">
                                    <InputError :message="telegramForm.errors.template_blog_publish" />
                                    <button
                                        type="button"
                                        @click="resetBlogTemplate"
                                        class="flex items-center gap-1 text-[10px] text-muted-foreground hover:text-foreground transition-colors ml-auto"
                                    >
                                        <RotateCcw class="h-3 w-3" />
                                        Reset ke default
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>

                <!-- Info box -->
                <div class="rounded-xl bg-muted/50 border border-border/50 p-4 space-y-2">
                    <p class="text-xs font-semibold text-foreground/70">Cara mendapatkan kredensial:</p>
                    <ol class="text-xs text-muted-foreground space-y-1 list-decimal list-inside">
                        <li>Buka Telegram, cari <code class="bg-muted rounded px-1 py-0.5 text-[10px]">@BotFather</code> dan buat bot baru dengan perintah <code class="bg-muted rounded px-1 py-0.5 text-[10px]">/newbot</code>.</li>
                        <li>Salin <strong>Bot Token</strong> yang diberikan dan tempel di field di atas.</li>
                        <li>Cari <code class="bg-muted rounded px-1 py-0.5 text-[10px]">@userinfobot</code> untuk mengetahui <strong>Chat ID</strong> akun Anda, atau gunakan ID grup/channel.</li>
                        <li>Pastikan bot sudah ditambahkan ke grup/channel target sebelum test.</li>
                    </ol>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-border/60 bg-muted/20">
                <Button
                    type="button"
                    @click="submit"
                    :disabled="telegramForm.processing"
                    class="h-9 text-xs px-6 font-semibold gap-2 bg-primary hover:bg-primary/90 text-white shadow-xs cursor-pointer"
                >
                    <Loader2 v-if="telegramForm.processing" class="h-3.5 w-3.5 animate-spin" />
                    <Check v-else class="h-3.5 w-3.5" />
                    {{ telegramForm.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </Button>
            </div>
        </div>
    </div>
</template>
