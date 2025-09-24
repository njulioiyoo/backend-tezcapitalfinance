<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class OjkConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $ojkConfigurations = [
            [
                'key' => 'ojk_title',
                'value' => 'Berizin dan Diawasi oleh Otoritas Jasa Keuangan',
                'type' => 'text',
                'group' => Configuration::GROUP_OJK,
                'description' => 'Title text displayed in OJK section of footer',
                'is_public' => true,
            ],
            [
                'key' => 'ojk_description',
                'value' => '© 2025 PT TEZ Capital and Finance. All Rights Reserved',
                'type' => 'text',
                'group' => Configuration::GROUP_OJK,
                'description' => 'Copyright/description text displayed in OJK section of footer',
                'is_public' => true,
            ],
            [
                'key' => 'ojk_images',
                'value' => json_encode([
                    [
                        'url' => '/img/ojk.png',
                        'alt' => 'OJK Certification Logo 1',
                        'isNew' => false
                    ],
                    [
                        'url' => '/img/ojk.png', 
                        'alt' => 'OJK Certification Logo 2',
                        'isNew' => false
                    ],
                    [
                        'url' => '/img/ojk.png',
                        'alt' => 'OJK Certification Logo 3',
                        'isNew' => false
                    ],
                    [
                        'url' => '/img/ojk.png',
                        'alt' => 'OJK Certification Logo 4', 
                        'isNew' => false
                    ],
                ]),
                'type' => 'json',
                'group' => Configuration::GROUP_OJK,
                'description' => 'OJK certification and regulatory logos displayed in footer',
                'is_public' => true,
            ],
        ];

        foreach ($ojkConfigurations as $config) {
            Configuration::updateOrCreate(
                [
                    'key' => $config['key'],
                    'group' => $config['group'],
                ],
                $config
            );
        }
    }
}