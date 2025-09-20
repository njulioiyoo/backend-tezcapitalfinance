<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Content;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "Creating services content from TEZ Capital demo...\n";
        
        // Service data from https://tez-compro-demo.vercel.app/service
        $services = [
            [
                'category' => 'Financing',
                'title_id' => 'Pembiayaan Investasi',
                'title_en' => 'Investment Financing',
                'excerpt_id' => 'Pembiayaan investasi adalah pembiayaan untuk pengadaan barang modal dan investasi yang diperlukan untuk mendukung kegiatan usaha.',
                'excerpt_en' => 'Investment financing is funding for the procurement of capital goods and investments needed to support business activities.',
                'content_id' => 'Pembiayaan investasi merupakan solusi keuangan yang dirancang khusus untuk membantu Anda dalam pengadaan aset produktif dan pengembangan bisnis jangka panjang.

**Keunggulan Pembiayaan Investasi:**
• Tenor pembiayaan hingga 10 tahun
• Suku bunga kompetitif dan fleksibel  
• Proses persetujuan yang cepat dan mudah
• Agunan berupa aset yang dibiayai
• Bebas biaya provisi dan administrasi
• Pendampingan konsultasi bisnis

**Jenis Investasi yang Dapat Dibiayai:**
• Pembelian mesin dan peralatan produksi
• Pembangunan atau renovasi pabrik/gudang
• Kendaraan operasional dan alat berat
• Teknologi dan sistem informasi
• Ekspansi usaha dan cabang baru
• Modernisasi fasilitas produksi

**Persyaratan Umum:**
• Usaha telah berjalan minimal 2 tahun
• Laporan keuangan lengkap
• Dokumen legalitas usaha
• NPWP dan dokumen perpajakan
• Rencana investasi yang jelas
• Kemampuan bayar yang memadai

Dengan pembiayaan investasi dari TEZ Capital, Anda dapat mewujudkan rencana pengembangan bisnis tanpa mengganggu cash flow operasional perusahaan.',
                'content_en' => 'Investment financing is a financial solution specifically designed to help you procure productive assets and develop long-term business growth.

**Investment Financing Advantages:**
• Financing tenor up to 10 years
• Competitive and flexible interest rates
• Fast and easy approval process
• Collateral using financed assets
• Free from provision and administration fees
• Business consultation support

**Types of Investment that Can Be Financed:**
• Purchase of machinery and production equipment
• Construction or renovation of factories/warehouses
• Operational vehicles and heavy equipment
• Technology and information systems
• Business expansion and new branches
• Production facility modernization

**General Requirements:**
• Business has been operating for at least 2 years
• Complete financial reports
• Business legality documents
• Tax ID and tax documents
• Clear investment plan
• Adequate payment capacity

With investment financing from TEZ Capital, you can realize your business development plans without disrupting your company\'s operational cash flow.',
                'featured_image' => 'services/investment-financing.jpg',
                'sort_order' => 1,
                'is_featured' => true,
                'tags' => ['investment', 'financing', 'business', 'capital', 'equipment'],
                'meta_title_id' => 'Pembiayaan Investasi - TEZ Capital & Finance',
                'meta_title_en' => 'Investment Financing - TEZ Capital & Finance',
                'meta_description_id' => 'Dapatkan pembiayaan investasi dengan tenor hingga 10 tahun, suku bunga kompetitif untuk pengadaan aset produktif dan pengembangan bisnis.',
                'meta_description_en' => 'Get investment financing with tenor up to 10 years, competitive interest rates for productive asset procurement and business development.',
            ],
            [
                'category' => 'Financing',
                'title_id' => 'Pembiayaan Modal Kerja',
                'title_en' => 'Working Capital Financing',
                'excerpt_id' => 'Pembiayaan modal kerja adalah pembiayaan untuk memenuhi kebutuhan dana operasional perusahaan dalam menjalankan aktivitas usaha sehari-hari.',
                'excerpt_en' => 'Working capital financing is funding to meet the operational fund needs of companies in running daily business activities.',
                'content_id' => 'Pembiayaan modal kerja adalah solusi pendanaan yang tepat untuk memenuhi kebutuhan operasional harian perusahaan Anda.

**Keunggulan Pembiayaan Modal Kerja:**
• Limit kredit hingga Rp 50 miliar
• Fasilitas revolving credit line
• Suku bunga yang kompetitif
• Tenor fleksibel sesuai kebutuhan
• Proses pencairan yang cepat
• Monitoring dan evaluasi berkala

**Kebutuhan yang Dapat Dipenuhi:**
• Pembelian bahan baku dan inventory
• Pembayaran gaji karyawan
• Biaya operasional dan overhead
• Biaya pemasaran dan promosi
• Piutang usaha dan cash flow
• Seasonal financing

**Jenis Fasilitas yang Tersedia:**
• Term Loan - pinjaman berjangka
• Revolving Credit - kredit bergulir
• Trade Finance - pembiayaan perdagangan
• Invoice Financing - pembiayaan piutang
• Inventory Financing - pembiayaan stok
• Bridge Financing - pinjaman jembatan

**Persyaratan:**
• Usaha telah berjalan minimal 1 tahun
• Cash flow positif
• Laporan keuangan terkini
• Riwayat perbankan yang baik
• Jaminan sesuai ketentuan
• Business plan yang jelas

TEZ Capital memahami dinamika bisnis yang membutuhkan fleksibilitas dalam pengelolaan modal kerja.',
                'content_en' => 'Working capital financing is the right funding solution to meet your company\'s daily operational needs.

**Working Capital Financing Advantages:**
• Credit limit up to IDR 50 billion
• Revolving credit line facility
• Competitive interest rates
• Flexible tenor according to needs
• Fast disbursement process
• Regular monitoring and evaluation

**Needs that Can Be Met:**
• Purchase of raw materials and inventory
• Employee salary payments
• Operational costs and overhead
• Marketing and promotion costs
• Trade receivables and cash flow
• Seasonal financing

**Available Facility Types:**
• Term Loan - term lending
• Revolving Credit - revolving credit
• Trade Finance - trade financing
• Invoice Financing - receivables financing
• Inventory Financing - stock financing
• Bridge Financing - bridge loans

**Requirements:**
• Business has been operating for at least 1 year
• Positive cash flow
• Current financial reports
• Good banking history
• Collateral according to provisions
• Clear business plan

TEZ Capital understands business dynamics that require flexibility in working capital management.',
                'featured_image' => 'services/working-capital-financing.jpg',
                'sort_order' => 2,
                'is_featured' => true,
                'tags' => ['working capital', 'financing', 'operational', 'cash flow', 'business'],
                'meta_title_id' => 'Pembiayaan Modal Kerja - TEZ Capital & Finance',
                'meta_title_en' => 'Working Capital Financing - TEZ Capital & Finance',
                'meta_description_id' => 'Solusi pembiayaan modal kerja dengan limit hingga Rp 50 miliar, fasilitas revolving credit dan suku bunga kompetitif.',
                'meta_description_en' => 'Working capital financing solution with limit up to IDR 50 billion, revolving credit facility and competitive interest rates.',
            ],
            [
                'category' => 'Financing',
                'title_id' => 'Pembiayaan Multi Guna',
                'title_en' => 'Multi-Purpose Financing',
                'excerpt_id' => 'Pembiayaan multi guna adalah pembiayaan untuk pengadaan barang konsumtif dan kebutuhan pribadi dengan jaminan properti.',
                'excerpt_en' => 'Multi-purpose financing is funding for consumptive goods procurement and personal needs with property collateral.',
                'content_id' => 'Pembiayaan multi guna memberikan fleksibilitas maksimal untuk berbagai kebutuhan finansial Anda dengan menggunakan properti sebagai jaminan.

**Keunggulan Pembiayaan Multi Guna:**
• Plafon hingga 80% dari nilai properti
• Tenor pembiayaan hingga 15 tahun
• Suku bunga tetap dan mengambang
• Bebas penalti pelunasan dipercepat
• Proses persetujuan 3-7 hari kerja
• Tidak ada batasan penggunaan dana

**Kebutuhan yang Dapat Dipenuhi:**
• Renovasi dan pembangunan rumah
• Biaya pendidikan anak
• Biaya kesehatan dan pengobatan
• Pembelian kendaraan pribadi
• Liburan dan rekreasi keluarga
• Investasi dan pengembangan usaha kecil
• Kebutuhan konsumtif lainnya

**Jenis Properti yang Dapat Dijaminkan:**
• Rumah tinggal (SHM/SHGB)
• Ruko dan rukan
• Apartemen dan kondominium
• Tanah kosong bersertifikat
• Gudang dan pabrik
• Villa dan rumah wisata

**Persyaratan Pengajuan:**
• Usia 21-60 tahun saat jatuh tempo
• Penghasilan minimal Rp 5 juta/bulan
• Sertifikat properti atas nama sendiri
• Dokumen penghasilan lengkap
• BI Checking bersih
• Tidak dalam daftar kredit macet

**Proses Mudah:**
1. Pengajuan dan survey lokasi
2. Penilaian properti oleh appraisal independen  
3. Analisa kredit dan persetujuan
4. Penandatanganan akad kredit
5. Pengikatan jaminan di notaris
6. Pencairan dana ke rekening

TEZ Capital memberikan solusi pembiayaan yang aman dan terpercaya untuk mewujudkan impian Anda.',
                'content_en' => 'Multi-purpose financing provides maximum flexibility for various financial needs using property as collateral.

**Multi-Purpose Financing Advantages:**
• Credit ceiling up to 80% of property value
• Financing tenor up to 15 years
• Fixed and floating interest rates
• Free from early settlement penalty
• Approval process 3-7 working days
• No restrictions on fund usage

**Needs that Can Be Met:**
• Home renovation and construction
• Children\'s education costs
• Health and medical expenses
• Personal vehicle purchase
• Family vacation and recreation
• Investment and small business development
• Other consumptive needs

**Types of Property that Can Be Used as Collateral:**
• Residential houses (SHM/SHGB)
• Shop houses and commercial buildings
• Apartments and condominiums
• Certified vacant land
• Warehouses and factories
• Villas and vacation homes

**Application Requirements:**
• Age 21-60 years at maturity
• Minimum income IDR 5 million/month
• Property certificate in own name
• Complete income documents
• Clean BI Checking
• Not in bad credit list

**Easy Process:**
1. Application and location survey
2. Property appraisal by independent appraiser
3. Credit analysis and approval
4. Credit agreement signing
5. Collateral binding at notary
6. Fund disbursement to account

TEZ Capital provides safe and reliable financing solutions to make your dreams come true.',
                'featured_image' => 'services/multi-purpose-financing.jpg',
                'sort_order' => 3,
                'is_featured' => true,
                'tags' => ['multi purpose', 'financing', 'property', 'personal', 'collateral'],
                'meta_title_id' => 'Pembiayaan Multi Guna - TEZ Capital & Finance',
                'meta_title_en' => 'Multi-Purpose Financing - TEZ Capital & Finance',
                'meta_description_id' => 'Pembiayaan multi guna dengan jaminan properti, plafon hingga 80% nilai properti, tenor hingga 15 tahun untuk berbagai kebutuhan.',
                'meta_description_en' => 'Multi-purpose financing with property collateral, credit ceiling up to 80% property value, tenor up to 15 years for various needs.',
            ]
        ];

        foreach ($services as $index => $serviceData) {
            $service = Content::create([
                'type' => 'service',
                'category' => $serviceData['category'],
                'title_id' => $serviceData['title_id'],
                'title_en' => $serviceData['title_en'],
                'excerpt_id' => $serviceData['excerpt_id'],
                'excerpt_en' => $serviceData['excerpt_en'],
                'content_id' => $serviceData['content_id'],
                'content_en' => $serviceData['content_en'],
                'featured_image' => $serviceData['featured_image'],
                'gallery' => json_encode([
                    'services/gallery-' . ($index + 1) . '-1.jpg',
                    'services/gallery-' . ($index + 1) . '-2.jpg',
                    'services/gallery-' . ($index + 1) . '-3.jpg',
                ]),
                'tags' => json_encode($serviceData['tags']),
                'sort_order' => $serviceData['sort_order'],
                'is_published' => true,
                'is_featured' => $serviceData['is_featured'],
                'status' => 'published',
                'published_at' => now(),
                'meta_title_id' => $serviceData['meta_title_id'],
                'meta_title_en' => $serviceData['meta_title_en'],
                'meta_description_id' => $serviceData['meta_description_id'],
                'meta_description_en' => $serviceData['meta_description_en'],
                'view_count' => rand(100, 2000),
                'like_count' => rand(10, 200),
                'share_count' => rand(5, 50),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "Created service: {$serviceData['title_id']}\n";
        }

        echo "Successfully created 3 services from TEZ Capital demo!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all services created by this migration
        Content::where('type', 'service')->delete();
        echo "Removed all services data.\n";
    }
};