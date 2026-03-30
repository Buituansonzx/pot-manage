#!/bin/sh
set -e

echo "Starting Redis configuration..."

# Process Redis configuration template
if [ -f "/etc/redis/redis.conf.template" ]; then
    echo "Processing Redis configuration template..."
    envsubst < /etc/redis/redis.conf.template > /etc/redis/redis.conf
    echo "Redis configuration generated successfully"
    
    # Set proper permissions
    chown redis:redis /etc/redis/redis.conf
    chmod 644 /etc/redis/redis.conf
fi

# Create necessary directories
mkdir -p /var/log/redis /var/run/redis
chown -R redis:redis /var/log/redis /var/run/redis /data
chmod 755 /var/log/redis /var/run/redis

# Set timezone
echo "Setting timezone to: $TZ"
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime
echo $TZ > /etc/timezone

# Handle password configuration
if [ -n "$REDIS_PASSWORD" ] && [ "$REDIS_PASSWORD" != "" ]; then
    echo "Configuring Redis with password protection..."
    echo "requirepass $REDIS_PASSWORD" >> /etc/redis/redis.conf
else
    echo "Redis running without password (development mode)"
fi

# Create backup script
cat > /usr/local/bin/redis-backup.sh << 'EOF'
#!/bin/sh
# Redis backup script
BACKUP_DIR="/var/backups/redis"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/redis_backup_$DATE.rdb"

echo "Creating Redis backup: $BACKUP_FILE"
redis-cli --rdb $BACKUP_FILE

# Keep only last 7 backups
find $BACKUP_DIR -name "redis_backup_*.rdb" -mtime +7 -delete

echo "Redis backup completed: $BACKUP_FILE"
EOF

chmod +x /usr/local/bin/redis-backup.sh

echo "Redis startup configuration completed"

# Switch to redis user and execute Redis
exec su-exec redis "$@"
