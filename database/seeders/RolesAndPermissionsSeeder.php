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

        // Create Permissions
        $permissions = [
            // System Level
            'manage-system',
            'manage-restaurants',
            'manage-users',
            'view-admin-dashboard',

            // Restaurant Level
            'view-restaurant-dashboard',
            'menu-management',
            'category-management',
            'product-management',
            'addon-management',
            'variation-management',
            'order-management',
            'reservation-management',
            'staff-management',
            'restaurant-settings',

            // Customer Level
            'place-orders',
            'view-own-orders',
            'manage-profile',

            // Driver Level
            'deliver-orders',
            'update-delivery-status',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign Permissions to Roles

        // Super Admin: Everything
        Role::findByName('super_admin')->syncPermissions(Permission::all());

        // Restaurant Owner: Everything related to their restaurant
        Role::findByName('restaurant_owner')->syncPermissions([
            'view-restaurant-dashboard',
            'menu-management',
            'category-management',
            'product-management',
            'addon-management',
            'variation-management',
            'order-management',
            'reservation-management',
            'staff-management',
            'restaurant-settings',
        ]);

        // Restaurant Staff: Operations mostly
        Role::findByName('restaurant_staff')->syncPermissions([
            'view-restaurant-dashboard',
            'order-management',
            'reservation-management',
            'menu-management',
        ]);

        // Customer
        Role::findByName('customer')->syncPermissions([
            'place-orders',
            'view-own-orders',
            'manage-profile',
        ]);

        // Delivery Driver
        Role::findByName('delivery_driver')->syncPermissions([
            'deliver-orders',
            'update-delivery-status',
        ]);
    }
}
