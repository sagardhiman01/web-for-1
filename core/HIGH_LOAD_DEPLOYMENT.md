# High Load Deployment (100k+ to 1M concurrent roadmap)

This project can handle very high traffic only with horizontal scaling. A single VPS/server will not safely handle 100k+ concurrent active users.

## 1) Required architecture

1. `Load Balancer` (Cloudflare/AWS ALB/Nginx LB) in front of multiple app servers.
2. `App Servers` (Laravel + PHP-FPM + Nginx) with autoscaling.
3. `Redis` for cache + session + queue.
4. `MySQL` primary + read replicas (or managed cluster).
5. `Queue workers` (Supervisor/systemd) for jobs/emails/notifications.
6. `CDN` for static assets (images/js/css).
7. `Monitoring` (CPU/RAM/RPS/DB/Redis slowlog + alerts).

## 2) Required Laravel environment values

Set these in production `.env`:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning

CACHE_DRIVER=redis
SESSION_DRIVER=redis
SESSION_CONNECTION=default
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
REDIS_CACHE_DB=1
```

## 3) Build/optimize commands (run on every deploy)

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

## 4) Queue workers (required)

Example:

```bash
php artisan queue:work redis --queue=default --sleep=1 --tries=3 --timeout=120
```

Run multiple workers per server via Supervisor.

## 5) Web server / PHP-FPM baseline

1. Enable `OPcache` (mandatory).
2. Use `pm = dynamic` or `ondemand` with enough PHP-FPM children.
3. Keepalive + gzip/brotli in Nginx.
4. Rate-limit abusive endpoints at edge/load balancer.
5. Use HTTPS + HTTP/2 or HTTP/3.

## 6) Database scaling

1. Keep MySQL on dedicated machine.
2. Use read replicas for read-heavy endpoints.
3. Keep `slow_query_log` enabled.
4. This project now includes extra performance indexes migration:
   - `2026_02_15_000005_add_high_load_indexes.php`

Run migration:

```bash
php artisan migrate --path=database/migrations/2026_02_15_000005_add_high_load_indexes.php --force
```

## 7) Stress test before production

Use k6/JMeter and test at increasing load:

1. 1k concurrent
2. 5k concurrent
3. 20k concurrent
4. 100k concurrent (distributed load generators)

Do not go live before p95/p99 latency, error rate, and DB/Redis saturation are stable.

## 8) 1M concurrent capacity plan

For ~1,000,000 concurrent users, use distributed infrastructure:

1. Multi-node load balancer + autoscaling app nodes
2. Redis HA/cluster for cache/session/queue
3. MySQL primary + multiple replicas
4. Dedicated queue worker fleet
5. Global CDN + WAF

Detailed sizing and config templates:

1. `deploy/HIGHLOAD_1M_REQUIREMENTS.md`
2. `deploy/highload/nginx-core-asset.conf`
3. `deploy/highload/php-fpm-www-highload.conf`
4. `deploy/highload/supervisor-laravel.conf`
