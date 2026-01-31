<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppEvolutionService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $webhookUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('evolution.api_url');
        $this->apiKey = config('evolution.api_key');
        $this->webhookUrl = config('evolution.webhook_url');
        $this->timeout = config('evolution.timeout', 30);
    }

    /**
     * Create a new Evolution instance for a restaurant
     */
    public function createInstance(Restaurant $restaurant): array
    {
        try {
            $instanceName = $this->generateInstanceName($restaurant);

            Log::info('Creating Evolution instance', [
                'restaurant_id' => $restaurant->id,
                'instance_name' => $instanceName,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->post("{$this->apiUrl}/instance/create", [
                    'instanceName' => $instanceName,
                    'token' => bin2hex(random_bytes(16)), // Generate secure random token
                    'qrcode' => true,
                    'integration' => 'WHATSAPP-BAILEYS',
                ]);

            if ($response->failed()) {
                throw new Exception('Failed to create instance: ' . $response->body());
            }

            $data = $response->json();

            Log::info('Evolution instance created successfully', [
                'restaurant_id' => $restaurant->id,
                'instance_name' => $instanceName,
            ]);

            // Set webhook for this instance
            $this->setWebhook($instanceName);

            return [
                'success' => true,
                'instance_name' => $instanceName,
                'token' => $data['hash']['apikey'] ?? null,
                'data' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Failed to create Evolution instance', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for instance connection
     */
    public function getInstanceQRCode(string $instanceName): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->get("{$this->apiUrl}/instance/connect/{$instanceName}");

            if ($response->failed()) {
                Log::error('Failed to get QR code', [
                    'instance_name' => $instanceName,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();

            return [
                'qrcode' => $data['base64'] ?? $data['qrcode']['base64'] ?? null,
                'code' => $data['code'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Exception getting QR code', [
                'instance_name' => $instanceName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get instance connection status
     */
    public function getInstanceStatus(string $instanceName): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->get("{$this->apiUrl}/instance/connectionState/{$instanceName}");

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            return [
                'state' => $data['state'] ?? 'unknown',
                'instance' => $data['instance'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Exception getting instance status', [
                'instance_name' => $instanceName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete an Evolution instance
     */
    public function deleteInstance(string $instanceName): bool
    {
        try {
            Log::info('Deleting Evolution instance', [
                'instance_name' => $instanceName,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->delete("{$this->apiUrl}/instance/delete/{$instanceName}");

            if ($response->failed()) {
                Log::error('Failed to delete instance', [
                    'instance_name' => $instanceName,
                    'status' => $response->status(),
                ]);
                return false;
            }

            Log::info('Evolution instance deleted successfully', [
                'instance_name' => $instanceName,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception deleting instance', [
                'instance_name' => $instanceName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendMessage(string $instanceName, string $number, string $message): array
    {
        try {
            // Ensure number is in correct format (e.g., 2010xxxxxxxx)
            $number = $this->formatPhoneNumber($number);

            Log::info('Sending WhatsApp message', [
                'instance_name' => $instanceName,
                'number' => $number,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->post("{$this->apiUrl}/message/sendText/{$instanceName}", [
                    'number' => $number,
                    'text' => $message,
                    'delay' => config('evolution.message_delay', 1200),
                    'linkPreview' => config('evolution.link_preview', true),
                ]);

            if ($response->failed()) {
                throw new Exception('Failed to send message: ' . $response->body());
            }

            Log::info('WhatsApp message sent successfully', [
                'instance_name' => $instanceName,
                'number' => $number,
            ]);

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'instance_name' => $instanceName,
                'number' => $number,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Set webhook for an instance
     */
    public function setWebhook(string $instanceName): bool
    {
        try {
            Log::info('Setting webhook for instance', [
                'instance_name' => $instanceName,
                'webhook_url' => $this->webhookUrl,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->post("{$this->apiUrl}/webhook/set/{$instanceName}", [
                    'url' => $this->webhookUrl,
                    'webhook_by_events' => false,
                    'webhook_base64' => true,
                    'events' => config('evolution.webhook_events', [
                        'MESSAGES_UPSERT',
                        'CONNECTION_UPDATE',
                        'QRCODE_UPDATED',
                    ]),
                ]);

            if ($response->failed()) {
                Log::error('Failed to set webhook', [
                    'instance_name' => $instanceName,
                    'status' => $response->status(),
                ]);
                return false;
            }

            Log::info('Webhook set successfully', [
                'instance_name' => $instanceName,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception setting webhook', [
                'instance_name' => $instanceName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate unique instance name for restaurant
     */
    protected function generateInstanceName(Restaurant $restaurant): string
    {
        $prefix = config('evolution.instance_prefix', 'restaurant_');
        return $prefix . $restaurant->id . '_' . time();
    }

    /**
     * Format phone number for WhatsApp (remove + and spaces)
     */
    protected function formatPhoneNumber(string $number): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // Ensure it starts with country code (e.g., 20 for Egypt)
        if (!str_starts_with($number, '20') && strlen($number) === 10) {
            $number = '20' . $number;
        }

        return $number;
    }

    /**
     * Logout from instance (disconnect WhatsApp)
     */
    public function logoutInstance(string $instanceName): bool
    {
        try {
            Log::info('Logging out instance', [
                'instance_name' => $instanceName,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders(['apikey' => $this->apiKey])
                ->delete("{$this->apiUrl}/instance/logout/{$instanceName}");

            if ($response->failed()) {
                Log::error('Failed to logout instance', [
                    'instance_name' => $instanceName,
                    'status' => $response->status(),
                ]);
                return false;
            }

            Log::info('Instance logged out successfully', [
                'instance_name' => $instanceName,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Exception logging out instance', [
                'instance_name' => $instanceName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
