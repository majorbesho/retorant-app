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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('name_translations');
            $table->text('description')->nullable();
            $table->json('description_translations')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('available_from')->nullable(); // فترة محددة
            $table->timestamp('available_to')->nullable();
            $table->json('availability_rules')->nullable(); // {days: [1,2,3], times: {start: '09:00', end: '23:00'}}

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
