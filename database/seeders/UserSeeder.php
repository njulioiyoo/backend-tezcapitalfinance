<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@tezcapital.com',
            'password' => Hash::make('password123'),
            'phone' => '+62812345678901',
            'address' => 'Jakarta, Indonesia',
            'status' => true,
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@tezcapital.com',
            'password' => Hash::make('password123'),
            'phone' => '+62812345678902',
            'address' => 'Jakarta, Indonesia',
            'status' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Editor
        $editor = User::create([
            'name' => 'Content Editor',
            'email' => 'editor@tezcapital.com',
            'password' => Hash::make('password123'),
            'phone' => '+62812345678903',
            'address' => 'Jakarta, Indonesia',
            'status' => true,
            'email_verified_at' => now(),
        ]);
        $editor->assignRole('editor');

        // Create Viewer
        $viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@tezcapital.com',
            'password' => Hash::make('password123'),
            'phone' => '+62812345678904',
            'address' => 'Jakarta, Indonesia',
            'status' => true,
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');

        $this->command->info('Users created successfully!');
        $this->command->info('Super Admin: superadmin@tezcapital.com / password123');
        $this->command->info('Admin: admin@tezcapital.com / password123');
        $this->command->info('Editor: editor@tezcapital.com / password123');
        $this->command->info('Viewer: viewer@tezcapital.com / password123');
    }
}