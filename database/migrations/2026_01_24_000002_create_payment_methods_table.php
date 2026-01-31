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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // معرف Stripe
            $table->string('stripe_payment_method_id')->nullable()->unique();

            // نوع الدفع
            $table->enum('type', ['card', 'wallet', 'bank_transfer', 'digital_wallet'])->default('card');

            // معلومات البطاقة
            $table->enum('brand', ['visa', 'mastercard', 'amex', 'discover', 'diners', 'jcb'])->nullable();
            $table->string('last_four')->nullable();
            $table->integer('expiry_month')->nullable();
            $table->integer('expiry_year')->nullable();
            $table->string('cardholder_name')->nullable();

            // الحالة
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('verified_at')->nullable();

            // بيانات إضافية
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
