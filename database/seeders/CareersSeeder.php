<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class CareersSeeder extends Seeder
{
    public function run()
    {
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
                    "Budget for marketing tools and training",
                    "Opportunity to attend marketing conferences",
                    "Team building activities",
                    "Professional development support"
                ],
                "posted_date" => "2025-01-03",
                "deadline" => "2025-02-03",
                "experience_level" => "senior"
            ],
            [
                "id" => "4",
                "title_id" => "HR Specialist",
                "title_en" => "HR Specialist",
                "category" => "Human Resources",
                "department" => "People & Operation",
                "location" => "Jakarta",
                "type" => "full-time",
                "description_id" => "Bergabunglah dengan tim People & Operation sebagai HR Specialist untuk mendukung berbagai inisiatif HR dan mengembangkan talent dalam organisasi. Posisi ini cocok untuk kandidat yang passionate tentang people development dan organizational culture.",
                "description_en" => "Join our People & Operation team as HR Specialist to support various HR initiatives and develop talent within the organization. This position is suitable for candidates passionate about people development and organizational culture.",
                "responsibilities_id" => [
                    "Mengelola proses recruitment dan selection",
                    "Mengembangkan program training dan development",
                    "Mengelola employee relations dan engagement",
                    "Memastikan compliance dengan employment law",
                    "Mengimplementasikan HR policies dan procedures",
                    "Mendukung performance management process"
                ],
                "responsibilities_en" => [
                    "Manage recruitment and selection processes",
                    "Develop training and development programs",
                    "Manage employee relations and engagement",
                    "Ensure compliance with employment law",
                    "Implement HR policies and procedures",
                    "Support performance management processes"
                ],
                "requirements_id" => [
                    "Sarjana Psikologi, Manajemen SDM, atau bidang terkait",
                    "Minimal 3 tahun pengalaman di bidang HR",
                    "Memahami employment law dan HR best practices",
                    "Kemampuan komunikasi dan interpersonal yang baik",
                    "Detail-oriented dan organized",
                    "Pengalaman dengan HRIS software"
                ],
                "requirements_en" => [
                    "Bachelor's degree in Psychology, HR Management, or related field",
                    "Minimum 3 years experience in HR",
                    "Understanding of employment law and HR best practices",
                    "Good communication and interpersonal skills",
                    "Detail-oriented and organized",
                    "Experience with HRIS software"
                ],
                "benefits_id" => [
                    "Competitive salary dan benefits",
                    "Professional HR certification support",
                    "Work-life balance programs",
                    "Employee wellness programs",
                    "Career advancement opportunities"
                ],
                "benefits_en" => [
                    "Competitive salary and benefits",
                    "Professional HR certification support",
                    "Work-life balance programs",
                    "Employee wellness programs",
                    "Career advancement opportunities"
                ],
                "posted_date" => "2025-01-07",
                "deadline" => "2025-02-07",
                "experience_level" => "mid"
            ],
            [
                "id" => "5",
                "title_id" => "Frontend Developer Intern",
                "title_en" => "Frontend Developer Intern",
                "category" => "Technology",
                "department" => "Technology",
                "location" => "Jakarta",
                "type" => "internship",
                "description_id" => "Program magang 6 bulan untuk mahasiswa atau fresh graduate yang ingin memulai karir di bidang frontend development. Anda akan bekerja dengan tim engineering yang berpengalaman dan mengembangkan skills dalam modern web technologies.",
                "description_en" => "6-month internship program for students or fresh graduates who want to start a career in frontend development. You will work with experienced engineering team and develop skills in modern web technologies.",
                "responsibilities_id" => [
                    "Mengembangkan komponen UI dengan Vue.js/React",
                    "Mengimplementasikan responsive design",
                    "Berkolaborasi dengan senior developers",
                    "Mempelajari best practices dalam frontend development",
                    "Melakukan testing dan debugging",
                    "Mendokumentasikan code dan processes"
                ],
                "responsibilities_en" => [
                    "Develop UI components with Vue.js/React",
                    "Implement responsive design",
                    "Collaborate with senior developers",
                    "Learn best practices in frontend development",
                    "Perform testing and debugging",
                    "Document code and processes"
                ],
                "requirements_id" => [
                    "Mahasiswa semester akhir atau fresh graduate",
                    "Dasar HTML, CSS, dan JavaScript",
                    "Familiar dengan Git version control",
                    "Motivasi tinggi untuk belajar",
                    "Kemampuan problem-solving yang baik",
                    "Dapat bekerja full-time selama 6 bulan"
                ],
                "requirements_en" => [
                    "Final year student or fresh graduate",
                    "Basic HTML, CSS, and JavaScript",
                    "Familiar with Git version control",
                    "High motivation to learn",
                    "Good problem-solving skills",
                    "Available to work full-time for 6 months"
                ],
                "benefits_id" => [
                    "Monthly stipend",
                    "Mentoring dari senior developers",
                    "Akses ke learning resources",
                    "Sertifikat completion",
                    "Kesempatan untuk full-time position",
                    "Real project experience"
                ],
                "benefits_en" => [
                    "Monthly stipend",
                    "Mentoring from senior developers",
                    "Access to learning resources",
                    "Completion certificate",
                    "Opportunity for full-time position",
                    "Real project experience"
                ],
                "posted_date" => "2025-01-10",
                "deadline" => "2025-01-25",
                "experience_level" => "entry"
            ]
        ];

        Configuration::updateOrCreate(
            [
                'group' => 'careers',
                'key' => 'careers_data'
            ],
            [
                'value' => json_encode($sampleCareers),
                'type' => 'json',
                'description' => 'Sample career data for the careers management system',
                'is_public' => true
            ]
        );
    }
}