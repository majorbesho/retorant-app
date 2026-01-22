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
        Schema::table('conversations', function (Blueprint $table) {
            // New fields for turn-based logging
            if (!Schema::hasColumn('conversations', 'customer_phone_number')) {
                $table->string('customer_phone_number')->after('customer_phone')->nullable();
            }
            $table->text('message_text')->after('messages')->nullable();
            $table->text('response_text')->after('message_text')->nullable();
            $table->enum('message_direction', ['inbound', 'outbound'])->after('response_text')->default('inbound');
            $table->string('sentiment')->after('message_direction')->nullable();
            $table->string('escalation_status')->after('sentiment')->nullable();

            // Supporting the requested structure
            $table->index('customer_phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'customer_phone_number',
                'message_text',
                'response_text',
                'message_direction',
                'sentiment',
                'escalation_status'
            ]);
        });
    }
};
