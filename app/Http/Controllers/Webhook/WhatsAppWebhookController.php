<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        $event = $data['event'] ?? '';
        $instance = $data['instance'] ?? '';

        Log::info("WhatsApp Webhook Received: $event for instance: $instance");

        // 1. معالجة حالة الاتصال (عندما يمسح المستخدم الـ QR)
        if ($event === 'connection.update') {
            $status = $data['data']['state'] ?? '';

            $restaurant = Restaurant::where('instance_name', $instance)->first();

            if ($restaurant) {
                if ($status === 'open') {
                    $restaurant->update(['whatsapp_status' => 'connected']);
                } elseif ($status === 'close' || $status === 'connecting') {
                    // يمكنك تحديث الحالة هنا إذا أردت
                    if ($status === 'close') {
                        $restaurant->update(['whatsapp_status' => 'disconnected']);
                    }
                }
            }
        }

        // 2. معالجة الرسائل الواردة (سيتم استخدامها لاحقاً في استقبال الطلبات)
        if ($event === 'messages.upsert') {
            // هنا سنضع منطق الـ Chatbot لاحقاً
        }

        return response()->json(['status' => 'success'], 200);
    }
}
