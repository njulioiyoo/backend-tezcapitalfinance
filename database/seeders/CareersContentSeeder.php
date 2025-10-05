<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CareersContentSeeder extends Seeder
{
    public function run()
    {
        $careers = [
            [
                'type' => 'career',
                'title_id' => 'Supervisor Procurement',
                'title_en' => 'Procurement Supervisor',
                'excerpt_id' => 'Supervisor Procurement yang berpengalaman untuk memimpin tim pengadaan.',
                'excerpt_en' => 'Experienced Procurement Supervisor to lead procurement team.',
                'content_id' => 'Kami mencari Supervisor Procurement yang berpengalaman untuk memimpin tim pengadaan dan memastikan proses procurement yang efisien dan efektif. Kandidat yang ideal memiliki pengalaman dalam manajemen vendor, negosiasi kontrak, dan optimasi biaya.',
                'content_en' => 'We are looking for an experienced Procurement Supervisor to lead our procurement team and ensure efficient and effective procurement processes. The ideal candidate has experience in vendor management, contract negotiation, and cost optimization.',
                'requirements_id' => [
                    'Sarjana di bidang Supply Chain, Business, atau Engineering',
                    'Minimal 5 tahun pengalaman di bidang procurement',
                    'Pengalaman memimpin tim minimal 2 tahun',
                    'Kemampuan negosiasi yang kuat',
                    'Menguasai sistem ERP dan Microsoft Office',
                    'Kemampuan bahasa Inggris yang baik'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Supply Chain, Business, or Engineering',
                    'Minimum 5 years experience in procurement',
                    'Minimum 2 years team leadership experience',
                    'Strong negotiation skills',
                    'Proficiency in ERP systems and Microsoft Office',
                    'Good English language skills'
                ],
                'benefits_id' => [
                    'Gaji kompetitif sesuai pengalaman',
                    'Tunjangan kesehatan dan keluarga',
                    'Program pengembangan karir',
                    'Bonus kinerja tahunan',
                    'Flexible working arrangement'
                ],
                'benefits_en' => [
                    'Competitive salary based on experience',
                    'Health and family allowances',
                    'Career development programs',
                    'Annual performance bonus',
                    'Flexible working arrangement'
                ],
                'location_id' => 'Jakarta',
                'location_en' => 'Jakarta',
                'department_id' => 'Finance',
                'department_en' => 'Finance',
                'tags' => ['Finance', 'full-time', 'mid-level'],
                'is_published' => true,
                'published_at' => Carbon::parse('2025-01-01'),
                'status' => 'published',
                'start_date' => Carbon::parse('2025-01-01'),
                'end_date' => Carbon::parse('2025-01-31'),
            ],
            [
                'type' => 'career',
                'title_id' => 'Software Engineer',
                'title_en' => 'Software Engineer',
                'excerpt_id' => 'Software Engineer untuk mengembangkan aplikasi web dan mobile yang inovatif.',
                'excerpt_en' => 'Software Engineer to develop innovative web and mobile applications.',
                'content_id' => 'Bergabunglah dengan tim teknologi kami sebagai Software Engineer untuk mengembangkan aplikasi web dan mobile yang inovatif. Kami mencari individu yang passionate tentang teknologi dan ingin berkontribusi dalam membangun solusi digital yang berdampak.',
                'content_en' => 'Join our technology team as a Software Engineer to develop innovative web and mobile applications. We are looking for individuals passionate about technology who want to contribute to building impactful digital solutions.',
                'requirements_id' => [
                    'Sarjana Teknik Informatika atau bidang terkait',
                    'Minimal 2 tahun pengalaman web development',
                    'Menguasai JavaScript, PHP, atau Python',
                    'Pengalaman dengan framework Laravel, Vue.js, atau React',
                    'Familiar dengan database MySQL/PostgreSQL',
                    'Memahami konsep Git version control'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Computer Science or related field',
                    'Minimum 2 years web development experience',
                    'Proficiency in JavaScript, PHP, or Python',
                    'Experience with Laravel, Vue.js, or React frameworks',
                    'Familiar with MySQL/PostgreSQL databases',
                    'Understanding of Git version control'
                ],
                'benefits_id' => [
                    'Gaji sesuai standar industri',
                    'Program training dan sertifikasi',
                    'Peralatan kerja modern (MacBook/Windows)',
                    'Remote work flexibility',
                    'Annual company retreat'
                ],
                'benefits_en' => [
                    'Industry-standard salary',
                    'Training and certification programs',
                    'Modern work equipment (MacBook/Windows)',
                    'Remote work flexibility',
                    'Annual company retreat'
                ],
                'location_id' => 'Jakarta',
                'location_en' => 'Jakarta',
                'department_id' => 'Technology',
                'department_en' => 'Technology',
                'tags' => ['Technology', 'full-time', 'entry-level'],
                'is_published' => true,
                'published_at' => Carbon::parse('2025-01-05'),
                'status' => 'published',
                'start_date' => Carbon::parse('2025-01-05'),
                'end_date' => Carbon::parse('2025-02-05'),
            ],
            [
                'type' => 'career',
                'title_id' => 'Marketing Manager',
                'title_en' => 'Marketing Manager',
                'excerpt_id' => 'Marketing Manager yang kreatif untuk memimpin strategi marketing.',
                'excerpt_en' => 'Creative Marketing Manager to lead marketing strategies.',
                'content_id' => 'Kami mencari Marketing Manager yang kreatif dan berpengalaman untuk memimpin strategi marketing dan meningkatkan brand awareness perusahaan. Posisi ini ideal untuk individu yang memiliki passion dalam digital marketing dan brand management.',
                'content_en' => 'We are looking for a creative and experienced Marketing Manager to lead marketing strategies and increase company brand awareness. This position is ideal for individuals passionate about digital marketing and brand management.',
                'requirements_id' => [
                    'Sarjana Marketing, Komunikasi, atau bidang terkait',
                    'Minimal 4 tahun pengalaman di bidang marketing',
                    'Pengalaman dalam digital marketing dan social media',
                    'Kemampuan analitis yang kuat',
                    'Kreatif dalam mengembangkan campaign',
                    'Kemampuan leadership dan komunikasi yang baik'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Marketing, Communications, or related field',
                    'Minimum 4 years experience in marketing',
                    'Experience in digital marketing and social media',
                    'Strong analytical skills',
                    'Creative in developing campaigns',
                    'Good leadership and communication skills'
                ],
                'benefits_id' => [
                    'Paket kompensasi yang menarik',
                    'Budget untuk marketing tools dan training',
                    'Kesempatan menghadiri conference marketing',
                    'Team building activities',
                    'Professional development support'
                ],
                'benefits_en' => [
                    'Attractive compensation package',
                    'Budget for marketing tools dan training',
                    'Opportunity to attend marketing conferences',
                    'Team building activities',
                    'Professional development support'
                ],
                'location_id' => 'Surabaya',
                'location_en' => 'Surabaya',
                'department_id' => 'Marketing',
                'department_en' => 'Marketing',
                'tags' => ['Marketing', 'full-time', 'senior-level'],
                'is_published' => true,
                'published_at' => Carbon::parse('2025-01-03'),
                'status' => 'published',
                'start_date' => Carbon::parse('2025-01-03'),
                'end_date' => Carbon::parse('2025-02-03'),
            ],
            [
                'type' => 'career',
                'title_id' => 'HR Specialist',
                'title_en' => 'HR Specialist',
                'excerpt_id' => 'HR Specialist untuk mendukung inisiatif HR dan pengembangan talent.',
                'excerpt_en' => 'HR Specialist to support HR initiatives and talent development.',
                'content_id' => 'Bergabunglah dengan tim People & Operation sebagai HR Specialist untuk mendukung berbagai inisiatif HR dan mengembangkan talent dalam organisasi. Posisi ini cocok untuk kandidat yang passionate tentang people development dan organizational culture.',
                'content_en' => 'Join our People & Operation team as HR Specialist to support various HR initiatives and develop talent within the organization. This position is suitable for candidates passionate about people development and organizational culture.',
                'requirements_id' => [
                    'Sarjana Psikologi, Manajemen SDM, atau bidang terkait',
                    'Minimal 3 tahun pengalaman di bidang HR',
                    'Memahami employment law dan HR best practices',
                    'Kemampuan komunikasi dan interpersonal yang baik',
                    'Detail-oriented dan organized',
                    'Pengalaman dengan HRIS software'
                ],
                'requirements_en' => [
                    'Bachelor\'s degree in Psychology, HR Management, or related field',
                    'Minimum 3 years experience in HR',
                    'Understanding of employment law and HR best practices',
                    'Good communication and interpersonal skills',
                    'Detail-oriented and organized',
                    'Experience with HRIS software'
                ],
                'benefits_id' => [
                    'Competitive salary dan benefits',
                    'Professional HR certification support',
                    'Work-life balance programs',
                    'Employee wellness programs',
                    'Career advancement opportunities'
                ],
                'benefits_en' => [
                    'Competitive salary and benefits',
                    'Professional HR certification support',
                    'Work-life balance programs',
                    'Employee wellness programs',
                    'Career advancement opportunities'
                ],
                'location_id' => 'Jakarta',
                'location_en' => 'Jakarta',
                'department_id' => 'People & Operation',
                'department_en' => 'People & Operation',
                'tags' => ['People & Operation', 'full-time', 'mid-level'],
                'is_published' => true,
                'published_at' => Carbon::parse('2025-01-07'),
                'status' => 'published',
                'start_date' => Carbon::parse('2025-01-07'),
                'end_date' => Carbon::parse('2025-02-07'),
            ],
            [
                'type' => 'career',
                'title_id' => 'Frontend Developer Intern',
                'title_en' => 'Frontend Developer Intern',
                'excerpt_id' => 'Program magang 6 bulan untuk frontend development.',
                'excerpt_en' => '6-month internship program for frontend development.',
                'content_id' => 'Program magang 6 bulan untuk mahasiswa atau fresh graduate yang ingin memulai karir di bidang frontend development. Anda akan bekerja dengan tim engineering yang berpengalaman dan mengembangkan skills dalam modern web technologies.',
                'content_en' => '6-month internship program for students or fresh graduates who want to start a career in frontend development. You will work with experienced engineering team and develop skills in modern web technologies.',
                'requirements_id' => [
                    'Mahasiswa semester akhir atau fresh graduate',
                    'Dasar HTML, CSS, dan JavaScript',
                    'Familiar dengan Git version control',
                    'Motivasi tinggi untuk belajar',
                    'Kemampuan problem-solving yang baik',
                    'Dapat bekerja full-time selama 6 bulan'
                ],
                'requirements_en' => [
                    'Final year student or fresh graduate',
                    'Basic HTML, CSS, and JavaScript',
                    'Familiar with Git version control',
                    'High motivation to learn',
                    'Good problem-solving skills',
                    'Available to work full-time for 6 months'
                ],
                'benefits_id' => [
                    'Monthly stipend',
                    'Mentoring dari senior developers',
                    'Akses ke learning resources',
                    'Sertifikat completion',
                    'Kesempatan untuk full-time position',
                    'Real project experience'
                ],
                'benefits_en' => [
                    'Monthly stipend',
                    'Mentoring from senior developers',
                    'Access to learning resources',
                    'Completion certificate',
                    'Opportunity for full-time position',
                    'Real project experience'
                ],
                'location_id' => 'Jakarta',
                'location_en' => 'Jakarta',
                'department_id' => 'Technology',
                'department_en' => 'Technology',
                'tags' => ['Technology', 'internship', 'entry-level'],
                'is_published' => true,
                'published_at' => Carbon::parse('2025-01-10'),
                'status' => 'published',
                'start_date' => Carbon::parse('2025-01-10'),
                'end_date' => Carbon::parse('2025-01-25'),
            ]
        ];

        foreach ($careers as $career) {
            Content::create($career);
        }
    }
}