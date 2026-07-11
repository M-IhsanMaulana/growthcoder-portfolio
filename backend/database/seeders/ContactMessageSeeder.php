<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactMessage::query()->delete();

        $messages = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'subject' => 'Project Inquiry: E-Commerce Landing Page',
                'message' => 'Halo Ihsan, saya tertarik untuk mendiskusikan pembuatan landing page e-commerce baru untuk bisnis retail kami. Berapa perkiraan timeline dan biaya pengerjaan untuk desain responsif beserta integrasi analitik?',
                'status' => 'unread',
                'sender_ip' => '192.168.1.50',
                'telegram_notified_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@techstart.io',
                'subject' => 'API Integration Collaboration',
                'message' => 'Hi Muhammad, I saw your portfolio and your expertise in Telegram Bot and API integrations. We are looking for a freelance developer to help us integrate Midtrans payment gateway and automate invoice notifications via Telegram. Let me know if you are available for a call this week.',
                'status' => 'read',
                'sender_ip' => '104.244.72.15',
                'telegram_notified_at' => now()->subDay(),
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@devcommunity.or.id',
                'subject' => 'Pertanyaan Mengenai Artikel Docker & Laravel',
                'message' => 'Mas Ihsan, terima kasih atas artikel tutorial Docker-nya, sangat membantu! Saya ada kendala saat melakukan hot-reload Vue di dalam container Docker. Apakah ada konfigurasi Vite khusus yang perlu ditambahkan agar reload-nya berjalan di Windows? Terima kasih.',
                'status' => 'replied',
                'sender_ip' => '182.253.140.22',
                'telegram_notified_at' => now()->subDays(2),
            ],
            [
                'name' => 'Promo Agency',
                'email' => 'marketing@spamagency.xyz',
                'subject' => 'Boost Your Portfolio Traffic!',
                'message' => 'Hey! Do you want more visitors to growthcoder.id? We offer cheap SEO solutions starting from $49/month. Contact us at spamagency.xyz to get started immediately!',
                'status' => 'unread',
                'sender_ip' => '45.132.22.108',
                'telegram_notified_at' => null,
            ],
            [
                'name' => 'Rian Kurnia',
                'email' => 'rian.k@gmail.com',
                'subject' => 'Great Portfolio!',
                'message' => 'Situs portofolionya keren sekali mas! Desainnya rapi dan performanya sangat cepat saat dibuka. Sukses terus untuk karirnya!',
                'status' => 'read',
                'sender_ip' => '110.138.89.54',
                'telegram_notified_at' => now()->subDays(3),
            ],
        ];

        foreach ($messages as $msg) {
            ContactMessage::create($msg);
        }
    }
}
