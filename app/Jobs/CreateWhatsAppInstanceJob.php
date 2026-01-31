<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Services\WhatsAppEvolutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateWhatsAppInstanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1min, 2min, 5min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Restaurant $restaurant
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppEvolutionService $evolutionService): void
    {
        Log::info('Starting WhatsApp instance creation job', [
            'restaurant_id' => $this->restaurant->id,
            'restaurant_name' => $this->restaurant->name,
        ]);

        // Create the instance
        $result = $evolutionService->createInstance($this->restaurant);

        if ($result['success']) {
            // Update restaurant with instance details
            $this->restaurant->update([
                'instance_name' => $result['instance_name'],
                'instance_token' => $result['token'],
                'whatsapp_status' => 'pending',
            ]);

            Log::info('WhatsApp instance created successfully', [
                'restaurant_id' => $this->restaurant->id,
                'instance_name' => $result['instance_name'],
            ]);

            // Dispatch job to fetch QR code
            FetchWhatsAppQRCodeJob::dispatch($this->restaurant)
                ->delay(now()->addSeconds(5));
        } else {
            // Mark as failed
            $this->restaurant->update([
                'whatsapp_status' => 'failed',
            ]);

            Log::error('Failed to create WhatsApp instance', [
                'restaurant_id' => $this->restaurant->id,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            // Throw exception to trigger retry
            throw new \Exception($result['error'] ?? 'Failed to create WhatsApp instance');
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('WhatsApp instance creation job failed permanently', [
            'restaurant_id' => $this->restaurant->id,
            'error' => $exception->getMessage(),
        ]);

        // Update restaurant status
        $this->restaurant->update([
            'whatsapp_status' => 'failed',
        ]);
    }
}
