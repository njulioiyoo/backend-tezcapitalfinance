<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuration;

class CareersDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careersData = [
            [
                'id' => '1',
                'title_id' => 'Supervisor Procurement',
                'title_en' => 'Procurement Supervisor',
                'category' => 'Procurement',
                'department' => 'Finance',
                'location' => 'Jakarta',
                'type' => 'full-time',
                'experience_level' => 'mid',
                'description_id' => 'Kami mencari Supervisor Procurement berpengalaman untuk bergabung dengan tim kami. Kandidat ideal akan mengawasi aktivitas procurement dan memastikan keputusan pembelian yang efektif biaya.',
                'description_en' => 'We are looking for an experienced Procurement Supervisor to join our team. The ideal candidate will oversee procurement activities and ensure cost-effective purchasing decisions.',
                'responsibilities_id' => [
                    'Mengawasi operasi dan aktivitas procurement harian',
                    'Mengembangkan dan menerapkan strategi dan kebijakan procurement',
                    'Mengelola hubungan supplier dan mengevaluasi performa vendor',
                    'Negosiasi kontrak dan harga dengan supplier',
                    'Monitoring budget procurement dan memastikan efektivitas biaya',
                    'Memimpin dan supervisi anggota tim procurement',
                    'Memastikan kepatuhan terhadap kebijakan perusahaan dan regulasi',
                    'Menyiapkan laporan dan analisis procurement'
                ],
                'responsibilities_en' => [
                    'Oversee daily procurement operations and activities',
                    'Develop and implement procurement strategies and policies',
                    'Manage supplier relationships and evaluate vendor performance',
                    'Negotiate contracts and pricing with suppliers',
                    'Monitor procurement budgets and ensure cost-effectiveness',
                    'Lead and supervise procurement team members',
                    'Ensure compliance with company policies and regulations',
                    'Prepare procurement reports and analysis'
                ],
                'requirements_id' => [
                    'Gelar Sarjana di bidang Administrasi Bisnis, Supply Chain Management, atau bidang terkait',
                    'Minimal 3-5 tahun pengalaman di bidang procurement atau supply chain management',
                    'Kemampuan negosiasi dan analitis yang kuat',
                    'Kemahiran dalam MS Office Suite dan software procurement',
                    'Kemampuan komunikasi dan kepemimpinan yang excellent',
                    'Pengetahuan tentang regulasi procurement dan best practices'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Business Administration, Supply Chain Management, or related field',
                    'Minimum 3-5 years of experience in procurement or supply chain management',
                    'Strong negotiation and analytical skills',
                    'Proficiency in MS Office Suite and procurement software',
                    'Excellent communication and leadership skills',
                    'Knowledge of procurement regulations and best practices'
                ],
                'benefits_id' => [
                    'Gaji kompetitif dan bonus performa',
                    'Asuransi kesehatan komprehensif',
                    'Peluang pengembangan profesional',
                    'Pengaturan kerja yang fleksibel',
                    'Cuti tahunan dan cuti sakit',
                    'Program wellness karyawan'
                ],
                'benefits_en' => [
                    'Competitive salary and performance bonuses',
                    'Comprehensive health insurance',
                    'Professional development opportunities',
                    'Flexible working arrangements',
                    'Annual leave and sick leave',
                    'Employee wellness programs'
                ],
                'posted_date' => '2024-09-15',
                'deadline' => '2024-11-15'
            ],
            [
                'id' => '2',
                'title_id' => 'Senior Talent Acquisition',
                'title_en' => 'Senior Talent Acquisition',
                'category' => 'Talent Acquisition',
                'department' => 'People & Operation',
                'location' => 'Jakarta',
                'type' => 'full-time',
                'experience_level' => 'senior',
                'description_id' => 'Bergabunglah dengan tim People & Operation kami sebagai spesialis Senior Talent Acquisition. Anda akan bertanggung jawab untuk menarik, merekrut, dan mempertahankan talenta terbaik untuk organisasi kami.',
                'description_en' => 'Join our People & Operation team as a Senior Talent Acquisition specialist. You will be responsible for attracting, recruiting, and retaining top talent for our organization.',
                'responsibilities_id' => [
                    'Mengembangkan dan mengeksekusi strategi recruitment yang komprehensif',
                    'Sourcing, screening, dan interview kandidat untuk berbagai posisi',
                    'Membangun dan memelihara talent pipeline untuk peran-peran kritikal',
                    'Berkolaborasi dengan hiring manager untuk memahami kebutuhan staffing',
                    'Mengelola siklus recruitment lengkap dari job posting hingga onboarding',
                    'Meningkatkan employer branding dan recruitment marketing',
                    'Menganalisis metrik recruitment dan memberikan insights',
                    'Tetap update dengan tren industri dan best practices'
                ],
                'responsibilities_en' => [
                    'Develop and execute comprehensive recruitment strategies',
                    'Source, screen, and interview candidates for various positions',
                    'Build and maintain talent pipelines for critical roles',
                    'Collaborate with hiring managers to understand staffing needs',
                    'Manage the full recruitment lifecycle from job posting to onboarding',
                    'Enhance employer branding and recruitment marketing',
                    'Analyze recruitment metrics and provide insights',
                    'Stay updated on industry trends and best practices'
                ],
                'requirements_id' => [
                    'Gelar Sarjana di bidang Human Resources, Psikologi, atau bidang terkait',
                    'Minimal 5-7 tahun pengalaman di bidang talent acquisition atau recruitment',
                    'Pemahaman kuat tentang best practices dan teknik recruitment',
                    'Pengalaman dengan applicant tracking systems (ATS)',
                    'Kemampuan interpersonal dan komunikasi yang excellent',
                    'Pengetahuan tentang hukum dan regulasi ketenagakerjaan',
                    'Kemampuan bekerja di lingkungan yang fast-paced'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Human Resources, Psychology, or related field',
                    'Minimum 5-7 years of experience in talent acquisition or recruitment',
                    'Strong understanding of recruitment best practices and techniques',
                    'Experience with applicant tracking systems (ATS)',
                    'Excellent interpersonal and communication skills',
                    'Knowledge of employment laws and regulations',
                    'Ability to work in fast-paced environment'
                ],
                'benefits_id' => [
                    'Paket gaji yang kompetitif',
                    'Asuransi kesehatan dan gigi',
                    'Budget learning dan development',
                    'Fleksibilitas remote work',
                    'Insentif berbasis performa',
                    'Peluang career advancement'
                ],
                'benefits_en' => [
                    'Competitive salary package',
                    'Health and dental insurance',
                    'Learning and development budget',
                    'Remote work flexibility',
                    'Performance-based incentives',
                    'Career advancement opportunities'
                ],
                'posted_date' => '2024-09-20',
                'deadline' => '2024-11-20'
            ],
            [
                'id' => '3',
                'title_id' => 'Transfer Pricing Assistant',
                'title_en' => 'Transfer Pricing Assistant',
                'category' => 'Tax',
                'department' => 'Finance',
                'location' => 'Jakarta',
                'type' => 'full-time',
                'experience_level' => 'entry',
                'description_id' => 'Kami mencari Transfer Pricing Assistant yang detail-oriented untuk mendukung aktivitas tax compliance dan dokumentasi transfer pricing kami.',
                'description_en' => 'We are seeking a detail-oriented Transfer Pricing Assistant to support our tax compliance and transfer pricing documentation activities.',
                'responsibilities_id' => [
                    'Membantu dalam penyusunan dokumentasi transfer pricing',
                    'Mendukung studi dan analisis transfer pricing',
                    'Memelihara database dan record transfer pricing',
                    'Membantu dalam aktivitas tax compliance dan reporting',
                    'Riset regulasi pajak dan guidelines transfer pricing',
                    'Mendukung senior staff dalam client engagement',
                    'Menyiapkan working papers dan dokumentasi',
                    'Membantu dalam tugas administratif dan file management'
                ],
                'responsibilities_en' => [
                    'Assist in preparation of transfer pricing documentation',
                    'Support transfer pricing studies and analysis',
                    'Maintain transfer pricing databases and records',
                    'Assist in tax compliance and reporting activities',
                    'Research tax regulations and transfer pricing guidelines',
                    'Support senior staff in client engagements',
                    'Prepare working papers and documentation',
                    'Assist in administrative tasks and file management'
                ],
                'requirements_id' => [
                    'Gelar Sarjana di bidang Akuntansi, Finance, Tax, atau bidang terkait',
                    'Fresh graduate atau 1-2 tahun pengalaman di bidang tax atau accounting',
                    'Pengetahuan tentang regulasi transfer pricing dan dokumentasi',
                    'Kemampuan analitis dan research yang kuat',
                    'Kemahiran dalam Excel dan software accounting',
                    'Kemampuan bahasa Inggris dan Bahasa Indonesia yang baik',
                    'Sertifikasi CPA atau tax merupakan nilai plus'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Accounting, Finance, Tax, or related field',
                    'Fresh graduate or 1-2 years of experience in tax or accounting',
                    'Knowledge of transfer pricing regulations and documentation',
                    'Strong analytical and research skills',
                    'Proficiency in Excel and accounting software',
                    'Good command of English and Bahasa Indonesia',
                    'CPA or tax certification is a plus'
                ],
                'benefits_id' => [
                    'Gaji entry-level yang kompetitif',
                    'Program training komprehensif',
                    'Coverage asuransi kesehatan',
                    'Support sertifikasi profesional',
                    'Peluang mentorship',
                    'Career growth pathway'
                ],
                'benefits_en' => [
                    'Competitive entry-level salary',
                    'Comprehensive training program',
                    'Health insurance coverage',
                    'Professional certification support',
                    'Mentorship opportunities',
                    'Career growth pathway'
                ],
                'posted_date' => '2024-09-25',
                'deadline' => '2024-11-25'
            ],
            [
                'id' => '4',
                'title_id' => 'Talent Acquisition Senior Manager',
                'title_en' => 'Talent Acquisition Senior Manager',
                'category' => 'Talent Acquisition',
                'department' => 'People & Operation',
                'location' => 'Jakarta',
                'type' => 'full-time',
                'experience_level' => 'senior',
                'description_id' => 'Pimpin fungsi talent acquisition kami sebagai Senior Manager. Anda akan bertanggung jawab untuk mengembangkan strategi recruitment dan membangun tim talent acquisition yang high-performing.',
                'description_en' => 'Lead our talent acquisition function as a Senior Manager. You will be responsible for developing recruitment strategies and building a high-performing talent acquisition team.',
                'responsibilities_id' => [
                    'Memimpin dan mengelola tim talent acquisition',
                    'Mengembangkan dan mengimplementasikan strategi talent acquisition yang sejalan dengan tujuan bisnis',
                    'Bermitra dengan senior leadership dalam workforce planning',
                    'Mengawasi proses recruitment dan memastikan quality hiring',
                    'Menggerakkan employer branding dan talent marketing initiatives',
                    'Mengelola budget recruitment dan vendor relationships',
                    'Menetapkan metrik dan KPI recruitment',
                    'Memastikan kepatuhan terhadap regulasi ketenagakerjaan'
                ],
                'responsibilities_en' => [
                    'Lead and manage the talent acquisition team',
                    'Develop and implement talent acquisition strategies aligned with business goals',
                    'Partner with senior leadership on workforce planning',
                    'Oversee recruitment processes and ensure quality hiring',
                    'Drive employer branding and talent marketing initiatives',
                    'Manage recruitment budgets and vendor relationships',
                    'Establish recruitment metrics and KPIs',
                    'Ensure compliance with employment regulations'
                ],
                'requirements_id' => [
                    'Gelar Sarjana atau Master di bidang Human Resources atau bidang terkait',
                    'Minimal 8-10 tahun pengalaman di talent acquisition dengan peran leadership',
                    'Track record yang terbukti dalam membangun dan memimpin tim recruitment',
                    'Strategic thinking dan business acumen',
                    'Pengalaman dengan talent acquisition technology dan tools',
                    'Kemampuan leadership dan people management yang kuat',
                    'Kemampuan stakeholder management yang excellent'
                ],
                'requirements_en' => [
                    'Bachelor\'s or Master\'s degree in Human Resources or related field',
                    'Minimum 8-10 years of experience in talent acquisition with leadership role',
                    'Proven track record in building and leading recruitment teams',
                    'Strategic thinking and business acumen',
                    'Experience with talent acquisition technology and tools',
                    'Strong leadership and people management skills',
                    'Excellent stakeholder management abilities'
                ],
                'benefits_id' => [
                    'Paket kompensasi eksekutif',
                    'Stock options atau equity participation',
                    'Executive health benefits',
                    'Program pengembangan leadership',
                    'Pengaturan kerja yang fleksibel',
                    'Peluang international assignment'
                ],
                'benefits_en' => [
                    'Executive compensation package',
                    'Stock options or equity participation',
                    'Executive health benefits',
                    'Leadership development programs',
                    'Flexible work arrangements',
                    'International assignment opportunities'
                ],
                'posted_date' => '2024-09-10',
                'deadline' => '2024-11-10'
            ]
        ];

        Configuration::updateOrCreate(
            [
                'group' => Configuration::GROUP_CAREERS,
                'key' => 'careers_data'
            ],
            [
                'value' => json_encode($careersData),
                'type' => 'json',
                'description' => 'Careers data with bilingual support',
                'is_public' => true
            ]
        );
    }
}
