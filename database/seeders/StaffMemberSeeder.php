<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaffMember;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Str;

class StaffMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف الموظفين السابقين
        StaffMember::query()->delete();

        // الحصول على المطاعم والمستخدمين
        $restaurants = Restaurant::limit(5)->get();
        $users = User::limit(10)->get();

        if ($restaurants->count() == 0) {
            $restaurants = Restaurant::factory(5)->create();
        }

        if ($users->count() == 0) {
            $users = User::factory(10)->create();
        }

        $roles = ['admin', 'manager', 'chef', 'cashier', 'delivery_driver', 'support_agent'];
        $channels = ['whatsapp', 'web_chat', 'email', 'phone'];

        $staffCount = 0;
        $usedCombinations = []; // Track user_id + restaurant_id combinations

        foreach ($restaurants as $restaurant) {
            // إضافة 2-4 موظفين لكل مطعم
            $staffPerRestaurant = rand(2, 4);

            for ($i = 0; $i < $staffPerRestaurant; $i++) {
                if ($users->count() == 0) break;

                // Find a unique user for this restaurant
                $availableUsers = $users->filter(function ($user) use ($restaurant, $usedCombinations) {
                    $key = $user->id . '-' . $restaurant->id;
                    return !in_array($key, $usedCombinations);
                });

                if ($availableUsers->count() == 0) break;

                $user = $availableUsers->random();
                $combination = $user->id . '-' . $restaurant->id;
                $usedCombinations[] = $combination;

                $role = $roles[array_rand($roles)];

                // تحديد الصلاحيات حسب الدور
                $permissions = $this->getPermissionsForRole($role);

                // تحديد القنوات المسموحة
                $allowedChannels = array_slice(
                    $channels,
                    0,
                    rand(1, count($channels))
                );

                StaffMember::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'restaurant_id' => $restaurant->id,
                    'role' => $role,
                    'permissions' => $permissions,
                    'allowed_channels' => $allowedChannels,
                    'total_orders_handled' => rand(10, 500),
                    'average_rating' => rand(3, 5) + (rand(0, 99) / 100),
                    'is_active' => rand(0, 1) ? true : false,
                ]);

                $staffCount++;
            }
        }

        echo "✅ تم إنشاء $staffCount موظف بنجاح!\n";
    }

    /**
     * الحصول على الصلاحيات حسب الدور
     */
    private function getPermissionsForRole(string $role): array
    {
        return match ($role) {
            'admin' => [
                'manage_staff' => true,
                'manage_settings' => true,
                'view_analytics' => true,
                'respond_to_chats' => true,
                'manage_menu' => true,
            ],
            'manager' => [
                'manage_staff' => false,
                'manage_settings' => true,
                'view_analytics' => true,
                'respond_to_chats' => true,
                'manage_menu' => true,
            ],
            'chef' => [
                'manage_staff' => false,
                'manage_settings' => false,
                'view_analytics' => false,
                'respond_to_chats' => false,
                'manage_menu' => true,
            ],
            'cashier' => [
                'manage_staff' => false,
                'manage_settings' => false,
                'view_analytics' => false,
                'respond_to_chats' => false,
                'manage_menu' => false,
            ],
            'delivery_driver' => [
                'manage_staff' => false,
                'manage_settings' => false,
                'view_analytics' => false,
                'respond_to_chats' => false,
                'manage_menu' => false,
            ],
            'support_agent' => [
                'manage_staff' => false,
                'manage_settings' => false,
                'view_analytics' => false,
                'respond_to_chats' => true,
                'manage_menu' => false,
            ],
            default => [],
        };
    }
}
