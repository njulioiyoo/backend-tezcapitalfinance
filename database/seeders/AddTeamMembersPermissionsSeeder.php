<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AddTeamMembersPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions for team members
        $newPermissions = [
            'view_team-members',
            'create_team-members',
            'edit_team-members',
            'delete_team-members',
            'publish_team-members',
        ];

        foreach ($newPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Add permissions to existing roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }

        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole) {
            $editorRole->givePermissionTo([
                'view_team-members',
                'create_team-members',
                'edit_team-members',
            ]);
        }

        $viewerRole = Role::where('name', 'viewer')->first();
        if ($viewerRole) {
            $viewerRole->givePermissionTo(['view_team-members']);
        }

        $this->command->info('Team members permissions added successfully!');
    }
}
