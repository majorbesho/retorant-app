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
        // Drop foreign key from restaurants table first
        if (Schema::hasColumn('restaurants', 'subscription_id')) {
            Schema::table('restaurants', function (Blueprint $table) {
                // We attempt to drop the foreign key. 
                // The name is usually 'restaurants_subscription_id_foreign' for constrained('user_subscriptions')
                // If the constraint name is different, this might fail, but this is the standard naming.
                try {
                    $table->dropForeign(['subscription_id']);
                } catch (\Exception $e) {
                    // Ignore if FK doesn't exist
                }

                // Drop index if exists (specifically for SQLite compatibility)
                try {
                    $table->dropIndex(['subscription_id', 'subscription_status']);
                } catch (\Exception $e) {
                }

                // We also drop the column as we will rely on User's subscription
                $table->dropColumn('subscription_id');
            });
        }

        // Drop redundant table
        Schema::dropIfExists('user_subscriptions');

        // Recreate subscriptions table for Cashier
        Schema::dropIfExists('subscriptions');

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id'); // Cashier standard
            $table->string('name');       // Cashier standard (e.g. 'default')
            $table->string('stripe_id')->unique();
            $table->string('stripe_status');
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Custom fields
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();

            $table->index(['user_id', 'stripe_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't easily reverse without the original schema definitions, 
        // but for now we just drop.
        Schema::dropIfExists('subscriptions');
    }
};
