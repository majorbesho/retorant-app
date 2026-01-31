<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف طرق الدفع السابقة
        PaymentMethod::query()->delete();

        // الحصول على أو إنشاء المستخدمين
        $users = User::limit(15)->get();

        if ($users->count() == 0) {
            $users = User::factory(15)->create();
        }

        $types = ['card', 'wallet', 'bank_transfer'];
        $brands = ['visa', 'mastercard', 'amex'];

        $paymentCount = 0;

        foreach ($users as $user) {
            // إضافة 1-2 طريقة دفع لكل مستخدم
            $methodsPerUser = rand(1, 2);

            for ($i = 0; $i < $methodsPerUser; $i++) {
                $type = $types[array_rand($types)];
                $brand = $type === 'card' ? $brands[array_rand($brands)] : null;

                PaymentMethod::create([
                    'user_id' => $user->id,
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'stripe_payment_method_id' => 'pm_' . bin2hex(random_bytes(15)),
                    'type' => $type,
                    'brand' => $brand,
                    'last_four' => str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
                    'expiry_month' => rand(1, 12),
                    'expiry_year' => now()->year + rand(1, 5),
                    'cardholder_name' => $user->name,
                    'is_default' => $i === 0, // الطريقة الأولى هي الافتراضية
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);

                $paymentCount++;
            }
        }

        echo "✅ تم إنشاء $paymentCount طريقة دفع بنجاح!\n";
    }
}
