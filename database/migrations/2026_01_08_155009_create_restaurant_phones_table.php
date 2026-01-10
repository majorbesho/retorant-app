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
        Schema::create('restaurant_phones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('whatsapp_number')->unique(); // مثال: '971501234567'
                $table->string('phone_number_id')->nullable(); // من Meta (اختياري لكن موصى به)
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_phones');
    }
};
