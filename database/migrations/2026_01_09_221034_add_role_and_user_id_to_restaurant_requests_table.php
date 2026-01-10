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
        Schema::table('restaurant_requests', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->string('role')->default('restaurant_owner')->after('cuisine_type');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('role');
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_requests', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['restaurant_id']);
            $table->dropColumn(['role', 'user_id', 'restaurant_id']);
        });
    }
};
