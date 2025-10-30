<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name_id' => 'Jakarta',
                'name_en' => 'Jakarta',
                'slug' => 'jakarta',
                'description_id' => 'Kantor pusat di Jakarta',
                'description_en' => 'Head office in Jakarta',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_id' => 'Surabaya',
                'name_en' => 'Surabaya',
                'slug' => 'surabaya',
                'description_id' => 'Kantor cabang di Surabaya',
                'description_en' => 'Branch office in Surabaya',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'country' => 'Indonesia',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_id' => 'Bandung',
                'name_en' => 'Bandung',
                'slug' => 'bandung',
                'description_id' => 'Kantor cabang di Bandung',
                'description_en' => 'Branch office in Bandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['slug' => $location['slug']],
                $location
            );
        }
    }
}
