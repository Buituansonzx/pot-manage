#!/bin/bash

echo "🧹 Cleaning up Docker space on Ubuntu..."

# Check current disk space
echo "📊 Current disk usage:"
df -h

echo ""
echo "📊 Docker directory size:"
du -sh /var/lib/docker 2>/dev/null || echo "Unable to check Docker directory"

echo ""
echo "🗑️  Cleaning Docker system..."

# Stop all containers
echo "   Stopping all containers..."
docker stop $(docker ps -aq) 2>/dev/null || true

# Remove all containers
echo "   Removing all containers..."
docker rm $(docker ps -aq) 2>/dev/null || true

# Remove all images
echo "   Removing all images..."
docker rmi $(docker images -q) 2>/dev/null || true

# Comprehensive cleanup
echo "   Running system prune..."
docker system prune -af --volumes

# Clean build cache
echo "   Cleaning build cache..."
docker builder prune -af

echo ""
echo "🧽 Cleaning system files..."

# Clean apt cache
sudo apt-get clean
sudo apt-get autoclean
sudo apt-get autoremove -y

# Clean temp files
sudo rm -rf /tmp/*
sudo rm -rf /var/tmp/*

# Clean logs
sudo journalctl --vacuum-time=3d

echo ""
echo "📊 Disk usage after cleanup:"
df -h

echo ""
echo "✅ Cleanup completed!"
echo ""
echo "💡 Now try rebuilding with:"
echo "   docker compose build --no-cache php-fpm"
