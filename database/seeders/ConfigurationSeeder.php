<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Tez Capital',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_GENERAL,
                'description' => 'Application name',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'Leading capital management solutions in Indonesia',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_GENERAL,
                'description' => 'Application description',
                'is_public' => true,
            ],
            [
                'key' => 'company_email',
                'value' => 'info@tezcapital.com',
                'type' => Configuration::TYPE_EMAIL,
                'group' => Configuration::GROUP_GENERAL,
                'description' => 'Main company email',
                'is_public' => true,
            ],
            [
                'key' => 'company_phone',
                'value' => '+62-21-1234-5678',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_GENERAL,
                'description' => 'Main company phone',
                'is_public' => true,
            ],
            [
                'key' => 'apply_now_link',
                'value' => 'https://docs.google.com/forms/d/e/1FAIpQLSdknDsyg8EluPjeYKWQhl14TGnpN6_hXGdGyTyk8bnalvxGGw/viewform',
                'type' => Configuration::TYPE_URL,
                'group' => Configuration::GROUP_GENERAL,
                'description' => 'Apply Now button link URL',
                'is_public' => true,
            ],

            // Branding Settings
            [
                'key' => 'company_logo',
                'value' => null,
                'type' => Configuration::TYPE_FILE,
                'group' => Configuration::GROUP_BRANDING,
                'description' => 'Company logo',
                'is_public' => true,
            ],
            [
                'key' => 'company_favicon',
                'value' => null,
                'type' => Configuration::TYPE_FILE,
                'group' => Configuration::GROUP_BRANDING,
                'description' => 'Website favicon',
                'is_public' => true,
            ],
            [
                'key' => 'primary_color',
                'value' => '#1E40AF',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_BRANDING,
                'description' => 'Primary brand color',
                'is_public' => true,
            ],
            [
                'key' => 'secondary_color',
                'value' => '#64748B',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_BRANDING,
                'description' => 'Secondary brand color',
                'is_public' => true,
            ],

            // Homepage Settings
            [
                'key' => 'hero_title',
                'value' => 'Solusi Finansial Terdepan untuk Masa Depan Anda',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_HOMEPAGE,
                'description' => 'Hero section title',
                'is_public' => true,
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'Tez Capital menyediakan layanan manajemen modal dan investasi terpercaya dengan pengalaman lebih dari 10 tahun',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_HOMEPAGE,
                'description' => 'Hero section subtitle',
                'is_public' => true,
            ],
            [
                'key' => 'hero_image',
                'value' => null,
                'type' => Configuration::TYPE_FILE,
                'group' => Configuration::GROUP_HOMEPAGE,
                'description' => 'Hero section background image',
                'is_public' => true,
            ],

            // Contact Settings
            [
                'key' => 'contact_address',
                'value' => 'Jl. Sudirman No. 123, Jakarta Pusat 10220, Indonesia',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_CONTACT,
                'description' => 'Company address',
                'is_public' => true,
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62-21-1234-5678',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_CONTACT,
                'description' => 'Contact phone number',
                'is_public' => true,
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@tezcapital.com',
                'type' => Configuration::TYPE_EMAIL,
                'group' => Configuration::GROUP_CONTACT,
                'description' => 'Contact email',
                'is_public' => true,
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+62812345678901',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_CONTACT,
                'description' => 'WhatsApp contact number',
                'is_public' => true,
            ],

            // Maintenance Settings
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => Configuration::TYPE_BOOLEAN,
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Enable maintenance mode',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Website sedang dalam maintenance. Mohon kembali lagi nanti.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Maintenance mode message',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_end_time',
                'value' => '',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_MAINTENANCE,
                'description' => 'Estimated maintenance end time',
                'is_public' => true,
            ],

            // Language Settings
            [
                'key' => 'default_language',
                'value' => 'id',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_LANGUAGE,
                'description' => 'Default website language',
                'is_public' => true,
            ],
            [
                'key' => 'available_languages',
                'value' => ['id', 'en'],
                'type' => Configuration::TYPE_JSON,
                'group' => Configuration::GROUP_LANGUAGE,
                'description' => 'Available website languages',
                'is_public' => true,
            ],

            // About Settings
            [
                'key' => 'company_history',
                'value' => 'Tez Capital didirikan pada tahun 2013 dengan visi menjadi perusahaan manajemen modal terdepan di Indonesia.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_ABOUT,
                'description' => 'Company history description',
                'is_public' => true,
            ],
            [
                'key' => 'company_vision',
                'value' => 'Menjadi partner terpercaya dalam solusi finansial dan investasi untuk mencapai kesuksesan bersama.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_ABOUT,
                'description' => 'Company vision',
                'is_public' => true,
            ],
            [
                'key' => 'company_mission',
                'value' => 'Memberikan layanan finansial berkualitas tinggi dengan inovasi dan integritas untuk membantu klien mencapai tujuan finansial mereka.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_ABOUT,
                'description' => 'Company mission',
                'is_public' => true,
            ],

            // Credit Settings
            [
                'key' => 'min_dp_percentage',
                'value' => 10,
                'type' => Configuration::TYPE_INTEGER,
                'group' => Configuration::GROUP_CREDIT,
                'description' => 'Minimum down payment percentage',
                'is_public' => true,
            ],
            [
                'key' => 'max_tenor_months',
                'value' => 35,
                'type' => Configuration::TYPE_INTEGER,
                'group' => Configuration::GROUP_CREDIT,
                'description' => 'Maximum tenor in months',
                'is_public' => true,
            ],
            [
                'key' => 'interest_rate',
                'value' => 12.5,
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_CREDIT,
                'description' => 'Base interest rate percentage',
                'is_public' => true,
            ],
        ];

        foreach ($configurations as $config) {
            Configuration::create($config);
        }

        $this->command->info('Configurations created successfully!');
    }
}