<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Content;
use Faker\Factory as Faker;

class NewsEventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $fakerId = Faker::create('id_ID'); // Indonesian faker

        // Updated categories matching demo site
        $newsCategories = ['business', 'company-activities', 'press-release', 'highlights'];
        $eventCategories = ['webinar', 'conference', 'workshop', 'seminar', 'training'];

        // News data templates with relevant content for each category
        $newsTemplates = [
            // Business Category
            [
                'category' => 'business',
                'title_en' => 'TEZ Capital Reports Strong Financial Performance in Q3 2024',
                'title_id' => 'TEZ Capital Laporkan Kinerja Keuangan yang Kuat di Q3 2024',
                'excerpt_en' => 'TEZ Capital announces robust financial results with 25% growth in financing portfolio and expanded market reach across Indonesia.',
                'excerpt_id' => 'TEZ Capital mengumumkan hasil keuangan yang kokoh dengan pertumbuhan 25% dalam portofolio pembiayaan dan jangkauan pasar yang diperluas di seluruh Indonesia.',
                'content_en' => 'TEZ Capital has demonstrated exceptional performance in the third quarter of 2024, reporting a remarkable 25% increase in its financing portfolio compared to the same period last year. The company\'s strategic initiatives in expanding digital financing solutions and strengthening partnerships with local banks have contributed significantly to this growth. The total financing disbursed reached IDR 2.5 trillion, serving over 15,000 small and medium enterprises across Indonesia. CEO stated that this performance reflects the company\'s commitment to supporting Indonesian businesses while maintaining prudent risk management practices.',
                'content_id' => 'TEZ Capital telah menunjukkan kinerja yang luar biasa di kuartal ketiga 2024, melaporkan peningkatan yang luar biasa sebesar 25% dalam portofolio pembiayaannya dibandingkan periode yang sama tahun lalu. Inisiatif strategis perusahaan dalam memperluas solusi pembiayaan digital dan memperkuat kemitraan dengan bank-bank lokal telah berkontribusi signifikan terhadap pertumbuhan ini. Total pembiayaan yang disalurkan mencapai Rp 2,5 triliun, melayani lebih dari 15.000 usaha kecil dan menengah di seluruh Indonesia. CEO menyatakan bahwa kinerja ini mencerminkan komitmen perusahaan untuk mendukung bisnis Indonesia sambil mempertahankan praktik manajemen risiko yang hati-hati.',
            ],
            [
                'category' => 'business',
                'title_en' => 'Market Expansion Strategy: TEZ Capital Enters Eastern Indonesia',
                'title_id' => 'Strategi Ekspansi Pasar: TEZ Capital Masuki Indonesia Timur',
                'excerpt_en' => 'Strategic expansion into Papua, Maluku, and Sulawesi regions to serve underbanked SME markets with innovative financing solutions.',
                'excerpt_id' => 'Ekspansi strategis ke wilayah Papua, Maluku, dan Sulawesi untuk melayani pasar UKM yang kurang terlayani dengan solusi pembiayaan inovatif.',
                'content_en' => 'TEZ Capital announces its ambitious expansion plan into Eastern Indonesia, targeting the underserved small and medium enterprise markets in Papua, Maluku, and Sulawesi regions. This strategic move aims to bridge the financing gap in these areas, where access to formal financial services remains limited. The company plans to establish 12 new branch offices and deploy digital financing platforms tailored to local business needs. The expansion is expected to serve an additional 8,000 businesses over the next two years, with a total financing target of IDR 1.2 trillion.',
                'content_id' => 'TEZ Capital mengumumkan rencana ekspansi ambisius ke Indonesia Timur, menargetkan pasar usaha kecil dan menengah yang kurang terlayani di wilayah Papua, Maluku, dan Sulawesi. Langkah strategis ini bertujuan untuk menjembatani kesenjangan pembiayaan di daerah-daerah ini, di mana akses ke layanan keuangan formal masih terbatas. Perusahaan berencana mendirikan 12 kantor cabang baru dan menerapkan platform pembiayaan digital yang disesuaikan dengan kebutuhan bisnis lokal. Ekspansi diharapkan dapat melayani 8.000 bisnis tambahan dalam dua tahun ke depan, dengan target pembiayaan total Rp 1,2 triliun.',
            ],
            // Company Activities Category
            [
                'category' => 'company-activities',
                'title_en' => 'TEZ Capital Launches Employee Development Program 2024',
                'title_id' => 'TEZ Capital Luncurkan Program Pengembangan Karyawan 2024',
                'excerpt_en' => 'Comprehensive training and certification program designed to enhance employee skills in digital finance and customer service excellence.',
                'excerpt_id' => 'Program pelatihan dan sertifikasi komprehensif yang dirancang untuk meningkatkan keterampilan karyawan dalam keuangan digital dan keunggulan layanan pelanggan.',
                'content_en' => 'TEZ Capital has launched its comprehensive Employee Development Program 2024, investing IDR 5 billion in staff training and professional development. The program covers advanced digital banking technologies, customer relationship management, and leadership skills. All 500+ employees will participate in the 6-month program, which includes both internal workshops and partnerships with leading universities. The initiative aims to enhance service quality and prepare the workforce for the company\'s digital transformation journey.',
                'content_id' => 'TEZ Capital telah meluncurkan Program Pengembangan Karyawan 2024 yang komprehensif, menginvestasikan Rp 5 miliar untuk pelatihan staf dan pengembangan profesional. Program ini mencakup teknologi perbankan digital canggih, manajemen hubungan pelanggan, dan keterampilan kepemimpinan. Semua 500+ karyawan akan berpartisipasi dalam program 6 bulan, yang mencakup workshop internal dan kemitraan dengan universitas terkemuka. Inisiatif ini bertujuan untuk meningkatkan kualitas layanan dan mempersiapkan tenaga kerja untuk perjalanan transformasi digital perusahaan.',
            ],
            [
                'category' => 'company-activities',
                'title_en' => 'Annual Corporate Social Responsibility Report 2024',
                'title_id' => 'Laporan Tanggung Jawab Sosial Perusahaan Tahunan 2024',
                'excerpt_en' => 'TEZ Capital\'s commitment to community development through education scholarships, SME mentoring, and environmental sustainability initiatives.',
                'excerpt_id' => 'Komitmen TEZ Capital terhadap pengembangan masyarakat melalui beasiswa pendidikan, mentoring UKM, dan inisiatif keberlanjutan lingkungan.',
                'content_en' => 'TEZ Capital released its Annual CSR Report highlighting significant community impact initiatives throughout 2024. The company provided 200 education scholarships to underprivileged students, conducted business mentoring for 1,000 SMEs, and implemented environmental sustainability programs in 50 locations. The total CSR investment reached IDR 15 billion, focusing on education, economic empowerment, and environmental conservation. The company also achieved carbon neutral certification for all its branch operations.',
                'content_id' => 'TEZ Capital merilis Laporan CSR Tahunan yang menyoroti inisiatif dampak komunitas yang signifikan sepanjang 2024. Perusahaan memberikan 200 beasiswa pendidikan kepada siswa kurang mampu, melakukan mentoring bisnis untuk 1.000 UKM, dan mengimplementasikan program keberlanjutan lingkungan di 50 lokasi. Total investasi CSR mencapai Rp 15 miliar, berfokus pada pendidikan, pemberdayaan ekonomi, dan konservasi lingkungan. Perusahaan juga meraih sertifikasi carbon neutral untuk semua operasi cabangnya.',
            ],
            // Press Release Category
            [
                'category' => 'press-release',
                'title_en' => 'TEZ Capital Receives "Best SME Financing Company" Award 2024',
                'title_id' => 'TEZ Capital Raih Penghargaan "Perusahaan Pembiayaan UKM Terbaik" 2024',
                'excerpt_en' => 'Recognition from Indonesia Finance Association for excellence in small business financing and innovative digital solutions.',
                'excerpt_id' => 'Pengakuan dari Asosiasi Keuangan Indonesia atas keunggulan dalam pembiayaan usaha kecil dan solusi digital inovatif.',
                'content_en' => 'TEZ Capital proudly announces receiving the prestigious "Best SME Financing Company" award from the Indonesia Finance Association (IFA) at the annual Financial Excellence Awards 2024. This recognition acknowledges the company\'s outstanding performance in supporting small and medium enterprises through innovative financing solutions and exceptional customer service. The award ceremony, attended by industry leaders and government officials, highlighted TEZ Capital\'s contribution to Indonesia\'s economic growth and SME development.',
                'content_id' => 'TEZ Capital dengan bangga mengumumkan menerima penghargaan bergengsi "Perusahaan Pembiayaan UKM Terbaik" dari Asosiasi Keuangan Indonesia (IFA) pada acara Financial Excellence Awards 2024 tahunan. Pengakuan ini mengakui kinerja luar biasa perusahaan dalam mendukung usaha kecil dan menengah melalui solusi pembiayaan inovatif dan layanan pelanggan yang luar biasa. Upacara penghargaan, yang dihadiri oleh para pemimpin industri dan pejabat pemerintah, menyoroti kontribusi TEZ Capital terhadap pertumbuhan ekonomi Indonesia dan pengembangan UKM.',
            ],
            [
                'category' => 'press-release',
                'title_en' => 'Strategic Partnership Announcement with Bank Mandiri',
                'title_id' => 'Pengumuman Kemitraan Strategis dengan Bank Mandiri',
                'excerpt_en' => 'Joint financing program to provide IDR 10 trillion credit facility for Indonesian SMEs over the next three years.',
                'excerpt_id' => 'Program pembiayaan bersama untuk menyediakan fasilitas kredit Rp 10 triliun untuk UKM Indonesia selama tiga tahun ke depan.',
                'content_en' => 'TEZ Capital and Bank Mandiri officially announce a strategic partnership to launch a joint SME financing program worth IDR 10 trillion over the next three years. This collaboration combines TEZ Capital\'s expertise in SME market penetration with Bank Mandiri\'s extensive banking infrastructure. The partnership will focus on digital lending platforms, supply chain financing, and working capital solutions for small businesses across Indonesia. The program is expected to serve over 50,000 SMEs nationwide.',
                'content_id' => 'TEZ Capital dan Bank Mandiri secara resmi mengumumkan kemitraan strategis untuk meluncurkan program pembiayaan UKM bersama senilai Rp 10 triliun selama tiga tahun ke depan. Kolaborasi ini menggabungkan keahlian TEZ Capital dalam penetrasi pasar UKM dengan infrastruktur perbankan Bank Mandiri yang luas. Kemitraan akan fokus pada platform pinjaman digital, pembiayaan rantai pasokan, dan solusi modal kerja untuk usaha kecil di seluruh Indonesia. Program ini diharapkan dapat melayani lebih dari 50.000 UKM di seluruh negeri.',
            ],
            // Highlights Category
            [
                'category' => 'highlights',
                'title_en' => 'TEZ Capital Achieves 1 Million SME Customers Milestone',
                'title_id' => 'TEZ Capital Capai Milestone 1 Juta Nasabah UKM',
                'excerpt_en' => 'Historic achievement reflecting seven years of dedicated service to Indonesian small and medium enterprises.',
                'excerpt_id' => 'Pencapaian bersejarah yang mencerminkan tujuh tahun pelayanan yang berdedikasi kepada usaha kecil dan menengah Indonesia.',
                'content_en' => 'TEZ Capital celebrates a historic milestone, reaching one million small and medium enterprise customers after seven years of operations. This achievement represents the company\'s unwavering commitment to financial inclusion and supporting Indonesian entrepreneurs. From its humble beginning with 100 customers in 2017, TEZ Capital has grown to become one of Indonesia\'s leading SME financing companies. The milestone was commemorated with a special ceremony attended by government officials, business partners, and loyal customers.',
                'content_id' => 'TEZ Capital merayakan milestone bersejarah, mencapai satu juta nasabah usaha kecil dan menengah setelah tujuh tahun beroperasi. Pencapaian ini mewakili komitmen teguh perusahaan terhadap inklusi keuangan dan dukungan kepada pengusaha Indonesia. Dari awal yang sederhana dengan 100 nasabah pada 2017, TEZ Capital telah tumbuh menjadi salah satu perusahaan pembiayaan UKM terkemuka di Indonesia. Milestone ini diperingati dengan upacara khusus yang dihadiri pejabat pemerintah, mitra bisnis, dan nasabah setia.',
            ],
            [
                'category' => 'highlights',
                'title_en' => 'Digital Transformation Success: 90% Paperless Operations Achieved',
                'title_id' => 'Sukses Transformasi Digital: Capai 90% Operasi Tanpa Kertas',
                'excerpt_en' => 'Revolutionary shift to digital-first operations reduces processing time by 60% and enhances customer experience significantly.',
                'excerpt_id' => 'Pergeseran revolusioner ke operasi digital-first mengurangi waktu pemrosesan hingga 60% dan meningkatkan pengalaman nasabah secara signifikan.',
                'content_en' => 'TEZ Capital announces a remarkable achievement in its digital transformation journey, reaching 90% paperless operations across all business processes. This transformation has resulted in 60% faster loan processing, reduced operational costs by 40%, and significantly improved customer satisfaction scores. The company\'s investment in cutting-edge fintech solutions and artificial intelligence has positioned it as an industry leader in digital financial services. The remaining 10% of processes are scheduled for digitization by Q2 2025.',
                'content_id' => 'TEZ Capital mengumumkan pencapaian luar biasa dalam perjalanan transformasi digitalnya, mencapai 90% operasi tanpa kertas di semua proses bisnis. Transformasi ini menghasilkan pemrosesan pinjaman 60% lebih cepat, mengurangi biaya operasional sebesar 40%, dan secara signifikan meningkatkan skor kepuasan nasabah. Investasi perusahaan dalam solusi fintech mutakhir dan kecerdasan buatan telah memposisikannya sebagai pemimpin industri dalam layanan keuangan digital. Sisa 10% proses dijadwalkan untuk digitalisasi pada Q2 2025.',
            ],
        ];

        // Event data templates
        $eventTemplates = [
            [
                'title_en' => 'SME Financial Literacy Workshop 2024',
                'title_id' => 'Workshop Literasi Keuangan UKM 2024',
                'excerpt_en' => 'Comprehensive financial education program for small business owners covering budgeting, investment, and business growth strategies.',
                'excerpt_id' => 'Program edukasi keuangan komprehensif untuk pemilik usaha kecil yang mencakup penganggaran, investasi, dan strategi pertumbuhan bisnis.',
                'location_id' => 'Jakarta Convention Center, Jakarta',
                'location_en' => 'Jakarta Convention Center, Jakarta',
                'category' => 'workshop',
                'organizer' => 'TEZ Capital & Indonesia SME Association',
                'price' => 0,
                'max_participants' => 200,
            ],
            [
                'title_en' => 'Indonesia Finance Summit 2024: Digital Future',
                'title_id' => 'Summit Keuangan Indonesia 2024: Masa Depan Digital',
                'excerpt_en' => 'Annual summit discussing the future of financial technology and its impact on Indonesian economy with industry leaders.',
                'excerpt_id' => 'Summit tahunan membahas masa depan teknologi keuangan dan dampaknya terhadap ekonomi Indonesia bersama para pemimpin industri.',
                'location_id' => 'The Ritz-Carlton Jakarta, Jakarta',
                'location_en' => 'The Ritz-Carlton Jakarta, Jakarta',
                'category' => 'conference',
                'organizer' => 'TEZ Capital & Financial Services Authority',
                'price' => 500000,
                'max_participants' => 500,
            ],
            [
                'title_en' => 'Digital Banking Webinar Series',
                'title_id' => 'Seri Webinar Perbankan Digital',
                'excerpt_en' => 'Monthly webinar series covering digital banking trends, cybersecurity, and customer experience innovations.',
                'excerpt_id' => 'Seri webinar bulanan yang membahas tren perbankan digital, keamanan siber, dan inovasi pengalaman nasabah.',
                'location_id' => 'Virtual Event Platform',
                'location_en' => 'Virtual Event Platform',
                'category' => 'webinar',
                'organizer' => 'TEZ Capital Digital Team',
                'price' => 0,
                'max_participants' => 1000,
            ],
        ];

        echo "Creating news content with updated categories...\n";
        
        // Create news items using templates and additional generated content
        foreach ($newsTemplates as $template) {
            $publishedAt = $faker->dateTimeBetween('-3 months', 'now');
            $isFeatured = $faker->boolean(30);
            
            Content::create([
                'type' => 'news',
                'category' => $template['category'],
                'title_id' => $template['title_id'],
                'title_en' => $template['title_en'],
                'excerpt_id' => $template['excerpt_id'],
                'excerpt_en' => $template['excerpt_en'],
                'content_id' => $template['content_id'],
                'content_en' => $template['content_en'],
                'featured_image' => 'news/news-' . uniqid() . '.jpg',
                'gallery' => json_encode([
                    'news/gallery-' . uniqid() . '-1.jpg',
                    'news/gallery-' . uniqid() . '-2.jpg',
                ]),
                'tags' => json_encode([
                    $faker->randomElement(['finance', 'banking', 'SME', 'business', 'digital']),
                    $faker->randomElement(['Indonesia', 'growth', 'innovation', 'partnership'])
                ]),
                'author' => $faker->randomElement(['Admin TEZ Capital', 'Tim Editorial', 'Public Relations']),
                'source_url' => null,
                'is_published' => true,
                'published_at' => $publishedAt,
                'is_featured' => $isFeatured,
                'status' => 'published',
                'meta_title_id' => $template['title_id'],
                'meta_title_en' => $template['title_en'],
                'meta_description_id' => $template['excerpt_id'],
                'meta_description_en' => $template['excerpt_en'],
                'sort_order' => 0,
                'view_count' => $faker->numberBetween(100, 5000),
                'like_count' => $faker->numberBetween(10, 500),
                'share_count' => $faker->numberBetween(5, 100),
                'created_at' => $publishedAt,
                'updated_at' => $faker->dateTimeBetween($publishedAt, 'now'),
            ]);
            
            echo "Created news: {$template['title_en']}\n";
        }

        // Generate additional news items to reach minimum 10
        $remainingNews = 2; // We have 8 templates, need 2 more
        for ($i = 0; $i < $remainingNews; $i++) {
            $category = $faker->randomElement($newsCategories);
            $publishedAt = $faker->dateTimeBetween('-2 months', 'now');
            
            Content::create([
                'type' => 'news',
                'category' => $category,
                'title_id' => $fakerId->sentence(8),
                'title_en' => $faker->sentence(8),
                'excerpt_id' => $fakerId->paragraph(2),
                'excerpt_en' => $faker->paragraph(2),
                'content_id' => $fakerId->paragraphs(6, true),
                'content_en' => $faker->paragraphs(6, true),
                'featured_image' => 'news/news-' . uniqid() . '.jpg',
                'gallery' => json_encode([
                    'news/gallery-' . uniqid() . '-1.jpg',
                    'news/gallery-' . uniqid() . '-2.jpg',
                ]),
                'tags' => json_encode([
                    $faker->randomElement(['finance', 'business', 'SME', 'technology']),
                    $faker->randomElement(['Indonesia', 'development', 'service'])
                ]),
                'author' => $faker->randomElement(['Admin TEZ Capital', 'Tim Editorial']),
                'is_published' => true,
                'published_at' => $publishedAt,
                'is_featured' => $faker->boolean(20),
                'status' => 'published',
                'meta_title_id' => $fakerId->sentence(6),
                'meta_title_en' => $faker->sentence(6),
                'meta_description_id' => $fakerId->sentence(12),
                'meta_description_en' => $faker->sentence(12),
                'view_count' => $faker->numberBetween(50, 2000),
                'like_count' => $faker->numberBetween(5, 200),
                'share_count' => $faker->numberBetween(1, 50),
                'created_at' => $publishedAt,
                'updated_at' => $faker->dateTimeBetween($publishedAt, 'now'),
            ]);
        }

        echo "Creating events content...\n";
        
        // Create events using templates
        foreach ($eventTemplates as $template) {
            $startDate = $faker->dateTimeBetween('now', '+6 months');
            $endDate = (clone $startDate)->modify('+' . $faker->numberBetween(1, 3) . ' days');
            $registeredCount = $faker->numberBetween(0, $template['max_participants'] - 20);
            
            Content::create([
                'type' => 'event',
                'category' => $template['category'],
                'title_id' => $template['title_id'],
                'title_en' => $template['title_en'],
                'excerpt_id' => $template['excerpt_id'],
                'excerpt_en' => $template['excerpt_en'],
                'content_id' => $fakerId->paragraphs(5, true),
                'content_en' => $faker->paragraphs(5, true),
                'featured_image' => 'events/event-' . uniqid() . '.jpg',
                'gallery' => json_encode([
                    'events/gallery-' . uniqid() . '-1.jpg',
                    'events/gallery-' . uniqid() . '-2.jpg',
                ]),
                'tags' => json_encode([
                    $faker->randomElement(['seminar', 'workshop', 'training', 'conference']),
                    $faker->randomElement(['finance', 'business', 'education', 'digital'])
                ]),
                'author' => 'Event Management Team',
                'location_id' => $template['location_id'],
                'location_en' => $template['location_en'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'organizer' => $template['organizer'],
                'price' => $template['price'],
                'max_participants' => $template['max_participants'],
                'registered_count' => $registeredCount,
                'is_published' => true,
                'published_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'is_featured' => $faker->boolean(50),
                'status' => 'published',
                'meta_title_id' => $template['title_id'],
                'meta_title_en' => $template['title_en'],
                'meta_description_id' => $template['excerpt_id'],
                'meta_description_en' => $template['excerpt_en'],
                'view_count' => $faker->numberBetween(50, 1500),
                'like_count' => $faker->numberBetween(5, 150),
                'share_count' => $faker->numberBetween(2, 50),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'updated_at' => now(),
            ]);
            
            echo "Created event: {$template['title_en']}\n";
        }

        echo "Successfully created 10 news items and 3 events with updated categories!\n";
    }
}