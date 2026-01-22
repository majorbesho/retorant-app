<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add restaurant_id to tables that missed it for multi-tenancy
        Schema::table('variations', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        Schema::table('variation_options', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('variation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        Schema::table('addons', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->after('addon_group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->index('restaurant_id');
        });

        // 2. Add missing translations columns
        Schema::table('addon_groups', function (Blueprint $table) {
            $table->json('description_translations')->after('name_translations')->nullable();
        });

        Schema::table('addons', function (Blueprint $table) {
            $table->json('description_translations')->after('name_translations')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurant_id');
        });

        Schema::table('variation_options', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurant_id');
        });

        Schema::table('addons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restaurant_id');
        });

        Schema::table('addon_groups', function (Blueprint $table) {
            $table->dropColumn('description_translations');
        });

        Schema::table('addons', function (Blueprint $table) {
            $table->dropColumn('description_translations');
        });
    }
};
