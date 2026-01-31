# WhatsApp Evolution API Integration - Setup Guide

## 📋 Overview

This integration allows restaurants to automatically connect their WhatsApp accounts to receive orders and messages through the Evolution API v2.1.1.

## 🔧 Installation Steps

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
EVOLUTION_API_URL=https://your-evolution-api.com
EVOLUTION_API_KEY=your-api-key-here
EVOLUTION_WEBHOOK_URL=${APP_URL}/api/whatsapp/webhook
EVOLUTION_INSTANCE_PREFIX=restaurant_
```

### 2. Run Migrations

```bash
php artisan migrate
```

This will add the following columns to the `restaurants` table:
- `instance_name` - Unique Evolution instance identifier
- `instance_token` - Instance API token
- `whatsapp_qr_code` - Base64 QR code for connection
- `whatsapp_status` - Connection status (pending/connected/disconnected/failed)
- `whatsapp_connected_at` - Timestamp of connection

### 3. Queue Configuration

Make sure your queue worker is running for asynchronous instance creation:

```bash
php artisan queue:work
```

## 📁 Files Created

### Backend

1. **Migration**: `database/migrations/2026_01_31_193322_add_whatsapp_instance_to_restaurants.php`
2. **Config**: `config/evolution.php`
3. **Service**: `app/Services/WhatsAppEvolutionService.php`
4. **Jobs**:
   - `app/Jobs/CreateWhatsAppInstanceJob.php`
   - `app/Jobs/FetchWhatsAppQRCodeJob.php`
5. **Event**: `app/Events/WhatsAppConnectedEvent.php`
6. **Controllers**:
   - `app/Http/Controllers/Api/WhatsAppWebhookController.php`
   - Updated `app/Http/Controllers/RestaurantController.php`
7. **Model**: Updated `app/Models/Restaurant.php`

### Frontend

1. **Views**:
   - `resources/views/restaurant/whatsapp-setup.blade.php` - QR code display and setup
   - Updated `resources/views/restaurant/settings.blade.php` - WhatsApp status section

### Routes

1. **Web Routes** (`routes/web.php`):
   - `GET /restaurant/whatsapp/setup` - Setup page
   - `POST /restaurant/whatsapp/create-instance` - Create instance
   - `GET /restaurant/whatsapp/qr-code` - Get QR code
   - `GET /restaurant/whatsapp/status` - Check status
   - `POST /restaurant/whatsapp/disconnect` - Disconnect

2. **API Routes** (`routes/api.php`):
   - `POST /api/whatsapp/webhook` - Webhook endpoint

## 🔄 How It Works

### Restaurant Setup Flow

1. **Restaurant clicks "ربط WhatsApp"** in settings
2. **System creates Evolution instance** (async via job)
3. **QR code is fetched** and displayed
4. **Restaurant scans QR** with WhatsApp mobile app
5. **Webhook receives connection event**
6. **Status updates to "connected"**
7. **Restaurant can now receive messages**

### Message Flow

1. **Customer sends WhatsApp message**
2. **Evolution API sends webhook** to `/api/whatsapp/webhook`
3. **WhatsAppWebhookController processes** the message
4. **Message is logged** and can be forwarded to AI agent
5. **Response is sent back** via Evolution API

## 🎯 API Endpoints Used

### Evolution API v2.1.1

All requests use header: `apikey: YOUR_API_KEY`

1. **Create Instance**
   - `POST /instance/create`
   - Body: `{ instanceName, qrcode: true, integration: 'WHATSAPP-BAILEYS' }`

2. **Get QR Code**
   - `GET /instance/connect/{instanceName}`

3. **Check Status**
   - `GET /instance/connectionState/{instanceName}`

4. **Send Message**
   - `POST /message/sendText/{instanceName}`
   - Body: `{ number, text, delay, linkPreview }`

5. **Set Webhook**
   - `POST /webhook/set/{instanceName}`
   - Body: `{ url, webhook_by_events, webhook_base64, events[] }`

6. **Logout**
   - `DELETE /instance/logout/{instanceName}`

7. **Delete Instance**
   - `DELETE /instance/delete/{instanceName}`

## 📊 Database Schema

```sql
ALTER TABLE restaurants ADD COLUMN instance_name VARCHAR(255) NULL UNIQUE;
ALTER TABLE restaurants ADD COLUMN instance_token TEXT NULL;
ALTER TABLE restaurants ADD COLUMN whatsapp_qr_code TEXT NULL;
ALTER TABLE restaurants ADD COLUMN whatsapp_status ENUM('pending','connected','disconnected','failed') DEFAULT 'pending';
ALTER TABLE restaurants ADD COLUMN whatsapp_connected_at TIMESTAMP NULL;
```

## 🎨 Frontend Features

### Setup Page (`/restaurant/whatsapp/setup`)

- **Not Connected**: Shows "ربط WhatsApp" button
- **Pending**: Displays QR code with auto-refresh
- **Connected**: Shows connection details and disconnect option
- **Failed**: Shows retry button

### Settings Page (`/restaurant/settings`)

- **WhatsApp Status Card** in "الاتصال والتواصل" tab
- Color-coded status badges (green/yellow/gray)
- Quick link to setup page
- Benefits list for non-connected restaurants

## 🔐 Security

1. **Webhook Validation**: API key checked in webhook controller
2. **Authentication**: All routes protected by auth middleware
3. **Permissions**: Requires `restaurant-settings` permission
4. **Token Encryption**: Instance tokens stored in database

## 🧪 Testing

### Manual Testing

1. Navigate to `/restaurant/settings`
2. Go to "الاتصال والتواصل" tab
3. Click "ربط WhatsApp"
4. Scan QR code with WhatsApp
5. Verify connection status updates

### Webhook Testing

Use tools like ngrok to expose local webhook:

```bash
ngrok http 8000
# Update EVOLUTION_WEBHOOK_URL in .env
```

## 📝 Model Methods

### Restaurant Model

```php
// Check if WhatsApp is connected
$restaurant->hasWhatsAppConnected(): bool

// Get WhatsApp service instance
$restaurant->getWhatsAppService(): WhatsAppEvolutionService

// Send WhatsApp message
$restaurant->sendWhatsAppMessage(string $number, string $message): array

// Get status badge color
$restaurant->getWhatsAppStatusBadge(): string

// Get status label (Arabic)
$restaurant->getWhatsAppStatusLabel(): string
```

## 🎯 Next Steps

1. **Integrate with AI Agent** - Process incoming messages with AI
2. **Order Processing** - Parse orders from WhatsApp messages
3. **Notifications** - Send order confirmations via WhatsApp
4. **Analytics** - Track message volumes and response times
5. **Multi-language** - Support for multiple languages in responses

## 🐛 Troubleshooting

### QR Code Not Showing

- Check Evolution API is running
- Verify `EVOLUTION_API_URL` and `EVOLUTION_API_KEY`
- Check Laravel logs: `storage/logs/laravel.log`

### Webhook Not Receiving Events

- Ensure webhook URL is publicly accessible
- Check Evolution webhook configuration
- Verify API key in webhook requests

### Instance Creation Fails

- Check queue worker is running
- Review job failures: `php artisan queue:failed`
- Check Evolution API logs

## 📚 References

- [Evolution API Documentation](https://doc.evolution-api.com/)
- [WhatsApp Business API](https://developers.facebook.com/docs/whatsapp)
- [Laravel Queues](https://laravel.com/docs/queues)
