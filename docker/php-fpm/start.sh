#!/bin/bash
set -e

echo "Starting PHP-FPM configuration..."

# Set umask 000 for 777 permissions on new files
echo "Setting umask 000 for automatic 777 permissions..."
umask 000
export UMASK=000

# Run 777 permissions setup script
if [ -f "/usr/local/bin/setup-777-permissions.sh" ]; then
    echo "Running 777 permissions setup..."
    /usr/local/bin/setup-777-permissions.sh
fi

# Disable zz-docker.conf that overrides our listen configuration
if [ -f "/usr/local/etc/php-fpm.d/zz-docker.conf" ]; then
    echo "Disabling zz-docker.conf to prevent listen override..."
    mv /usr/local/etc/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf.disabled
fi

# Xử lý các template cấu hình
# QUAN TRỌNG: Xử lý php.ini.template và tạo ra custom.ini
# File custom.ini chứa cài đặt OpCode cache cho development
# validate_timestamps=1 đảm bảo code thay đổi được phản ánh ngay lập tức mà không cần restart
if [ -f "/usr/local/etc/php/php.ini.template" ]; then
    echo "Processing PHP configuration template..."
    envsubst < /usr/local/etc/php/php.ini.template > /usr/local/etc/php/conf.d/custom.ini
    echo "PHP configuration generated successfully"
fi

if [ -f "/usr/local/etc/php-fpm.d/www.conf.template" ]; then
    echo "Processing PHP-FPM configuration template..."
    envsubst < /usr/local/etc/php-fpm.d/www.conf.template > /usr/local/etc/php-fpm.d/www.conf
    echo "PHP-FPM configuration generated successfully"
fi

# Fix permissions for new files/folders - SET 777 for Docker compatibility
echo "Setting 777 permissions for Docker compatibility..."

# Tạo các thư mục cần thiết nếu chưa có với umask 000
umask 000
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

# Đặt ownership cho các thư mục quan trọng (chỉ nếu có quyền)
if [ "$(id -u)" = "0" ]; then
    # Nếu chạy với quyền root, đặt ownership
    chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
    echo "Fixed ownership for storage and cache directories"
else
    echo "Running as non-root user, skipping ownership changes"
fi

# Đặt quyền 777 cho các thư mục Laravel để tương thích với Docker
chmod -R 777 /var/www/html/storage 2>/dev/null || true
chmod -R 777 /var/www/html/bootstrap/cache 2>/dev/null || true

# Fix passport OAuth keys permissions that require strict 600 or 660 instead of 777
chmod 600 /var/www/html/storage/oauth-*.key 2>/dev/null || true

echo "777 permissions applied for Docker compatibility, and 600 for Passport keys"

echo "Permission setup completed"

# Ensure umask 000 is set for PHP-FPM process
echo "Setting final umask 000 for PHP-FPM process..."
umask 000

# Check if Supervisor should be started
if [ "$INSTALL_SUPERVISOR" = "true" ]; then
    echo "Starting services with Supervisor..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
else
    echo "Starting PHP-FPM with umask 000 for 777 permissions..."
    exec php-fpm
fi
