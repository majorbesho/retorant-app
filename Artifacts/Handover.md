# Deployment Handover & Operations Guide

**Role:** DevOps Engineer
**Method:** CI/CD Pipeline & Infrastructure as Code
**Version:** 1.0

## 1. Deployment Pipeline (CI/CD)
We use GitHub Actions -> DigitalOcean App Platform (or similar VPS).

### Stage 1: Build (CI)
Script: `.github/workflows/ci.yml`
1.  `checkout` code.
2.  `setup-php` 8.2.
3.  `composer install --no-dev`.
4.  Run Linter (`npm run lint` / `pint`).
5.  Run Tests (`php artisan test`).
    *   *Gate:* If fail, stop.

### Stage 2: Deploy (CD)
1.  SSH into Production Server.
2.  `git pull origin main`.
3.  `composer install --optimize-autoloader --no-dev`.
4.  `php artisan migrate --force`.
5.  `php artisan config:cache` & `route:cache`.
6.  `php artisan view:cache`.
7.  Restart Queues: `php artisan queue:restart`.
8.  Reload Octane/PHP-FPM: `sudo service php8.2-fpm reload`.

## 2. Environment Configuration (.env)
Ensure these secrets are set in Production Vault:
```ini
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis  <-- CRITICAL for AI API
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# n8n Integration
N8N_API_KEY=sk_prod_xxxxxxxxxxxx  <-- Rotate every 90 days

# Stripe
STRIPE_SECRET=sk_live_...
```

## 3. Rollback Plan
In case of Critical Failure (SEV-1) after deployment:

**Option A: Code Rollback (Fast)**
```bash
git reset --hard HEAD~1
php artisan optimize:clear
sudo service php8.2-fpm reload
```

**Option B: Database Rollback (Risky)**
*   Only use if migrations broke data integrity.
```bash
php artisan migrate:rollback --step=1
```

## 4. Monitoring & Alerts
*   **Uptime:** UptimeRobot checks `/up` endpoint every 1 min.
*   **Logs:** All logs streamed to CloudWatch/Papertrail.
*   **Alerts:** Slack Channel `#ops-alerts` triggered if:
    *   API 5xx errors > 1%.
    *   Redis Memory Usage > 80%.
