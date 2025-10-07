<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Role Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            
            // Permission Management
            'view_permissions',
            'assign_permissions',
            
            // Content Management
            'view_contents',
            'create_contents',
            'edit_contents',
            'delete_contents',
            'publish_contents',
            
            // News Management
            'view_news',
            'create_news',
            'edit_news',
            'delete_news',
            'publish_news',
            
            // Events Management
            'view_events',
            'create_events',
            'edit_events',
            'delete_events',
            'publish_events',
            
            // Careers Management
            'view_careers',
            'create_careers',
            'edit_careers',
            'delete_careers',
            'publish_careers',
            
            // Team Members Management
            'view_team-members',
            'create_team-members',
            'edit_team-members',
            'delete_team-members',
            'publish_team-members',
            
            // Motor Management
            'view_motors',
            'create_motors',
            'edit_motors',
            'delete_motors',
            
            // Configuration Management
            'view_configurations',
            'edit_configurations',
            
            // Menu Management
            'view_menus',
            'create_menus',
            'edit_menus',
            'delete_menus',
            
            // Audit Log
            'view_audit_logs',
            
            // Reports
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',
            
            // Dashboard
            'view_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin Role
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin Role
        $adminRole = Role::create(['name' => 'admin']);
        $adminPermissions = [
            'view_dashboard',
            'view_users', 'create_users', 'edit_users',
            'view_contents', 'create_contents', 'edit_contents', 'delete_contents', 'publish_contents',
            'view_news', 'create_news', 'edit_news', 'delete_news', 'publish_news',
            'view_events', 'create_events', 'edit_events', 'delete_events', 'publish_events',
            'view_careers', 'create_careers', 'edit_careers', 'delete_careers', 'publish_careers',
            'view_team-members', 'create_team-members', 'edit_team-members', 'delete_team-members', 'publish_team-members',
            'view_motors', 'create_motors', 'edit_motors', 'delete_motors',
            'view_configurations', 'edit_configurations',
            'view_menus', 'create_menus', 'edit_menus', 'delete_menus',
            'view_audit_logs',
            'reports.view', 'reports.create', 'reports.edit', 'reports.delete',
        ];
        $adminRole->givePermissionTo($adminPermissions);

        // Editor Role
        $editorRole = Role::create(['name' => 'editor']);
        $editorPermissions = [
            'view_dashboard',
            'view_contents', 'create_contents', 'edit_contents',
            'view_news', 'create_news', 'edit_news',
            'view_events', 'create_events', 'edit_events',
            'view_careers', 'create_careers', 'edit_careers',
            'view_team-members', 'create_team-members', 'edit_team-members',
            'view_motors',
        ];
        $editorRole->givePermissionTo($editorPermissions);

        // Viewer Role
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerPermissions = [
            'view_dashboard',
            'view_contents',
            'view_news',
            'view_events',
            'view_careers',
            'view_team-members',
            'view_motors',
            'reports.view',
        ];
        $viewerRole->givePermissionTo($viewerPermissions);

        $this->command->info('Roles and permissions created successfully!');
    }
}