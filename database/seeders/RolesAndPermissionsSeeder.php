<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = [
            'super_admin',
            'restaurant_owner',
            'restaurant_staff',
            'customer',
            'delivery_driver',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create Permissions (Example)
        $permissions = [
            'manage_restaurants',
            'manage_users',
            'manage_orders',
            'place_orders',
            'deliver_orders',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign Permissions to Roles (Basic mapping)
        Role::findByName('super_admin')->givePermissionTo(Permission::all());
        Role::findByName('restaurant_owner')->givePermissionTo(['manage_restaurants', 'manage_orders']);
        Role::findByName('restaurant_staff')->givePermissionTo(['manage_orders']);
        Role::findByName('customer')->givePermissionTo(['place_orders']);
        Role::findByName('delivery_driver')->givePermissionTo(['deliver_orders']);
    }
}
