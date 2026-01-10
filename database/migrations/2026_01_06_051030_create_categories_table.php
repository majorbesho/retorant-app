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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->json('name_translations');
            $table->text('description')->nullable();
            $table->json('description_translations')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('tags')->nullable(); // [popular, new, spicy, vegetarian]

            $table->timestamps();
            $table->softDeletes();

            $table->index(['restaurant_id', 'menu_id', 'parent_id']);
            $table->index('sort_order');
        });
        Schema::create('category_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ancestor_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('descendant_id')->constrained('categories')->cascadeOnDelete();
            $table->integer('depth')->default(0);

            $table->unique(['ancestor_id', 'descendant_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
