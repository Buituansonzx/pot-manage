#!/bin/bash
set -e

echo "Starting MySQL configuration..."

# Process MySQL configuration template
if [ -f "/etc/mysql/conf.d/my.cnf.template" ]; then
    echo "Processing MySQL configuration template..."
    envsubst < /etc/mysql/conf.d/my.cnf.template > /etc/mysql/conf.d/my.cnf
    echo "MySQL configuration generated successfully"
fi

# Create log directory if it doesn't exist
mkdir -p /var/log/mysql
chown -R mysql:mysql /var/log/mysql

# Set timezone
echo "Setting timezone to: $TZ"
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime
echo $TZ > /etc/timezone

# Set MySQL timezone
mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql 2>/dev/null || echo "Timezone data already loaded"

echo "MySQL startup configuration completed"

# Execute the original docker entrypoint
exec /usr/local/bin/docker-entrypoint.sh "$@"
