# Core Asset Investing - 1M Concurrent Readiness Blueprint

This document defines a production topology for handling up to ~1,000,000 concurrent connected users (not all performing heavy write transactions at the exact same millisecond).  
Single-server hosting cannot safely support this target.

## 1) Minimum Recommended Production Topology

1. Edge / CDN / DDoS:
1. Cloudflare Pro/Business (or AWS CloudFront + WAF) in front of all traffic.

2. Load Balancer layer:
1. 2x L4/L7 load balancers (active-active), each:
1. 8 vCPU, 16 GB RAM, 10 Gbps network.

3. App layer (Laravel + Nginx + PHP-FPM):
1. Start with 24 app nodes:
1. 8 vCPU, 16 GB RAM per node.
2. Autoscale to 40+ nodes during spikes.

4. Redis layer:
1. Redis Cluster (3 primary + 3 replica) OR managed Redis with HA.
2. Minimum 3 nodes, each:
1. 8 vCPU, 32 GB RAM, persistent storage enabled.

5. Database layer:
1. MySQL primary:
1. 32 vCPU, 128 GB RAM, NVMe SSD, provisioned IOPS.
2. Read replicas:
1. 4 replicas minimum, each 16 vCPU, 64 GB RAM.
3. PITR backups + binary logs + failover automation required.

6. Queue workers:
1. 6 worker nodes minimum:
1. 4 vCPU, 8 GB RAM each.
2. Supervisor managed workers with horizontal scaling.

7. Observability:
1. Prometheus + Grafana + Loki/ELK + alerting (PagerDuty/Slack).
2. SLO alerts for p95 latency, error %, DB/Redis saturation, queue lag.

## 2) Performance Rules (Mandatory)

1. `CACHE_DRIVER=redis`
2. `SESSION_DRIVER=redis`
3. `QUEUE_CONNECTION=redis`
4. Enable PHP OPcache + JIT (if stable in your runtime).
5. Keep `APP_DEBUG=false`.
6. Run all Laravel caches:
1. `php artisan config:cache`
2. `php artisan route:cache`
3. `php artisan view:cache`
4. `php artisan event:cache`

## 3) Capacity Notes

1. 1M concurrent does not mean 1M heavy DB writes/second.
2. Wallet/deposit/withdraw endpoints must be protected by throttles, queues, and DB constraints.
3. Session and frontend-content caching are required to reduce DB pressure.
4. Add load testing in stages before launch:
1. 10k -> 50k -> 100k -> 250k -> 500k -> 1M simulated concurrent connections.

## 4) Required Deployment Artifacts (included)

1. `deploy/highload/nginx-core-asset.conf`
2. `deploy/highload/php-fpm-www-highload.conf`
3. `deploy/highload/supervisor-laravel.conf`
4. `core/.env.highload.example`

## 5) Final Warning

Without autoscaling, Redis HA, DB replicas, and queue worker separation, the app can still crash under very high simultaneous traffic even if code is optimized.

