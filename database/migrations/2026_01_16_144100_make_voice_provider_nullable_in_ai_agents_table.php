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
        Schema::table('ai_agents', function (Blueprint $table) {
            $table->string('voice_provider')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_agents', function (Blueprint $table) {
            // Revert to not nullable with default if needed, 
            // but strict revert might fail if nulls exist. 
            // For now, we assume we might want to keep it nullable or revert to previous state.
            $table->string('voice_provider')->default('elevenlabs')->nullable(false)->change();
        });
    }
};
