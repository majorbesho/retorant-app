<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();

            // الدور والصلاحيات
            $table->enum('role', [
                'manager',
                'chef',
                'cashier',
                'delivery_driver',
                'support_agent',
                'admin',
            ])->default('support_agent');

            // المعلومات الشخصية
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            // التوظيف
            $table->date('join_date')->useCurrent();
            $table->date('end_date')->nullable()->comment('تاريخ انتهاء الخدمة');
            $table->boolean('is_active')->default(true);

            // الإحصائيات
            $table->integer('total_orders_handled')->default(0);
            $table->integer('total_conversations_handled')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamp('last_active_at')->nullable();

            // الصلاحيات المخصصة
            $table->json('permissions')->nullable()->comment('صلاحيات محددة مخصصة');
            $table->json('allowed_channels')->nullable()->comment('القنوات المسموح بها');

            // بيانات إضافية
            $table->string('national_id')->nullable()->comment('رقم الهوية الوطنية');
            $table->string('bank_account')->nullable()->comment('حساب البنك للتحويلات');
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'restaurant_id']);
            $table->index(['restaurant_id', 'role', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};
