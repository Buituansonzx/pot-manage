# Hướng dẫn cấu hình và sử dụng Supervisor trong Docker

## Tổng quan
Supervisor là một hệ thống quản lý process cho Unix/Linux, cho phép giám sát và điều khiển các tiến trình chạy nền. Trong dự án Homestay, chúng ta sử dụng Supervisor để quản lý:
- PHP-FPM process
- Laravel Queue Workers 
- Laravel Task Scheduler

## Cấu trúc file cấu hình

### 1. File cấu hình chính: `supervisord.conf`
```properties
[supervisord]
nodaemon=true               # Chạy ở foreground (phù hợp cho Docker)
user=root                   # User chạy supervisord
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[unix_http_server]
file=/var/run/supervisor.sock
chmod=0700

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface
```

### 2. Cấu hình các Program

#### PHP-FPM Process
```properties
[program:php-fpm]
command=/usr/local/sbin/php-fpm -F    # -F = foreground mode
autostart=true                         # Tự động start khi supervisord khởi động
autorestart=true                       # Tự động restart nếu process died
user=root                              # User chạy process
stdout_logfile=/var/log/supervisor/php-fpm.log
stderr_logfile=/var/log/supervisor/php-fpm.log
```

#### Laravel Queue Worker
```properties
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/homestay-api/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www                               # Chạy với user www (bảo mật)
numprocs=1                             # Số process worker
redirect_stderr=true                   # Gộp stderr vào stdout
stdout_logfile=/var/log/supervisor/laravel-worker.log
stopwaitsecs=3600                      # Thời gian chờ trước khi force kill
```

#### Laravel Task Scheduler
```properties
[program:laravel-schedule]
process_name=%(program_name)s
command=/bin/bash -c "while [ true ]; do (php /var/www/html/homestay-api/artisan schedule:run --verbose --no-interaction &); sleep 60; done"
autostart=true
autorestart=true
user=www
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-schedule.log
```

## Cấu hình trong Docker

### 1. Dockerfile
```dockerfile
# Cài đặt Supervisor
RUN apt-get update && apt-get install -y supervisor && rm -rf /var/lib/apt/lists/*

# Copy cấu hình
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Tạo thư mục log
RUN mkdir -p /var/log/supervisor /var/run/php-fpm
```

### 2. docker-compose.yml
```yaml
php-fpm:
  build:
    args:
      - INSTALL_SUPERVISOR=true
  environment:
    - INSTALL_SUPERVISOR=true
  volumes:
    - ./php-fpm/supervisord.conf:/etc/supervisor/conf.d/supervisord.conf
```

### 3. Script khởi động (start.sh)
```bash
# Kiểm tra biến môi trường
if [ "$INSTALL_SUPERVISOR" = "true" ]; then
    echo "Starting services with Supervisor..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
else
    echo "Starting PHP-FPM..."
    exec php-fpm
fi
```

## Các lệnh supervisorctl thường dùng

### 1. Kiểm tra trạng thái
```bash
# Xem trạng thái tất cả services
docker exec honestay_php_fpm supervisorctl status

# Output:
# laravel-schedule    RUNNING   pid 32187, uptime 0:00:10
# laravel-worker_00   RUNNING   pid 32188, uptime 0:00:10  
# php-fpm             RUNNING   pid 31941, uptime 0:12:26
```

### 2. Quản lý services
```bash
# Khởi động tất cả services
docker exec honestay_php_fpm supervisorctl start all

# Khởi động một service cụ thể
docker exec honestay_php_fpm supervisorctl start laravel-worker

# Dừng một service
docker exec honestay_php_fpm supervisorctl stop laravel-worker

# Restart một service
docker exec honestay_php_fpm supervisorctl restart laravel-worker

# Restart tất cả
docker exec honestay_php_fpm supervisorctl restart all
```

### 3. Quản lý cấu hình
```bash
# Đọc lại file cấu hình
docker exec honestay_php_fpm supervisorctl reread

# Cập nhật services (sau khi thay đổi config)
docker exec honestay_php_fpm supervisorctl update

# Reload toàn bộ supervisor
docker exec honestay_php_fpm supervisorctl reload
```

### 4. Xem logs
```bash
# Xem log real-time của một service
docker exec honestay_php_fpm supervisorctl tail -f laravel-worker

# Xem log với số dòng cụ thể
docker exec honestay_php_fpm supervisorctl tail laravel-worker

# Xem log từ file trực tiếp
docker exec honestay_php_fpm tail -f /var/log/supervisor/laravel-worker.log
```

### 5. Thông tin chi tiết
```bash
# Xem thông tin chi tiết một process
docker exec honestay_php_fpm supervisorctl pid laravel-worker

# Xem tất cả processes
docker exec honestay_php_fpm supervisorctl avail
```

## Troubleshooting

### 1. Service không khởi động
```bash
# Kiểm tra log supervisord
docker exec honestay_php_fpm tail -f /var/log/supervisor/supervisord.log

# Kiểm tra log service cụ thể
docker exec honestay_php_fpm tail -f /var/log/supervisor/laravel-worker.log

# Kiểm tra cấu hình syntax
docker exec honestay_php_fpm supervisord -c /etc/supervisor/conf.d/supervisord.conf -t
```

### 2. Process bị FATAL
```bash
# Thường do:
- Đường dẫn file không đúng
- Quyền truy cập không đủ  
- Command không hợp lệ
- Dependencies thiếu

# Debug:
docker exec honestay_php_fpm supervisorctl tail laravel-worker
```

### 3. Socket file không tồn tại
```bash
# Khởi động lại supervisord
docker exec honestay_php_fpm pkill supervisord
docker exec honestay_php_fpm supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

## Best Practices

### 1. Log Management
```properties
# Rotation logs tự động
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
stderr_logfile_maxbytes=50MB  
stderr_logfile_backups=10
```

### 2. Security
```properties
# Chạy với user có quyền thấp nhất
user=www

# Đặt chmod socket file
chmod=0700
```

### 3. Performance
```properties
# Queue worker với multiple processes
numprocs=4
process_name=%(program_name)s_%(process_num)02d

# Graceful shutdown
stopwaitsecs=3600
stopsignal=QUIT
```

### 4. Monitoring
```bash
# Script check health
#!/bin/bash
STATUS=$(docker exec honestay_php_fpm supervisorctl status | grep -v RUNNING)
if [ ! -z "$STATUS" ]; then
    echo "Some services are not running:"
    echo "$STATUS"
    # Send alert
fi
```

## Tích hợp với Laravel

### 1. Queue Configuration (config/queue.php)
```php
'connections' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

### 2. Schedule Definition (app/Console/Kernel.php)
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('inspire')->hourly();
    $schedule->command('queue:work --stop-when-empty')->everyMinute();
}
```

### 3. Job Example
```php
// Dispatch job
ProcessPayment::dispatch($payment);

// Job class
class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle()
    {
        // Process payment logic
    }
}
```

## Monitoring và Alerting

### 1. Health Check Script
```bash
#!/bin/bash
# File: /usr/local/bin/supervisor-health-check.sh

CONTAINER_NAME="honestay_php_fpm"
EXPECTED_SERVICES=("php-fpm" "laravel-worker" "laravel-schedule")

for service in "${EXPECTED_SERVICES[@]}"; do
    STATUS=$(docker exec $CONTAINER_NAME supervisorctl status $service | awk '{print $2}')
    if [ "$STATUS" != "RUNNING" ]; then
        echo "ALERT: $service is $STATUS"
        # Send notification (email, Slack, etc.)
    fi
done
```

### 2. Log Aggregation
```bash
# Collect all supervisor logs
docker exec honestay_php_fpm find /var/log/supervisor -name "*.log" -exec tail -f {} +
```

## Production Deployment

### 1. Environment Variables
```bash
# .env
SUPERVISOR_LOG_LEVEL=info
SUPERVISOR_NODAEMON=true
QUEUE_CONNECTION=redis
QUEUE_DRIVER=redis
```

### 2. Resource Limits
```properties
# supervisord.conf
[program:laravel-worker]
priority=999                    # Start order
startsecs=10                   # Thời gian chờ start thành công
startretries=3                 # Số lần retry khi start fail
exitcodes=0,2                  # Exit codes được coi là bình thường
```

### 3. Deployment Script
```bash
#!/bin/bash
# deploy.sh

# Update supervisor config
docker cp php-fpm/supervisord.conf honestay_php_fpm:/etc/supervisor/conf.d/

# Reload configuration
docker exec honestay_php_fpm supervisorctl reread
docker exec honestay_php_fpm supervisorctl update

# Restart workers to load new code
docker exec honestay_php_fpm supervisorctl restart laravel-worker:*

echo "Deployment completed successfully"
```

---

**Lưu ý:** Supervisor đã được cấu hình và chạy thành công trong container `honestay_php_fpm`. Tất cả các lệnh trên có thể sử dụng ngay để quản lý các services.
