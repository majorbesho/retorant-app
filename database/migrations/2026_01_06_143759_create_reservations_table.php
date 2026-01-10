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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('reservation_number')->unique();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // بيانات العميل
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->integer('number_of_guests');

            // تفاصيل الحجز
            $table->timestamp('reservation_date');
            $table->time('reservation_time');
            $table->enum('shift', ['breakfast', 'lunch', 'dinner', 'late_night']);
            $table->integer('duration')->default(120); // بالمدة بالدقائق
            $table->string('table_number')->nullable();
            $table->json('table_preferences')->nullable(); // {area: 'window', type: 'booth'}

            // الحالة
            $table->enum('status', ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->enum('occasion', ['birthday', 'anniversary', 'business', 'date', 'family', 'none'])->default('none');

            // التوأكيد والإشعارات
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->boolean('reminder_sent')->default(false);

            // الدفع
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('deposit_status', ['pending', 'paid', 'refunded'])->default('pending');

            // بيانات النظام
            $table->json('metadata')->nullable();
            $table->string('source')->default('web');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'reservation_date', 'status']);
            $table->index(['customer_phone', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
