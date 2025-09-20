<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class BannerConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $bannerConfigs = [
            // About Page Banner
            [
                'key' => 'banner_about_title_id',
                'value' => 'Tentang Kami',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'About page banner title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_about_title_en',
                'value' => 'About Us',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'About page banner title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_about_description_id',
                'value' => 'Pelajari lebih lanjut tentang TEZ Capital & Finance',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'About page banner description (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_about_description_en',
                'value' => 'Learn more about TEZ Capital & Finance',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'About page banner description (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_about_image',
                'value' => '',
                'type' => 'file',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'About page banner background image',
                'is_public' => true,
            ],

            // Services Page Banner
            [
                'key' => 'banner_services_title_id',
                'value' => 'Layanan Kami',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Services page banner title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_services_title_en',
                'value' => 'Our Services',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Services page banner title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_services_description_id',
                'value' => 'Solusi pembiayaan terpercaya untuk kebutuhan Anda',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Services page banner description (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_services_description_en',
                'value' => 'Trusted financing solutions for your needs',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Services page banner description (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_services_image',
                'value' => '',
                'type' => 'file',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Services page banner background image',
                'is_public' => true,
            ],

            // News Page Banner
            [
                'key' => 'banner_news_title_id',
                'value' => 'Berita & Acara',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'News page banner title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_news_title_en',
                'value' => 'News & Events',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'News page banner title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_news_description_id',
                'value' => 'Temukan berita terbaru mengenai TEZ Capital di sini',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'News page banner description (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_news_description_en',
                'value' => 'Find the latest news about TEZ Capital here',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'News page banner description (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_news_image',
                'value' => '',
                'type' => 'file',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'News page banner background image',
                'is_public' => true,
            ],

            // Contact Page Banner
            [
                'key' => 'banner_contact_title_id',
                'value' => 'Hubungi Kami',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Contact page banner title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_contact_title_en',
                'value' => 'Contact Us',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Contact page banner title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_contact_description_id',
                'value' => 'Siap membantu dengan layanan terbaik untuk Anda',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Contact page banner description (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_contact_description_en',
                'value' => 'Ready to help with the best service for you',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Contact page banner description (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_contact_image',
                'value' => '',
                'type' => 'file',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Contact page banner background image',
                'is_public' => true,
            ],

            // Corporate Page Banner
            [
                'key' => 'banner_corporate_title_id',
                'value' => 'Korporat',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Corporate page banner title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_corporate_title_en',
                'value' => 'Corporate',
                'type' => 'string',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Corporate page banner title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_corporate_description_id',
                'value' => 'Informasi korporat dan laporan keuangan terkini',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Corporate page banner description (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_corporate_description_en',
                'value' => 'Corporate information and latest financial reports',
                'type' => 'text',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Corporate page banner description (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_corporate_image',
                'value' => '',
                'type' => 'file',
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Corporate page banner background image',
                'is_public' => true,
            ],
        ];

        foreach ($bannerConfigs as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}