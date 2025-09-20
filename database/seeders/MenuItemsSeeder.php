<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing menu items to prevent duplicates
        \App\Models\MenuItem::truncate();

        $menuItems = [
            // Dashboard
            [
                'id' => 1,
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'position' => 1,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Content Parent
            [
                'id' => 2,
                'title' => 'Content',
                'href' => null,
                'icon' => 'FileText',
                'position' => 3,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // System Parent
            [
                'id' => 3,
                'title' => 'System',
                'href' => null,
                'icon' => 'Settings',
                'position' => 5,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Content sub-menus
            [
                'id' => 4,
                'title' => 'About',
                'href' => '/content/about',
                'icon' => 'FileText',
                'position' => 1,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'title' => 'News & Events',
                'href' => '/content/news-events',
                'icon' => 'Calendar',
                'position' => 2,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'title' => 'Services',
                'href' => '/content/services',
                'icon' => 'Briefcase',
                'position' => 3,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'title' => 'Partners',
                'href' => '/content/partners',
                'icon' => 'Users',
                'position' => 4,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // System sub-menus
            [
                'id' => 8,
                'title' => 'Users',
                'href' => '/system/users',
                'icon' => 'Users',
                'position' => 1,
                'parent_id' => 3,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 9,
                'title' => 'Roles & Permissions',
                'href' => '/system/roles',
                'icon' => 'Shield',
                'position' => 2,
                'parent_id' => 3,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 10,
                'title' => 'Configurations',
                'href' => '/system/configurations',
                'icon' => 'Settings',
                'position' => 3,
                'parent_id' => 3,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 11,
                'title' => 'Menu Manager',
                'href' => '/system/menu',
                'icon' => 'Menu',
                'position' => 4,
                'parent_id' => 3,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
            [
                'id' => 12,
                'title' => 'Audit Log',
                'href' => '/system/audit-log',
                'icon' => 'Activity',
                'position' => 5,
                'parent_id' => 3,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Separators
            [
                'id' => 13,
                'title' => '',
                'href' => null,
                'icon' => null,
                'position' => 2,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => true,
                'is_active' => true,
            ],
            [
                'id' => 14,
                'title' => '',
                'href' => null,
                'icon' => null,
                'position' => 4,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => true,
                'is_active' => true,
            ],
        ];

        // Insert all menu items
        foreach ($menuItems as $item) {
            \App\Models\MenuItem::create($item);
        }
    }
}
