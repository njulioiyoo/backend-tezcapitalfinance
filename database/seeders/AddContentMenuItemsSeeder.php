<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class AddContentMenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Content Management parent menu (id = 8)
        $contentManagement = MenuItem::where('id', 8)->first();
        
        if (!$contentManagement) {
            $this->command->error('Content Management menu not found. Please run MenuItemSeeder first.');
            return;
        }

        // Check if careers menu already exists
        $existingCareers = MenuItem::where('href', '/content/careers')->first();
        if (!$existingCareers) {
            MenuItem::create([
                'title' => 'Careers',
                'href' => '/content/careers',
                'icon' => 'Briefcase',
                'position' => 5,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ]);
            $this->command->info('Careers menu item created.');
        }

        // Check if team members menu already exists
        $existingTeamMembers = MenuItem::where('href', '/content/team-members')->first();
        if (!$existingTeamMembers) {
            MenuItem::create([
                'title' => 'Team Members',
                'href' => '/content/team-members',
                'icon' => 'Users',
                'position' => 6,
                'parent_id' => 8,
                'badge' => null,
                'disabled' => false,
                'is_separator' => false,
                'is_active' => true,
            ]);
            $this->command->info('Team Members menu item created.');
        }

        $this->command->info('Content menu items added successfully!');
    }
}
