<?php

namespace App\Http\Controllers\Api;

use App\Events\WhatsAppConnectedEvent;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Evolution API
     */
    public function handle(Request $request)
    {
        // Log all webhook data for debugging
        Log::info('WhatsApp Webhook Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        // Validate API key
        $apiKey = $request->header('apikey');
        if ($apiKey !== config('evolution.api_key')) {
            Log::warning('Invalid API key in webhook', [
                'received_key' => $apiKey,
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        $event = $data['event'] ?? null;

        // Handle different event types
        try {
            match ($event) {
                'MESSAGES_UPSERT', 'messages.upsert' => $this->handleMessageReceived($data),
                'CONNECTION_UPDATE', 'connection.update' => $this->handleConnectionUpdate($data),
                'QRCODE_UPDATED', 'qrcode.updated' => $this->handleQRCodeUpdate($data),
                default => Log::info('Unhandled webhook event', ['event' => $event]),
            };

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing webhook', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle incoming message
     */
    protected function handleMessageReceived(array $data): void
    {
        $instanceName = $data['instance'] ?? null;

        if (!$instanceName) {
            Log::warning('Message received without instance name');
            return;
        }

        // Find restaurant by instance name
        $restaurant = Restaurant::where('instance_name', $instanceName)->first();

        if (!$restaurant) {
            Log::warning('Restaurant not found for instance', [
                'instance_name' => $instanceName,
            ]);
            return;
        }

        // Extract message data
        $messageData = $data['data'] ?? [];
        $message = $messageData['message'] ?? [];
        $key = $messageData['key'] ?? [];

        $from = $key['remoteJid'] ?? null;
        $messageText = $message['conversation'] ??
            $message['extendedTextMessage']['text'] ??
            null;

        if (!$from || !$messageText) {
            Log::info('Message without text or sender', [
                'instance_name' => $instanceName,
            ]);
            return;
        }

        // Skip messages from the restaurant itself
        if ($key['fromMe'] ?? false) {
            return;
        }

        Log::info('Processing customer message', [
            'restaurant_id' => $restaurant->id,
            'from' => $from,
            'message' => $messageText,
        ]);

        // TODO: Process message with AI agent
        // This will be integrated with your existing AI conversation system
        // For now, just log it

        // Example: You could dispatch a job to process with AI
        // ProcessWhatsAppMessageJob::dispatch($restaurant, $from, $messageText);
    }

    /**
     * Handle connection status update
     */
    protected function handleConnectionUpdate(array $data): void
    {
        $instanceName = $data['instance'] ?? null;
        $state = $data['data']['state'] ?? $data['state'] ?? null;

        if (!$instanceName) {
            return;
        }

        $restaurant = Restaurant::where('instance_name', $instanceName)->first();

        if (!$restaurant) {
            Log::warning('Restaurant not found for connection update', [
                'instance_name' => $instanceName,
            ]);
            return;
        }

        Log::info('Connection status updated', [
            'restaurant_id' => $restaurant->id,
            'instance_name' => $instanceName,
            'state' => $state,
        ]);

        // Update restaurant status based on connection state
        if ($state === 'open' || $state === 'connected') {
            // Extract phone number if available
            $phoneNumber = $data['data']['instance']['profilePictureUrl'] ??
                $data['data']['number'] ??
                null;

            $restaurant->update([
                'whatsapp_status' => 'connected',
                'whatsapp_connected_at' => now(),
                'whatsapp_number' => $phoneNumber ?? $restaurant->whatsapp_number,
            ]);

            // Fire event
            event(new WhatsAppConnectedEvent($restaurant, $phoneNumber ?? ''));

            Log::info('WhatsApp connected successfully', [
                'restaurant_id' => $restaurant->id,
                'phone_number' => $phoneNumber,
            ]);
        } elseif ($state === 'close' || $state === 'disconnected') {
            $restaurant->update([
                'whatsapp_status' => 'disconnected',
            ]);

            Log::info('WhatsApp disconnected', [
                'restaurant_id' => $restaurant->id,
            ]);
        }
    }

    /**
     * Handle QR code update
     */
    protected function handleQRCodeUpdate(array $data): void
    {
        $instanceName = $data['instance'] ?? null;
        $qrCode = $data['data']['qrcode']['base64'] ??
            $data['data']['base64'] ??
            $data['qrcode'] ??
            null;

        if (!$instanceName || !$qrCode) {
            return;
        }

        $restaurant = Restaurant::where('instance_name', $instanceName)->first();

        if (!$restaurant) {
            return;
        }

        $restaurant->update([
            'whatsapp_qr_code' => $qrCode,
        ]);

        Log::info('QR code updated', [
            'restaurant_id' => $restaurant->id,
            'instance_name' => $instanceName,
        ]);
    }
}
