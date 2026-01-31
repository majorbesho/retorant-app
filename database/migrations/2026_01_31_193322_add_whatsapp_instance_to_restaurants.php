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
        Schema::table('restaurants', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('restaurants', 'instance_name')) {
                $table->string('instance_name')->nullable()->unique()->after('slug');
            }
            if (!Schema::hasColumn('restaurants', 'instance_token')) {
                $table->text('instance_token')->nullable()->after('instance_name');
            }
            // whatsapp_number already exists in the table
            if (!Schema::hasColumn('restaurants', 'whatsapp_qr_code')) {
                $table->text('whatsapp_qr_code')->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('restaurants', 'whatsapp_status')) {
                $table->enum('whatsapp_status', [
                    'pending',      // Instance created, waiting for QR scan
                    'connected',    // WhatsApp connected and active
                    'disconnected', // Manually disconnected
                    'failed'        // Connection failed or error
                ])->default('pending')->after('whatsapp_qr_code');
            }
            if (!Schema::hasColumn('restaurants', 'whatsapp_connected_at')) {
                $table->timestamp('whatsapp_connected_at')->nullable()->after('whatsapp_status');
            }
        });

        // Add indexes if they don't exist
        if (!Schema::hasIndex('restaurants', 'restaurants_instance_name_index')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->index('instance_name');
            });
        }
        if (!Schema::hasIndex('restaurants', 'restaurants_whatsapp_status_index')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->index('whatsapp_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropIndex(['instance_name']);
            $table->dropIndex(['whatsapp_status']);

            $table->dropColumn([
                'instance_name',
                'instance_token',
                // whatsapp_number already existed, don't drop it
                'whatsapp_qr_code',
                'whatsapp_status',
                'whatsapp_connected_at',
            ]);
        });
    }
};
