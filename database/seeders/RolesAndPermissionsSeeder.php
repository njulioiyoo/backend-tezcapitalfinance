<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for all modules
        $modules = [
            'users' => 'User Management',
            'roles' => 'Roles & Permissions',
            'permissions' => 'Permissions Management',
            'configurations' => 'System Configurations',
            'content' => 'Content Management',
            'news-events' => 'News & Events',
            'services' => 'Services',
            'partners' => 'Partners',
            'about' => 'About Content',
            'homepage' => 'Homepage Settings',
            'menu' => 'Menu Management',
            'audit-log' => 'Audit Logs',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        $permissions = [];

        // Create permissions for each module and action
        foreach ($modules as $module => $description) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
                $permissions[$permissionName] = $permission;
            }
        }

        // Create additional specific permissions
        $additionalPermissions = [
            'configurations.bulk-update' => 'Bulk Update Configurations',
            'content.bulk-action' => 'Bulk Actions on Content',
            'content.upload-image' => 'Upload Images for Content',
            'users.toggle-status' => 'Toggle User Status',
            'menu.reorder' => 'Reorder Menu Items',
        ];

        foreach ($additionalPermissions as $name => $description) {
            $permissions[$name] = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $editorRole = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web'
        ]);

        // Assign permissions to super-admin (all permissions)
        $superAdminRole->syncPermissions(Permission::all());

        // Assign permissions to admin (all except roles and permissions management)
        $adminPermissions = collect($permissions)->filter(function ($permission, $name) {
            return !str_starts_with($name, 'roles.') && !str_starts_with($name, 'permissions.');
        })->values();
        $adminRole->syncPermissions($adminPermissions);

        // Assign permissions to editor (only content-related permissions)
        $editorPermissions = collect($permissions)->filter(function ($permission, $name) {
            return str_starts_with($name, 'content.') || 
                   str_starts_with($name, 'news-events.') ||
                   str_starts_with($name, 'services.') ||
                   str_starts_with($name, 'partners.') ||
                   str_starts_with($name, 'about.') ||
                   str_starts_with($name, 'homepage.');
        })->values();
        $editorRole->syncPermissions($editorPermissions);

        // Create default users for each role
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@example.com'
        ], [
            'name' => 'Super Administrator',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $editor = User::firstOrCreate([
            'email' => 'editor@example.com'
        ], [
            'name' => 'Editor',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $editor->assignRole('editor');

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin: superadmin@example.com / password');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Editor: editor@example.com / password');
    }
}