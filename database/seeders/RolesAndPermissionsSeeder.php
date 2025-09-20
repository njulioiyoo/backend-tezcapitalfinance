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
        // Clear existing data to prevent duplicates
        \DB::table('model_has_permissions')->delete();
        \DB::table('model_has_roles')->delete();
        \DB::table('role_has_permissions')->delete();
        Permission::query()->delete();
        Role::query()->delete();
        User::query()->delete();

        // Create all permissions based on current database state
        $allPermissions = [
            'about.create',
            'about.delete', 
            'about.edit',
            'about.view',
            'audit-log.create',
            'audit-log.delete',
            'audit-log.edit',
            'audit-log.view',
            'configurations.bulk-update',
            'configurations.create',
            'configurations.delete',
            'configurations.edit',
            'configurations.view',
            'content.bulk-action',
            'content.create',
            'content.delete',
            'content.edit',
            'content.news.create',
            'content.news.delete',
            'content.news.edit',
            'content.news.view',
            'content.partners.create',
            'content.partners.delete',
            'content.partners.edit',
            'content.partners.view',
            'content.upload-image',
            'content.view',
            'homepage.create',
            'homepage.delete',
            'homepage.edit',
            'homepage.view',
            'menu.create',
            'menu.delete',
            'menu.edit',
            'menu.reorder',
            'menu.view',
            'news-events.create',
            'news-events.delete',
            'news-events.edit',
            'news-events.view',
            'partners.create',
            'partners.delete',
            'partners.edit',
            'partners.view',
            'permissions.create',
            'permissions.delete',
            'permissions.edit',
            'permissions.view',
            'roles.create',
            'roles.delete',
            'roles.edit',
            'roles.view',
            'services.create',
            'services.delete',
            'services.edit',
            'services.view',
            'system.audit.export',
            'system.audit.view',
            'system.configurations.edit',
            'system.configurations.view',
            'system.menu.create',
            'system.menu.delete',
            'system.menu.edit',
            'system.menu.view',
            'system.roles.create',
            'system.roles.delete',
            'system.roles.edit',
            'system.roles.view',
            'system.users.create',
            'system.users.delete',
            'system.users.edit',
            'system.users.view',
            'users.create',
            'users.delete',
            'users.edit',
            'users.toggle-status',
            'users.view',
        ];

        $permissions = [];
        foreach ($allPermissions as $permissionName) {
            $permissions[$permissionName] = Permission::create([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);
        }

        // Create roles
        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $editorRole = Role::create([
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

        // Create users with correct emails based on current database
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@tez-capital.com',
            'password' => bcrypt('password'),
            'email_verified_at' => null,
        ]);
        $superAdmin->assignRole('super-admin');

        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@tez-capital.com',
            'password' => bcrypt('password'),
            'email_verified_at' => null,
        ]);
        $admin->assignRole('admin');

        $editor = User::create([
            'name' => 'Content Editor',
            'email' => 'editor@tez-capital.com',
            'password' => bcrypt('password'),
            'email_verified_at' => null,
        ]);
        $editor->assignRole('editor');

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin: superadmin@tez-capital.com / password');
        $this->command->info('Admin: admin@tez-capital.com / password');
        $this->command->info('Editor: editor@tez-capital.com / password');
    }
}