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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name'); // نسخة عند الطلب
            $table->json('product_name_translations');

            // الكمية والسعر
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            // التخصيصات
            $table->json('variations')->nullable(); // {size: 'large', color: 'red'}
            $table->json('addons')->nullable(); // [{name: 'extra cheese', price: 2.00}]
            $table->text('special_instructions')->nullable();

            // بيانات المنتج وقت الطلب
            $table->json('product_data')->nullable(); // نسخة كاملة من بيانات المنتج

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
