<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeBenefitsConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $employeeBenefitsItems = [
            [
                'category_title_id' => 'Informasi Dasar',
                'category_title_en' => 'Basic Information',
                'items' => [
                    [
                        'title_id' => 'Cuti Tahunan',
                        'title_en' => 'Annual Holiday',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Karyawan mendapatkan cuti tahunan sesuai dengan kebijakan perusahaan untuk menjaga keseimbangan kehidupan kerja.',
                        'description_en' => 'Employees receive annual leave according to company policy to maintain work-life balance.'
                    ],
                    [
                        'title_id' => 'Tingkat Penggunaan Cuti Berbayar',
                        'title_en' => 'Paid Leave Utilization Rate',
                        'icon' => '',
                        'percentage' => '100%',
                        'description_id' => 'Tingkat penggunaan cuti berbayar yang tinggi menunjukkan budaya kerja yang sehat dan mendukung kesejahteraan karyawan.',
                        'description_en' => 'High paid leave utilization rate shows healthy work culture and supports employee wellbeing.'
                    ],
                    [
                        'title_id' => 'Rata-rata Tingkat Turnover',
                        'title_en' => 'Average Turnover Rate',
                        'icon' => '',
                        'percentage' => '10%',
                        'description_id' => 'Tingkat turnover yang rendah menunjukkan kepuasan karyawan yang tinggi dan lingkungan kerja yang positif.',
                        'description_en' => 'Low turnover rate indicates high employee satisfaction and positive work environment.'
                    ]
                ]
            ],
            [
                'category_title_id' => 'Subsidi dan Tunjangan',
                'category_title_en' => 'Subsidies and Allowances',
                'items' => [
                    [
                        'title_id' => 'Tunjangan Transportasi',
                        'title_en' => 'Transportation Allowance',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Perusahaan menyediakan tunjangan transportasi untuk membantu biaya perjalanan karyawan ke tempat kerja.',
                        'description_en' => 'Company provides transportation allowance to help employee commuting costs to workplace.'
                    ],
                    [
                        'title_id' => 'Tunjangan Makan',
                        'title_en' => 'Meal Allowance',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Tunjangan makan disediakan untuk membantu kebutuhan nutrisi karyawan selama jam kerja.',
                        'description_en' => 'Meal allowance is provided to help employee nutrition needs during working hours.'
                    ],
                    [
                        'title_id' => 'Asuransi Kesehatan',
                        'title_en' => 'Health Insurance',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Asuransi kesehatan komprehensif untuk karyawan dan keluarga sebagai bagian dari komitmen kesejahteraan.',
                        'description_en' => 'Comprehensive health insurance for employees and family as part of welfare commitment.'
                    ]
                ]
            ],
            [
                'category_title_id' => 'Pernikahan dan Kelahiran',
                'category_title_en' => 'Marriage and Childbirth',
                'items' => [
                    [
                        'title_id' => 'Uang Hadiah Pernikahan',
                        'title_en' => 'Wedding Gift Money',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Perusahaan memberikan hadiah berupa uang untuk merayakan momen bahagia pernikahan karyawan.',
                        'description_en' => 'Company provides monetary gift to celebrate employee happy wedding moments.'
                    ],
                    [
                        'title_id' => 'Uang Hadiah Kelahiran',
                        'title_en' => 'Birth Gift Money',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Hadiah uang diberikan untuk merayakan kelahiran anak karyawan sebagai bentuk dukungan keluarga.',
                        'description_en' => 'Monetary gift is given to celebrate employee child birth as family support.'
                    ],
                    [
                        'title_id' => 'Cuti Melahirkan',
                        'title_en' => 'Maternity Leave',
                        'icon' => '',
                        'percentage' => '',
                        'description_id' => 'Cuti melahirkan yang memadai untuk ibu yang baru melahirkan sesuai dengan peraturan ketenagakerjaan.',
                        'description_en' => 'Adequate maternity leave for new mothers according to labor regulations.'
                    ]
                ]
            ]
        ];

        Configuration::updateOrCreate(
            ['key' => 'employee_benefits_items'],
            [
                'value' => json_encode($employeeBenefitsItems),
                'type' => 'json',
                'group' => 'join_us',
                'description' => 'Employee benefits items displayed on the Employee Benefits page',
                'is_public' => true,
            ]
        );
    }
}