<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
        ]);
        $superAdmin->assignRole('super_admin');

        // 2. Restaurant Owners (5)
        $restaurants = \App\Models\Restaurant::all();
        $owners = User::factory(5)->create([
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
        ]);
        foreach ($owners as $index => $owner) {
            $owner->restaurant_id = $restaurants[$index % $restaurants->count()]->id;
            $owner->save();
            $owner->assignRole('restaurant_owner');
        }

        // 3. Restaurant Staff (5)
        $staff = User::factory(5)->create([
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
        ]);
        foreach ($staff as $index => $s) {
            $s->restaurant_id = $restaurants[$index % $restaurants->count()]->id;
            $s->save();
            $s->assignRole('restaurant_staff');
        }

        // 4. Delivery Drivers (5)
        $drivers = User::factory(5)->create([
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
        ]);
        foreach ($drivers as $driver) {
            $driver->assignRole('delivery_driver');
        }

        // 5. Customers (4)
        $customers = User::factory(4)->create([
            'password' => Hash::make('password123'),
            'is_super_admin' => false,
        ]);
        foreach ($customers as $customer) {
            $customer->assignRole('customer');
        }
    }
}
