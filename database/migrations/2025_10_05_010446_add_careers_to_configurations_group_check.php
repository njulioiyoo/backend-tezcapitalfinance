<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all existing groups to include them in the constraint
        $existingGroups = DB::table('configurations')->distinct()->pluck('group');
        $allowedGroups = collect(['general', 'about', 'branding', 'join_us', 'careers'])
            ->merge($existingGroups)
            ->unique()
            ->map(function($group) { return "'{$group}'"; })
            ->implode(', ');
        
        // Drop existing constraint
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check');
        
        // Add new constraint with all existing groups plus careers
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ({$allowedGroups}))");
        
        // Insert sample careers data
        $sampleCareers = [
            [
                "id" => "1",
                "title_id" => "Supervisor Procurement",
                "title_en" => "Procurement Supervisor",
                "category" => "Procurement",
                "department" => "Finance",
                "location" => "Jakarta",
                "type" => "full-time",
                "description_id" => "Kami mencari Supervisor Procurement yang berpengalaman untuk memimpin tim pengadaan dan memastikan proses procurement yang efisien dan efektif. Kandidat yang ideal memiliki pengalaman dalam manajemen vendor, negosiasi kontrak, dan optimasi biaya.",
                "description_en" => "We are looking for an experienced Procurement Supervisor to lead our procurement team and ensure efficient and effective procurement processes. The ideal candidate has experience in vendor management, contract negotiation, and cost optimization.",
                "responsibilities_id" => [
                    "Memimpin dan mengelola tim procurement",
                    "Mengembangkan strategi pengadaan yang efektif",
                    "Melakukan negosiasi dengan vendor dan supplier",
                    "Memastikan kepatuhan terhadap kebijakan procurement",
                    "Menganalisis dan mengoptimalkan biaya pengadaan",
                    "Membangun hubungan strategis dengan key suppliers"
                ],
                "responsibilities_en" => [
                    "Lead and manage the procurement team",
                    "Develop effective procurement strategies",
                    "Negotiate with vendors and suppliers",
                    "Ensure compliance with procurement policies",
                    "Analyze and optimize procurement costs",
                    "Build strategic relationships with key suppliers"
                ],
                "requirements_id" => [
                    "Sarjana di bidang Supply Chain, Business, atau Engineering",
                    "Minimal 5 tahun pengalaman di bidang procurement",
                    "Pengalaman memimpin tim minimal 2 tahun",
                    "Kemampuan negosiasi yang kuat",
                    "Menguasai sistem ERP dan Microsoft Office",
                    "Kemampuan bahasa Inggris yang baik"
                ],
                "requirements_en" => [
                    "Bachelor's degree in Supply Chain, Business, or Engineering",
                    "Minimum 5 years experience in procurement",
                    "Minimum 2 years team leadership experience",
                    "Strong negotiation skills",
                    "Proficiency in ERP systems and Microsoft Office",
                    "Good English language skills"
                ],
                "benefits_id" => [
                    "Gaji kompetitif sesuai pengalaman",
                    "Tunjangan kesehatan dan keluarga",
                    "Program pengembangan karir",
                    "Bonus kinerja tahunan",
                    "Flexible working arrangement"
                ],
                "benefits_en" => [
                    "Competitive salary based on experience",
                    "Health and family allowances",
                    "Career development programs",
                    "Annual performance bonus",
                    "Flexible working arrangement"
                ],
                "posted_date" => "2025-01-01",
                "deadline" => "2025-01-31",
                "experience_level" => "mid"
            ],
            [
                "id" => "2",
                "title_id" => "Software Engineer",
                "title_en" => "Software Engineer",
                "category" => "Engineering",
                "department" => "Technology",
                "location" => "Jakarta",
                "type" => "full-time",
                "description_id" => "Bergabunglah dengan tim teknologi kami sebagai Software Engineer untuk mengembangkan aplikasi web dan mobile yang inovatif. Kami mencari individu yang passionate tentang teknologi dan ingin berkontribusi dalam membangun solusi digital yang berdampak.",
                "description_en" => "Join our technology team as a Software Engineer to develop innovative web and mobile applications. We are looking for individuals passionate about technology who want to contribute to building impactful digital solutions.",
                "responsibilities_id" => [
                    "Mengembangkan aplikasi web menggunakan modern framework",
                    "Membangun RESTful API yang scalable",
                    "Melakukan code review dan testing",
                    "Berkolaborasi dengan tim UI/UX designer",
                    "Mengoptimalkan performa aplikasi",
                    "Memaintain dan mengupdate sistem existing"
                ],
                "responsibilities_en" => [
                    "Develop web applications using modern frameworks",
                    "Build scalable RESTful APIs",
                    "Conduct code reviews and testing",
                    "Collaborate with UI/UX design team",
                    "Optimize application performance",
                    "Maintain and update existing systems"
                ],
                "requirements_id" => [
                    "Sarjana Teknik Informatika atau bidang terkait",
                    "Minimal 2 tahun pengalaman web development",
                    "Menguasai JavaScript, PHP, atau Python",
                    "Pengalaman dengan framework Laravel, Vue.js, atau React",
                    "Familiar dengan database MySQL/PostgreSQL",
                    "Memahami konsep Git version control"
                ],
                "requirements_en" => [
                    "Bachelor's degree in Computer Science or related field",
                    "Minimum 2 years web development experience",
                    "Proficiency in JavaScript, PHP, or Python",
                    "Experience with Laravel, Vue.js, or React frameworks",
                    "Familiar with MySQL/PostgreSQL databases",
                    "Understanding of Git version control"
                ],
                "benefits_id" => [
                    "Gaji sesuai standar industri",
                    "Program training dan sertifikasi",
                    "Peralatan kerja modern (MacBook/Windows)",
                    "Remote work flexibility",
                    "Annual company retreat"
                ],
                "benefits_en" => [
                    "Industry-standard salary",
                    "Training and certification programs",
                    "Modern work equipment (MacBook/Windows)",
                    "Remote work flexibility",
                    "Annual company retreat"
                ],
                "posted_date" => "2025-01-05",
                "deadline" => "2025-02-05",
                "experience_level" => "entry"
            ],
            [
                "id" => "3",
                "title_id" => "Marketing Manager",
                "title_en" => "Marketing Manager",
                "category" => "Marketing",
                "department" => "Marketing",
                "location" => "Surabaya",
                "type" => "full-time",
                "description_id" => "Kami mencari Marketing Manager yang kreatif dan berpengalaman untuk memimpin strategi marketing dan meningkatkan brand awareness perusahaan. Posisi ini ideal untuk individu yang memiliki passion dalam digital marketing dan brand management.",
                "description_en" => "We are looking for a creative and experienced Marketing Manager to lead marketing strategies and increase company brand awareness. This position is ideal for individuals passionate about digital marketing and brand management.",
                "responsibilities_id" => [
                    "Mengembangkan dan melaksanakan strategi marketing",
                    "Mengelola campaign digital marketing",
                    "Menganalisis market trends dan kompetitor",
                    "Mengelola social media dan content marketing",
                    "Berkoordinasi dengan tim sales dan product",
                    "Mengukur dan melaporkan ROI marketing activities"
                ],
                "responsibilities_en" => [
                    "Develop and execute marketing strategies",
                    "Manage digital marketing campaigns",
                    "Analyze market trends and competitors",
                    "Manage social media and content marketing",
                    "Coordinate with sales and product teams",
                    "Measure and report ROI of marketing activities"
                ],
                "requirements_id" => [
                    "Sarjana Marketing, Komunikasi, atau bidang terkait",
                    "Minimal 4 tahun pengalaman di bidang marketing",
                    "Pengalaman dalam digital marketing dan social media",
                    "Kemampuan analitis yang kuat",
                    "Kreatif dalam mengembangkan campaign",
                    "Kemampuan leadership dan komunikasi yang baik"
                ],
                "requirements_en" => [
                    "Bachelor's degree in Marketing, Communications, or related field",
                    "Minimum 4 years experience in marketing",
                    "Experience in digital marketing and social media",
                    "Strong analytical skills",
                    "Creative in developing campaigns",
                    "Good leadership and communication skills"
                ],
                "benefits_id" => [
                    "Paket kompensasi yang menarik",
                    "Budget untuk marketing tools dan training",
                    "Kesempatan menghadiri conference marketing",
                    "Team building activities",
                    "Professional development support"
                ],
                "benefits_en" => [
                    "Attractive compensation package",
                    "Budget for marketing tools dan training",
                    "Opportunity to attend marketing conferences",
                    "Team building activities",
                    "Professional development support"
                ],
                "posted_date" => "2025-01-03",
                "deadline" => "2025-02-03",
                "experience_level" => "senior"
            ]
        ];

        // Insert or update the careers configuration
        DB::table('configurations')->updateOrInsert(
            [
                'group' => 'careers',
                'key' => 'careers_data'
            ],
            [
                'value' => json_encode($sampleCareers),
                'type' => 'json',
                'description' => 'Sample career data for the careers management system',
                'is_public' => true,
                'updated_at' => now(),
                'created_at' => now()
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check');
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'about', 'branding', 'join_us'))");
    }
};
