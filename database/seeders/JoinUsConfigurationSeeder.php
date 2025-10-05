<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class JoinUsConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            [
                'key' => 'hero_title',
                'value' => 'Bagian dari TEZ Capital',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Hero title for Join Us page',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_title',
                'value' => 'Pesan dari CEO',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message section title',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_content',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut a gravida metus. Pellentesque interdum tristique lacus, vitae ultrices sapien ultrices hendrerit. Quisque diam lorem, vestibulum nec risus sit amet, mollis dapibus nunc. Nunc pharetra eget odio quis facilisis. Maecenas blandit, magna vel commodo blandit, nibh purus hendrerit quam, nec interdum leo arcu ac ipsum.

Fusce ullamcorper ornare odio, id rutrum diam dignissim semper. Fusce nec massa at nunc vulputate feugiat et vitae lectus. Sed interdum hendrerit mi, sit amet tincidunt quam ultrices quis.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message content',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_image',
                'value' => '/img/profile/1.png',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO image URL',
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
}