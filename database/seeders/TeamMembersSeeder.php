<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;

class TeamMembersSeeder extends Seeder
{
    public function run(): void
    {
        $teamMembers = [
            [
                'type' => 'team-member',
                'title_id' => 'John Doe',
                'title_en' => 'John Doe',
                'department_id' => 'Leadership',
                'department_en' => 'Leadership',
                'category' => 'leadership',
                'featured_image' => '/img/team/ceo.jpg',
                'is_published' => true,
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 1,
                'slug' => 'john-doe',
            ],
            [
                'type' => 'team-member',
                'title_id' => 'Jane Smith',
                'title_en' => 'Jane Smith',
                'department_id' => 'Finance',
                'department_en' => 'Finance',
                'category' => 'management',
                'featured_image' => '/img/team/cfo.jpg',
                'is_published' => true,
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 2,
                'slug' => 'jane-smith',
            ],
            [
                'type' => 'team-member',
                'title_id' => 'Michael Johnson',
                'title_en' => 'Michael Johnson',
                'department_id' => 'Technology',
                'department_en' => 'Technology',
                'category' => 'technology',
                'featured_image' => '/img/team/cto.jpg',
                'is_published' => true,
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 3,
                'slug' => 'michael-johnson',
            ],
            [
                'type' => 'team-member',
                'title_id' => 'Sarah Wilson',
                'title_en' => 'Sarah Wilson',
                'department_id' => 'Marketing',
                'department_en' => 'Marketing',
                'category' => 'marketing',
                'featured_image' => '/img/team/marketing.jpg',
                'is_published' => true,
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 4,
                'slug' => 'sarah-wilson',
            ],
            [
                'type' => 'team-member',
                'title_id' => 'David Brown',
                'title_en' => 'David Brown',
                'department_id' => 'Operations',
                'department_en' => 'Operations',
                'category' => 'operations',
                'featured_image' => '/img/team/operations.jpg',
                'is_published' => true,
                'is_featured' => true,
                'status' => 'published',
                'sort_order' => 5,
                'slug' => 'david-brown',
            ],
        ];

        foreach ($teamMembers as $member) {
            Content::updateOrCreate(
                ['slug' => $member['slug']],
                $member
            );
        }

        $this->command->info('Team members seeded successfully!');
    }
}
