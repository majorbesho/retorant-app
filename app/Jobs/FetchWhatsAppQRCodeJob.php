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

class FetchWhatsAppQRCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [5, 10, 15, 30, 60];

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
        if (!$this->restaurant->instance_name) {
            Log::warning('Cannot fetch QR code: no instance name', [
                'restaurant_id' => $this->restaurant->id,
            ]);
            return;
        }

        Log::info('Fetching WhatsApp QR code', [
            'restaurant_id' => $this->restaurant->id,
            'instance_name' => $this->restaurant->instance_name,
        ]);

        $qrData = $evolutionService->getInstanceQRCode($this->restaurant->instance_name);

        if ($qrData && isset($qrData['qrcode'])) {
            $this->restaurant->update([
                'whatsapp_qr_code' => $qrData['qrcode'],
            ]);

            Log::info('QR code fetched successfully', [
                'restaurant_id' => $this->restaurant->id,
            ]);
        } else {
            Log::warning('QR code not available yet', [
                'restaurant_id' => $this->restaurant->id,
                'attempt' => $this->attempts(),
            ]);

            // Retry if not max attempts
            if ($this->attempts() < $this->tries) {
                $this->release(10); // Retry after 10 seconds
            }
        }
    }
}
