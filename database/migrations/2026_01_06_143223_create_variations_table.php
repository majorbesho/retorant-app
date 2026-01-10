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
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // الحجم: كبير، صغير، لون: أحمر، أخضر
            $table->json('name_translations');
            $table->json('values')->nullable(); // {size: ['small', 'large'], color: ['red', 'green']}
            $table->boolean('is_required')->default(false);
            $table->integer('min_selections')->default(1);
            $table->integer('max_selections')->default(1);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });

        Schema::create('variation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_id')->constrained()->cascadeOnDelete();
            $table->string('value'); // small, large, red
            $table->json('value_translations');
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->integer('stock_quantity')->default(-1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable(); // صورة خاصة بالخيار

            $table->timestamps();

            $table->unique(['variation_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};
