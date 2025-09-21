<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            // News Articles
            [
                'type' => 'news',
                'category' => 'business',
                'slug' => 'tez-capital-meraih-penghargaan-perusahaan-finansial-terbaik-2024',
                'title_id' => 'Tez Capital Meraih Penghargaan Perusahaan Finansial Terbaik 2024',
                'title_en' => 'Tez Capital Wins Best Financial Company Award 2024',
                'excerpt_id' => 'Tez Capital kembali meraih prestasi dengan menerima penghargaan sebagai Perusahaan Finansial Terbaik tahun 2024 dari Asosiasi Finansial Indonesia.',
                'excerpt_en' => 'Tez Capital achieves recognition by receiving the Best Financial Company award for 2024 from the Indonesian Financial Association.',
                'content_id' => '<p>Jakarta, 15 September 2024 - PT Tez Capital Indonesia dengan bangga mengumumkan pencapaian prestasi gemilang dengan meraih penghargaan "Perusahaan Finansial Terbaik 2024" dari Asosiasi Finansial Indonesia (AFI).</p><p>Penghargaan ini diberikan berdasarkan penilaian komprehensif terhadap kinerja perusahaan dalam memberikan layanan finansial yang inovatif, transparansi dalam operasional, serta kontribusi positif terhadap pertumbuhan ekonomi nasional.</p>',
                'content_en' => '<p>Jakarta, September 15, 2024 - PT Tez Capital Indonesia proudly announces its outstanding achievement by winning the "Best Financial Company 2024" award from the Indonesian Financial Association (AFI).</p><p>This award is based on comprehensive evaluation of company performance in providing innovative financial services, operational transparency, and positive contribution to national economic growth.</p>',
                'author' => 'Tim Editorial Tez Capital',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'status' => 'published',
                'view_count' => 1250,
                'like_count' => 89,
                'share_count' => 42,
            ],
            [
                'type' => 'news',
                'category' => 'company-activities',
                'slug' => 'workshop-literasi-finansial-untuk-umkm-sukses-digelar',
                'title_id' => 'Workshop Literasi Finansial untuk UMKM Sukses Digelar',
                'title_en' => 'Financial Literacy Workshop for SMEs Successfully Held',
                'excerpt_id' => 'Tez Capital menyelenggarakan workshop literasi finansial yang dihadiri lebih dari 200 pelaku UMKM di Jakarta.',
                'excerpt_en' => 'Tez Capital organized a financial literacy workshop attended by more than 200 SME practitioners in Jakarta.',
                'content_id' => '<p>Sebagai bentuk komitmen terhadap pemberdayaan UMKM, Tez Capital menyelenggarakan workshop "Literasi Finansial untuk Kemajuan UMKM" yang berlangsung di Hotel Grand Indonesia, Jakarta pada tanggal 10 September 2024.</p>',
                'content_en' => '<p>As a form of commitment to SME empowerment, Tez Capital organized the "Financial Literacy for SME Advancement" workshop held at Grand Indonesia Hotel, Jakarta on September 10, 2024.</p>',
                'author' => 'Departemen CSR',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'is_featured' => false,
                'status' => 'published',
                'view_count' => 650,
                'like_count' => 45,
                'share_count' => 18,
            ],

            // Events
            [
                'type' => 'event',
                'category' => 'seminar',
                'slug' => 'seminar-investasi-cerdas-strategi-menghadapi-era-digital',
                'title_id' => 'Seminar Investasi Cerdas: Strategi Menghadapi Era Digital',
                'title_en' => 'Smart Investment Seminar: Strategies for the Digital Era',
                'excerpt_id' => 'Ikuti seminar eksklusif tentang strategi investasi di era digital bersama para ahli finansial terkemuka.',
                'excerpt_en' => 'Join our exclusive seminar on digital era investment strategies with leading financial experts.',
                'content_id' => '<p>Tez Capital mengundang Anda untuk mengikuti seminar eksklusif "Investasi Cerdas: Strategi Menghadapi Era Digital" yang akan membahas berbagai peluang investasi terkini.</p><p>Seminar ini akan menghadirkan pembicara ahli dari berbagai bidang finansial dan teknologi.</p>',
                'content_en' => '<p>Tez Capital invites you to join our exclusive seminar "Smart Investment: Strategies for the Digital Era" which will discuss various current investment opportunities.</p><p>This seminar will feature expert speakers from various financial and technology fields.</p>',
                'location_id' => 'Ballroom Hotel Mandarin Oriental, Jakarta',
                'location_en' => 'Ballroom Mandarin Oriental Hotel, Jakarta',
                'start_date' => now()->addDays(15)->setTime(9, 0, 0),
                'end_date' => now()->addDays(15)->setTime(17, 0, 0),
                'organizer' => 'Tez Capital',
                'price' => 250000,
                'max_participants' => 150,
                'registered_count' => 87,
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'is_featured' => true,
                'status' => 'published',
                'view_count' => 2100,
                'like_count' => 156,
                'share_count' => 73,
            ],
            [
                'type' => 'event',
                'category' => 'workshop',
                'slug' => 'workshop-perencanaan-keuangan-keluarga',
                'title_id' => 'Workshop Perencanaan Keuangan Keluarga',
                'title_en' => 'Family Financial Planning Workshop',
                'excerpt_id' => 'Pelajari cara mengelola keuangan keluarga dengan bijak dan merencanakan masa depan finansial yang lebih baik.',
                'excerpt_en' => 'Learn how to manage family finances wisely and plan a better financial future.',
                'content_id' => '<p>Workshop ini dirancang khusus untuk keluarga yang ingin memulai perencanaan keuangan yang tepat. Materi mencakup budgeting, saving, dan investment planning.</p>',
                'content_en' => '<p>This workshop is specially designed for families who want to start proper financial planning. Materials include budgeting, saving, and investment planning.</p>',
                'location_id' => 'Ruang Seminar Tez Capital, Jakarta',
                'location_en' => 'Tez Capital Seminar Room, Jakarta',
                'start_date' => now()->addDays(25)->setTime(10, 0, 0),
                'end_date' => now()->addDays(25)->setTime(15, 0, 0),
                'organizer' => 'Tez Capital Education Division',
                'price' => 150000,
                'max_participants' => 50,
                'registered_count' => 23,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'is_featured' => false,
                'status' => 'published',
                'view_count' => 890,
                'like_count' => 67,
                'share_count' => 29,
            ],

            // Services
            [
                'type' => 'service',
                'category' => 'financing',
                'slug' => 'pembiayaan-kendaraan-bermotor',
                'title_id' => 'Pembiayaan Kendaraan Bermotor',
                'title_en' => 'Motor Vehicle Financing',
                'excerpt_id' => 'Solusi pembiayaan kendaraan bermotor dengan bunga kompetitif dan proses yang mudah.',
                'excerpt_en' => 'Motor vehicle financing solutions with competitive interest rates and easy processes.',
                'content_id' => '<p>Tez Capital menyediakan layanan pembiayaan kendaraan bermotor dengan berbagai keunggulan:</p><ul><li>Bunga kompetitif mulai dari 8% per tahun</li><li>Down Payment mulai dari 10%</li><li>Tenor hingga 35 bulan</li><li>Proses persetujuan cepat</li></ul>',
                'content_en' => '<p>Tez Capital provides motor vehicle financing services with various advantages:</p><ul><li>Competitive interest starting from 8% per year</li><li>Down Payment starting from 10%</li><li>Tenor up to 35 months</li><li>Fast approval process</li></ul>',
                'is_published' => true,
                'published_at' => now()->subMonths(2),
                'is_featured' => true,
                'status' => 'published',
                'view_count' => 5420,
                'like_count' => 234,
                'share_count' => 89,
            ],

            // Partners
            [
                'type' => 'partner',
                'category' => 'bank',
                'slug' => 'bank-mandiri',
                'title_id' => 'Bank Mandiri',
                'title_en' => 'Bank Mandiri',
                'excerpt_id' => 'Partner strategis dalam layanan perbankan dan finansial.',
                'excerpt_en' => 'Strategic partner in banking and financial services.',
                'content_id' => '<p>Bank Mandiri merupakan salah satu partner strategis Tez Capital dalam menyediakan layanan finansial terpadu kepada nasabah.</p>',
                'content_en' => '<p>Bank Mandiri is one of Tez Capital\'s strategic partners in providing integrated financial services to customers.</p>',
                'is_published' => true,
                'published_at' => now()->subMonths(1),
                'is_featured' => true,
                'status' => 'published',
                'view_count' => 1200,
                'like_count' => 45,
                'share_count' => 12,
            ],
        ];

        foreach ($contents as $content) {
            Content::create($content);
        }

        $this->command->info('Content seeded successfully! Total: ' . count($contents) . ' contents');
    }
}