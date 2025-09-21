<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing menu items
        MenuItem::truncate();

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

            // System Management
            [
                'id' => 2,
                'title' => 'System Management',
                'href' => null,
                'icon' => 'Settings',
                'position' => 2,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Users under System Management
            [
                'id' => 3,
                'title' => 'Users',
                'href' => '/users',
                'icon' => 'Users',
                'position' => 1,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Roles & Permissions under System Management
            [
                'id' => 4,
                'title' => 'Roles & Permissions',
                'href' => '/users/roles',
                'icon' => 'Shield',
                'position' => 2,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Menus under System Management
            [
                'id' => 5,
                'title' => 'Menus',
                'href' => '/menu-manager',
                'icon' => 'LayoutGrid',
                'position' => 3,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Audit Log under System Management
            [
                'id' => 6,
                'title' => 'Audit Log',
                'href' => '/audit-log',
                'icon' => 'FileText',
                'position' => 4,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // System Configuration under System Management
            [
                'id' => 7,
                'title' => 'System Configuration',
                'href' => '/system/config',
                'icon' => 'Settings',
                'position' => 5,
                'parent_id' => 2,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Content Management
            [
                'id' => 8,
                'title' => 'Content Management',
                'href' => null,
                'icon' => 'FileText',
                'position' => 3,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // News under Content Management
            [
                'id' => 9,
                'title' => 'News',
                'href' => '/content/news',
                'icon' => 'Newspaper',
                'position' => 1,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Events under Content Management
            [
                'id' => 10,
                'title' => 'Events',
                'href' => '/content/events',
                'icon' => 'Calendar',
                'position' => 2,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Services under Content Management
            [
                'id' => 11,
                'title' => 'Services',
                'href' => '/content/services',
                'icon' => 'Briefcase',
                'position' => 3,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Partners under Content Management
            [
                'id' => 12,
                'title' => 'Partners',
                'href' => '/content/partners',
                'icon' => 'Handshake',
                'position' => 4,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Motor Management
            [
                'id' => 13,
                'title' => 'Motor Management',
                'href' => '/motors',
                'icon' => 'Car',
                'position' => 4,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Reports
            [
                'id' => 14,
                'title' => 'Reports',
                'href' => null,
                'icon' => 'FileText',
                'position' => 5,
                'parent_id' => null,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Laporan Keuangan under Reports
            [
                'id' => 15,
                'title' => 'Laporan Keuangan',
                'href' => '/reports?type=laporan-keuangan',
                'icon' => 'CreditCard',
                'position' => 1,
                'parent_id' => 14,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Laporan Tahunan under Reports
            [
                'id' => 16,
                'title' => 'Laporan Tahunan',
                'href' => '/reports?type=laporan-tahunan',
                'icon' => 'Calendar',
                'position' => 2,
                'parent_id' => 14,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],

            // Laporan Pengaduan under Reports
            [
                'id' => 17,
                'title' => 'Laporan Pengaduan',
                'href' => '/reports?type=laporan-pengaduan',
                'icon' => 'MessageSquare',
                'position' => 3,
                'parent_id' => 14,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        $this->command->info('Menu items created successfully!');
    }
}