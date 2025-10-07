<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class JoinUsBannerConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            [
                'key' => 'banner_join_us_title_id',
                'value' => 'Bergabunglah dengan Tim Kami',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Banner title for Join Us page (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_join_us_title_en',
                'value' => 'Join Our Team',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Banner title for Join Us page (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_join_us_description_id',
                'value' => 'Temukan peluang karir yang tepat untuk Anda di TEZ Capital. Bergabunglah dengan tim profesional kami dan wujudkan potensi terbaik Anda.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Banner description for Join Us page (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_join_us_description_en',
                'value' => 'Discover the right career opportunities for you at TEZ Capital. Join our professional team and realize your full potential.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Banner description for Join Us page (English)',
                'is_public' => true,
            ],
            [
                'key' => 'banner_join_us_image',
                'value' => '/img/banners/join-us-banner.jpg',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_BANNERS,
                'description' => 'Banner image for Join Us page',
                'is_public' => true,
            ],
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        $this->command->info('Join Us banner configurations seeded successfully!');
    }
}
