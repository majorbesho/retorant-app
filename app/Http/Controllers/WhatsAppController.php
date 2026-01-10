<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function handleIncoming(Request $request)
    {
        // التحقق من التوقيع
        $signature = $request->header('X-Signature');
        $expected = hash_hmac('sha256', $request->getContent(), env('WHATSAPP_WEBHOOK_SECRET'));
        if (! hash_equals($signature, $expected)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $from = $request->input('from'); // رقم العميل (1234567890@c.us)
        $text = strtolower($request->input('text', ''));

        // 🔍 اكتشف أي مطعم هذا الرقم مخصص له
        // (في الواقع: ستحتاج جدول `restaurant_phones` أو ربط رقم الواتساب بالـ restaurant)
        // لكن للـ MVP: نفترض أن كل رسالة تصل إلى رقم مخصص لمطعم واحد
        // لذا نستخدم مطعم افتراضي (مثلاً: ID = 1)
        $restaurantId = 1; // ← ستُعدّله لاحقًا ليدعم أكثر من مطعم

        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            Log::warning("No restaurant found for message from: $from");
            return response()->json(['status' => 'ignored']);
        }

        // 🧠 اكتشف الـ intent
        $intent = 'unknown';
        if (Str::contains($text, ['العنوان', 'موقع', 'فينكم', 'الفرع'])) {
            $intent = 'location';
        } elseif (Str::contains($text, ['منيو', 'قائمة', 'اسعار', 'وجبات'])) {
            $intent = 'menu';
        } elseif (Str::contains($text, ['طلب', 'عايز', 'اريد', 'احجز'])) {
            $intent = 'order';
        }

        // 📤 أنشئ الرد
        $workingHours = $restaurant->working_hours_text ?? '10 ص - 12 ص';
        $orderLink = $restaurant->order_link ?? 'https://talabat.com/...';

        $reply = match ($intent) {
            'location' => "📍 *{$restaurant->name}*\nالعنوان: {$restaurant->address}\nالوقت: {$workingHours}",
            'menu' => "🍽️ شوف المنيو الكامل هنا:\nhttps://yourdomain.com/menu/{$restaurant->slug}",
            'order' => "🚀 اطلب الآن بخصم 10%:\n{$orderLink}",
            default => "مرحبًا! 🤖\nأنا مساعد {$restaurant->name} الذكي.\n\nاسألني عن:\n📍 العنوان\n🍽️ المنيو\n🚀 طلب أونلاين"
        };

        // 📬 أرسل الرد عبر Node.js Gateway
        \Illuminate\Support\Facades\Http::post('http://localhost:4000/send', [
            'to' => $from,
            'message' => $reply
        ]);

        // 📊 سجّل المحادثة (اختياري)
        \Log::info("WhatsApp message processed", [
            'from' => $from,
            'text' => $text,
            'intent' => $intent,
            'restaurant_id' => $restaurantId
        ]);

        return response()->json(['status' => 'processed']);
    }
}
