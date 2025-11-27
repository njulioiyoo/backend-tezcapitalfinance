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
                'key' => 'hero_title_id',
                'value' => 'Bagian dari TEZ Capital',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Hero title for Join Us page (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'hero_title_en',
                'value' => 'Be Part of TEZ Capital',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Hero title for Join Us page (English)',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_title_id',
                'value' => 'Pesan dari CEO',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message section title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_title_en',
                'value' => 'Message from CEO',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message section title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_content_id',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut a gravida metus. Pellentesque interdum tristique lacus, vitae ultrices sapien ultrices hendrerit. Quisque diam lorem, vestibulum nec risus sit amet, mollis dapibus nunc. Nunc pharetra eget odio quis facilisis. Maecenas blandit, magna vel commodo blandit, nibh purus hendrerit quam, nec interdum leo arcu ac ipsum.

Fusce ullamcorper ornare odio, id rutrum diam dignissim semper. Fusce nec massa at nunc vulputate feugiat et vitae lectus. Sed interdum hendrerit mi, sit amet tincidunt quam ultrices quis.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message content (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'ceo_message_content_en',
                'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. We are committed to providing innovative financial solutions that empower businesses to grow and succeed. Our experienced team understands the unique challenges facing modern enterprises and we are here to support your journey towards financial success.

Through our comprehensive range of services, we aim to be your trusted partner in achieving your business goals. Together, we can build a stronger financial future for your organization.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'CEO message content (English)',
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
            [
                'key' => 'our_business_title_id',
                'value' => 'Bisnis Kami',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Our Business section title (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'our_business_title_en',
                'value' => 'Our Business',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Our Business section title (English)',
                'is_public' => true,
            ],
            [
                'key' => 'our_business_content_id',
                'value' => 'Pinjaman Beragunan Properti adalah layanan keuangan yang menyediakan dana dengan menggunakan properti nasabah—seperti tanah atau bangunan—sebagai jaminan. Pinjaman ini dapat digunakan secara fleksibel untuk berbagai keperluan, termasuk modal kerja untuk ekspansi bisnis, biaya hidup seperti medis atau pendidikan, serta investasi atau refinancing.\n\nDi TEZ, kami sangat mementingkan pemahaman mendalam terhadap situasi setiap nasabah, memberikan penilaian yang cepat, dan menawarkan rencana optimal untuk memenuhi kebutuhan pendanaan mereka secara efisien. Dengan memanfaatkan nilai properti, kami mendukung nasabah untuk melangkah maju—inilah inti dari bisnis Pinjaman Beragunan Properti kami.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Our Business section content (Indonesian)',
                'is_public' => true,
            ],
            [
                'key' => 'our_business_content_en',
                'value' => 'A real estate secured loan is a financial service that provides funds using the customer\'s real estate—such as land or buildings—as collateral. These loans can be used flexibly for various purposes, including working capital for business expansion, living expenses such as medical or educational costs, as well as investment or refinancing.\n\nAt TEZ, we place great importance on carefully understanding each customer\'s situation, offering swift assessments, and proposing optimal plans to meet their funding needs efficiently. By leveraging the value of real estate, we support our customers in taking their next step forward—this is the essence of our real estate secured loan business.',
                'type' => Configuration::TYPE_TEXT,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Our Business section content (English)',
                'is_public' => true,
            ],
            [
                'key' => 'our_business_image',
                'value' => '/img/Sorotan.svg',
                'type' => Configuration::TYPE_STRING,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Our Business image URL',
                'is_public' => true,
            ],
            [
                'key' => 'button_join_us_enabled',
                'value' => true,
                'type' => Configuration::TYPE_BOOLEAN,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Enable/disable Join Us button in header',
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