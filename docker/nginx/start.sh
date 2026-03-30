#!/bin/sh

# Process nginx configuration templates with environment variables
echo "Processing Nginx configuration templates..."

# Process main nginx.conf (let envsubst process all variables)
envsubst < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Default.conf is already static file, no need to process

# Test nginx configuration
echo "Testing Nginx configuration..."
nginx -t

if [ $? -eq 0 ]; then
    echo "Nginx configuration is valid. Starting Nginx..."
    exec "$@"
else
    echo "Nginx configuration test failed!"
    exit 1
fi
