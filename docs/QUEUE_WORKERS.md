# OTP Queue Workers Setup

This document describes how to set up and test the OTP queue workers using Supervisor.

## Overview

The system uses Laravel Queue with Redis for processing OTP (One-Time Password) emails asynchronously. Supervisor manages the queue workers to ensure they restart on failure and run continuously.

## Components

### 1. Queue Configuration
- **Connection**: Redis
- **Queues**:
  - `otp` - High priority for OTP emails
  - `default` - Standard queue
  - `notifications` - For notification emails

### 2. Supervisor Workers
Located in `docker/supervisor/`:

| File | Purpose | Queue | Processes |
|------|---------|-------|-----------|
| `otp.conf` | OTP Queue Worker | otp,default | 2 |
| `default.conf` | Default Queue Worker | default,emails | 2 |
| `supervisord.conf` | Complete supervisor config | All queues | 5 total |

## Local Development

### Option 1: Using Laravel Artisan

```bash
# Start OTP queue worker
php artisan queue:work redis --queue=otp,default

# Or use the custom command
php artisan supervisor:start --queue=otp,default
```

### Option 2: Using the OTP Worker Script

```bash
php artisan otp:worker
```

### Option 3: Starting Multiple Workers

```bash
# Terminal 1: OTP worker
php artisan queue:work redis --queue=otp --sleep=3

# Terminal 2: Default worker
php artisan queue:work redis --queue=default,emails --sleep=3
```

## Docker Production

The `docker-compose.yml` includes a `supervisor` service that runs all queue workers:

```yaml
supervisor:
  build:
    context: .
    target: app
  volumes:
    - ./docker/supervisor:/etc/supervisor/conf.d:ro
  command: >
    sh -c "mkdir -p /app/storage/logs &&
           /usr/bin/supervisord -c /etc/supervisor/supervisord.conf"
```

### Build and Start

```bash
# Build images
docker-compose build

# Start all services including supervisor
docker-compose up -d

# Check supervisor status
docker-compose exec supervisor supervisorctl status

# View logs
docker-compose logs -f supervisor
```

## Testing OTP Email

### 1. Ensure Mailpit is Running

```bash
# Check if mailpit is accessible
curl http://localhost:8025
```

### 2. Start Queue Worker

```bash
php artisan queue:work redis --queue=otp
```

### 3. Send Test Email

```bash
# Using the test command
php artisan otp:test-email test@example.com

# Or via the API
curl -X POST http://localhost/api/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```

### 4. Check Mailpit

Open http://localhost:8025 in your browser to view the sent OTP emails.

## Troubleshooting

### Queue Worker Not Starting

```bash
# Check if Redis is running
redis-cli ping

# Check queue connection
php artisan queue:failed
```

### Jobs Not Processing

```bash
# Check queued jobs
php artisan queue:work redis --queue=otp --once

# View recent failures
php artisan queue:failed
```

### View Logs

```bash
# Supervisor logs
docker-compose logs supervisor

# Laravel queue logs
tail -f storage/logs/laravel.log
```

## Queue Worker Options

| Option | Description | Default |
|--------|-------------|---------|
| `--queue` | Queue names to process | default |
| `--sleep` | Seconds to sleep when empty | 3 |
| `--tries` | Max job attempts | 3 |
| `--timeout` | Max job runtime (seconds) | 60 |
| `--max-time` | Max worker runtime | 3600 |

## Production Best Practices

1. **Use Supervisor**: Always run queue workers under Supervisor for auto-restart
2. **Multiple Workers**: Scale workers based on job volume
3. **Monitor**: Set up monitoring for failed jobs
4. **Logs**: Rotate logs to prevent disk full issues
5. **Memory**: Limit worker memory to prevent leaks

```ini
; Example production config
[program:laravel-queue]
command=php /app/artisan queue:work redis --queue=otp,default --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
numprocs=4
redirect_stderr=true
stdout_logfile=/app/storage/logs/supervisor.log
memory_limit=128M
```