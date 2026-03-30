<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
                // User permissions
                'view free plans',
                'upgrade to vip',
                'browse coaches',
                'view coach plan',
                
                // Coach permissions
                'view vip requests',
                'accept vip request',
                'decline vip request',
                'create plan',
                'update plan',
                'regenerate plan',
                
                // Admin permissions
                'approve coach',
                'decline coach',
                'approve post',
                'decline post',
                'create free plan',
                'view pending coaches',
                'view pending posts',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $coachRole = Role::create(['name' => 'coach']);
        $userRole = Role::create(['name' => 'user']);

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Coach permissions
        $coachRole->givePermissionTo([
            'view vip requests',
            'accept vip request',
            'decline vip request',
            'create plan',
            'update plan',
            'regenerate plan',
        ]);

        // User permissions
        $userRole->givePermissionTo([
            'view free plans',
            'upgrade to vip',
            'browse coaches',
            'view coach plan',
        ]);
    }
}


