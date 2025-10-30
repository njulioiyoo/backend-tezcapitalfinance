<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name_id' => 'Finance',
                'name_en' => 'Finance',
                'slug' => 'finance',
                'description_id' => 'Departemen Keuangan mengelola semua aspek keuangan perusahaan',
                'description_en' => 'Finance department manages all financial aspects of the company',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_id' => 'People & Operation',
                'name_en' => 'People & Operation',
                'slug' => 'people-operation',
                'description_id' => 'Departemen People & Operation mengelola sumber daya manusia dan operasional',
                'description_en' => 'People & Operation department manages human resources and operations',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_id' => 'Technology',
                'name_en' => 'Technology',
                'slug' => 'technology',
                'description_id' => 'Departemen Teknologi mengelola infrastruktur dan pengembangan sistem',
                'description_en' => 'Technology department manages infrastructure and system development',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name_id' => 'Marketing',
                'name_en' => 'Marketing',
                'slug' => 'marketing',
                'description_id' => 'Departemen Marketing mengelola strategi pemasaran dan branding',
                'description_en' => 'Marketing department manages marketing strategy and branding',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name_id' => 'Sales',
                'name_en' => 'Sales',
                'slug' => 'sales',
                'description_id' => 'Departemen Sales mengelola penjualan dan hubungan dengan pelanggan',
                'description_en' => 'Sales department manages sales and customer relationships',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['slug' => $department['slug']],
                $department
            );
        }
    }
}
