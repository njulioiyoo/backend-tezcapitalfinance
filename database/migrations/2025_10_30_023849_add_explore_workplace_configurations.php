<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $configurations = [
            // Working Environment Card
            [
                'key' => 'workplace_working_environment_title_id',
                'value' => json_encode('Lingkungan Kerja'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Judul kartu Working Environment (Bahasa Indonesia)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_working_environment_title_en',
                'value' => json_encode('Working Environment'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Judul kartu Working Environment (English)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_working_environment_description_id',
                'value' => json_encode('Tempatkan deskripsi mengenai lingkungan kerja di TEZ Capital & Finance. Jelaskan bagaimana suasana kerja yang kondusif dan mendukung produktivitas karyawan.'),
                'type' => 'textarea',
                'group' => 'join_us',
                'description' => 'Deskripsi kartu Working Environment (Bahasa Indonesia)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_working_environment_description_en',
                'value' => json_encode('Place description about the working environment at TEZ Capital & Finance. Explain how the work atmosphere is conducive and supports employee productivity.'),
                'type' => 'textarea',
                'group' => 'join_us',
                'description' => 'Deskripsi kartu Working Environment (English)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_working_environment_image',
                'value' => json_encode('/img/workplace/working-environment.jpg'),
                'type' => 'file',
                'group' => 'join_us',
                'description' => 'Gambar untuk kartu Working Environment',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_working_environment_slug',
                'value' => json_encode('#working-environment'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Slug/link untuk kartu Working Environment',
                'is_public' => true,
            ],

            // Employee Benefits Card
            [
                'key' => 'workplace_employee_benefits_title_id',
                'value' => json_encode('Benefit Karyawan'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Judul kartu Employee Benefits (Bahasa Indonesia)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_employee_benefits_title_en',
                'value' => json_encode('Employee Benefits'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Judul kartu Employee Benefits (English)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_employee_benefits_description_id',
                'value' => json_encode('Tempatkan deskripsi mengenai benefit dan fasilitas yang diberikan kepada karyawan TEZ Capital & Finance. Jelaskan paket kompensasi dan tunjangan yang menarik.'),
                'type' => 'textarea',
                'group' => 'join_us',
                'description' => 'Deskripsi kartu Employee Benefits (Bahasa Indonesia)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_employee_benefits_description_en',
                'value' => json_encode('Place description about the benefits and facilities provided to TEZ Capital & Finance employees. Explain the attractive compensation and benefits package.'),
                'type' => 'textarea',
                'group' => 'join_us',
                'description' => 'Deskripsi kartu Employee Benefits (English)',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_employee_benefits_image',
                'value' => json_encode('/img/workplace/employee-benefits.jpg'),
                'type' => 'file',
                'group' => 'join_us',
                'description' => 'Gambar untuk kartu Employee Benefits',
                'is_public' => true,
            ],
            [
                'key' => 'workplace_employee_benefits_slug',
                'value' => json_encode('#employee-benefits'),
                'type' => 'text',
                'group' => 'join_us',
                'description' => 'Slug/link untuk kartu Employee Benefits',
                'is_public' => true,
            ],
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'workplace_working_environment_title_id',
            'workplace_working_environment_title_en',
            'workplace_working_environment_description_id',
            'workplace_working_environment_description_en',
            'workplace_working_environment_image',
            'workplace_working_environment_slug',
            'workplace_employee_benefits_title_id',
            'workplace_employee_benefits_title_en',
            'workplace_employee_benefits_description_id',
            'workplace_employee_benefits_description_en',
            'workplace_employee_benefits_image',
            'workplace_employee_benefits_slug',
        ];

        Configuration::whereIn('key', $keys)->delete();
    }
};