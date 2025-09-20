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
        $menuItems = [
            // Dashboard
            [
                'title' => 'Dashboard',
                'href' => '/dashboard',
                'icon' => 'LayoutGrid',
                'position' => 1,
                'parent_id' => null,
                'is_active' => true,
            ],

            // Content Management
            [
                'title' => 'Content Management',
                'href' => null,
                'icon' => 'FileText',
                'position' => 2,
                'parent_id' => null,
                'is_active' => true,
            ],

            // System Management
            [
                'title' => 'System Management',
                'href' => null,
                'icon' => 'Settings',
                'position' => 3,
                'parent_id' => null,
                'is_active' => true,
            ],
        ];

        // Insert parent menu items first
        foreach ($menuItems as $item) {
            \App\Models\MenuItem::create($item);
        }

        // Get parent IDs
        $contentParent = \App\Models\MenuItem::where('title', 'Content Management')->first();
        $systemParent = \App\Models\MenuItem::where('title', 'System Management')->first();

        // Content sub-menus
        $contentMenus = [
            [
                'title' => 'About',
                'href' => '/content/about',
                'icon' => 'FileText',
                'position' => 1,
                'parent_id' => $contentParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'News & Events',
                'href' => '/system/news-events',
                'icon' => 'Calendar',
                'position' => 2,
                'parent_id' => $contentParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Services',
                'href' => '/content/services',
                'icon' => 'Briefcase',
                'position' => 3,
                'parent_id' => $contentParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Partners',
                'href' => '/content/partners',
                'icon' => 'Users',
                'position' => 4,
                'parent_id' => $contentParent->id,
                'is_active' => true,
            ],
        ];

        // System sub-menus
        $systemMenus = [
            [
                'title' => 'Users',
                'href' => '/system/users',
                'icon' => 'Users',
                'position' => 1,
                'parent_id' => $systemParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Roles & Permissions',
                'href' => '/system/roles',
                'icon' => 'Shield',
                'position' => 2,
                'parent_id' => $systemParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Configurations',
                'href' => '/system/configurations',
                'icon' => 'Settings',
                'position' => 3,
                'parent_id' => $systemParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Menu Manager',
                'href' => '/system/menu',
                'icon' => 'Menu',
                'position' => 4,
                'parent_id' => $systemParent->id,
                'is_active' => true,
            ],
            [
                'title' => 'Audit Log',
                'href' => '/system/audit-log',
                'icon' => 'Activity',
                'position' => 5,
                'parent_id' => $systemParent->id,
                'is_active' => true,
            ],
        ];

        // Insert sub-menus
        foreach ($contentMenus as $item) {
            \App\Models\MenuItem::create($item);
        }

        foreach ($systemMenus as $item) {
            \App\Models\MenuItem::create($item);
        }
    }
}
