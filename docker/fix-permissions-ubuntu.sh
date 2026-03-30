#!/bin/bash

# Script để fix quyền truy cập file/folder trên Ubuntu khi sử dụng Docker
# Sử dụng script này khi gặecho ""
echo "🚀 Next Steps:echo "🔧 Advanced Options:"
echo "   a) Fix permissions in running container: ./fix-permissions-ubuntu.sh --fix-container"
echo "   b) Clean rebuild everything: ./fix-permissions-ubuntu.sh --clean-rebuild"
echo "   c) Just fix host permissions: ./fix-permissions-ubuntu.sh --host-only"
echo "   g) Fix container umask permanently: ./fix-permissions-ubuntu.sh --fix-container-umask"ho "   1. Stop existing containers: docker compose down"
echo "   2. Rebuild containers with new user config: docker compose build --no-cache"
echo "   3. Start containers: docker compose up -d"
echo ""
echo "🔧 Advanced Options:"
echo "   a) Fix permissions in running container: ./fix-permissions-ubuntu.sh --fix-container"
echo "   b) Clean rebuild everything: ./fix-permissions-ubuntu.sh --clean-rebuild"
echo "   c) Just fix host permissions: ./fix-permissions-ubuntu.sh --host-only"
echo "   d) Set default umask for new files: ./fix-permissions-ubuntu.sh --set-umask"
echo "   e) Set 777 for full Docker access: ./fix-permissions-ubuntu.sh --set-777"
echo "   f) Setup automatic 777 for new files: ./fix-permissions-ubuntu.sh --auto-777"
echo "   g) Fix container umask permanently: ./fix-permissions-ubuntu.sh --fix-container-umask"
echo ""
echo "💡 For future file creation issues, run this script again or use:"
echo "   sudo chown -R \$(whoami):\$(whoami) /path/to/new/files"
echo ""
echo "⚠️  IMPORTANT: After this fix, all files will have 777 permissions!"
echo "   This provides maximum Docker compatibility but reduces security."
echo "   Only use this in development environments, not production."
echo ""on denied trên Ubuntu

echo "🔧 Fix Permissions for HoneStay Docker on Ubuntu"
echo "=============================================="

# Lấy thông tin user hiện tại
CURRENT_USER=$(whoami)
CURRENT_UID=$(id -u)
CURRENT_GID=$(id -g)

echo "📋 Current User Info:"
echo "   User: $CURRENT_USER"
echo "   UID: $CURRENT_UID" 
echo "   GID: $CURRENT_GID"

# Kiểm tra hệ điều hành
if [[ "$OSTYPE" != "linux-gnu"* ]]; then
    echo "⚠️  This script is designed for Ubuntu/Linux. Current OS: $OSTYPE"
    echo "   On macOS, permission issues are usually handled automatically."
    exit 1
fi

# Đường dẫn project
PROJECT_ROOT="$(dirname "$(pwd)")"
HOMESTAY_API_PATH="$PROJECT_ROOT/homestay-api"

echo ""
echo "📁 Project Paths:"
echo "   Project Root: $PROJECT_ROOT"
echo "   HomeStay API: $HOMESTAY_API_PATH"

# Kiểm tra và cảnh báo về vấn đề 777
echo ""
echo "🔍 Checking for permission issues..."
if find "$HOMESTAY_API_PATH" -type d -perm 777 2>/dev/null | head -1 | grep -q .; then
    echo "   ⚠️  Found directories with 777 permissions - this indicates Docker user mapping issues"
    echo "   🔧 This script will fix the root cause to avoid needing 777 permissions"
fi

# Cập nhật .env với UID/GID của user hiện tại
echo ""
echo "🔄 Updating Docker .env with current user UID/GID..."

# Backup .env hiện tại
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Cập nhật CONTAINER_USER_ID và CONTAINER_GROUP_ID
sed -i "s/CONTAINER_USER_ID=.*/CONTAINER_USER_ID=$CURRENT_UID/" .env
sed -i "s/CONTAINER_GROUP_ID=.*/CONTAINER_GROUP_ID=$CURRENT_GID/" .env

echo "   ✅ Updated CONTAINER_USER_ID=$CURRENT_UID"
echo "   ✅ Updated CONTAINER_GROUP_ID=$CURRENT_GID"

# Fix quyền cho thư mục hiện tại
echo ""
echo "🔒 Fixing current permissions..."

# Đặt quyền cho thư mục Docker
sudo chown -R $CURRENT_USER:$CURRENT_USER .
sudo chmod -R 777 .

# Đặt quyền cho thư mục HomeStay API
if [ -d "$HOMESTAY_API_PATH" ]; then
    echo "   🔧 Fixing HomeStay API permissions..."
    
    # Fix ownership cho toàn bộ project
    sudo chown -R $CURRENT_USER:$CURRENT_USER "$HOMESTAY_API_PATH"
    
    # Đặt quyền cơ bản 777 cho tất cả files và directories  
    sudo chmod -R 777 "$HOMESTAY_API_PATH"
    
    # Quyền 777 cho các thư mục Laravel cần ghi
    if [ -d "$HOMESTAY_API_PATH/storage" ]; then
        sudo chmod -R 777 "$HOMESTAY_API_PATH/storage"
        echo "   ✅ Fixed storage permissions (777)"
    fi
    
    if [ -d "$HOMESTAY_API_PATH/bootstrap/cache" ]; then
        sudo chmod -R 777 "$HOMESTAY_API_PATH/bootstrap/cache"
        echo "   ✅ Fixed bootstrap/cache permissions (777)"
    fi
    
    if [ -d "$HOMESTAY_API_PATH/database/migrations" ]; then
        sudo chmod -R 777 "$HOMESTAY_API_PATH/database/migrations"
        echo "   ✅ Fixed migrations permissions (777)"
    fi
    
    # Fix quyền cho vendor nếu có
    if [ -d "$HOMESTAY_API_PATH/vendor" ]; then
        sudo chmod -R 777 "$HOMESTAY_API_PATH/vendor"
        echo "   ✅ Fixed vendor permissions (777)"
    fi
    
    # Fix tất cả directories lên quyền 777 cho Docker access
    echo "   � Setting all directories to 777 permissions..."
    find "$HOMESTAY_API_PATH" -type d -exec sudo chmod 777 {} \; 2>/dev/null || true
    
    # Fix tất cả files lên quyền 777 cho Docker access
    echo "   🔓 Setting all files to 777 permissions..."
    find "$HOMESTAY_API_PATH" -type f -exec sudo chmod 777 {} \; 2>/dev/null || true
    
    echo "   ✅ All files and directories now have 777 permissions for Docker"
    
else
    echo "   ⚠️  HomeStay API directory not found: $HOMESTAY_API_PATH"
fi

# Đặt quyền cho log và data directories
echo "   🔧 Fixing Docker data and log permissions..."
sudo chown -R $CURRENT_USER:$CURRENT_USER ./data ./logs 2>/dev/null || true
sudo chmod -R 777 ./data ./logs 2>/dev/null || true

echo ""
echo "🚀 Next Steps:"
echo "   1. Stop existing containers: docker compose down"
echo "   2. Rebuild containers with new user config: docker compose build --no-cache"
echo "   3. Start containers: docker compose up -d"
echo ""
echo "� Advanced Options:"
echo "   a) Fix permissions in running container: ./fix-permissions-ubuntu.sh --fix-container"
echo "   b) Clean rebuild everything: ./fix-permissions-ubuntu.sh --clean-rebuild"
echo "   c) Just fix host permissions: ./fix-permissions-ubuntu.sh --host-only"
echo ""
echo "�💡 For future file creation issues, run this script again or use:"
echo "   sudo chown -R \$(whoami):\$(whoami) /path/to/new/files"
echo ""

# Handle advanced options
case "${1:-}" in
    --fix-container)
        echo "🐳 Fixing permissions in running container..."
        if docker ps | grep -q honestay_php_fpm; then
            docker exec honestay_php_fpm chown -R www:www /var/www/html/storage 2>/dev/null || true
            docker exec honestay_php_fpm chmod -R 777 /var/www/html/storage 2>/dev/null || true
            docker exec honestay_php_fpm chmod -R 777 /var/www/html/bootstrap/cache 2>/dev/null || true
            echo "   ✅ Fixed container permissions"
        else
            echo "   ⚠️  Container honestay_php_fpm is not running"
        fi
        ;;
    --clean-rebuild)
        echo "🧹 Clean rebuild containers..."
        docker compose down
        docker system prune -f --volumes
        docker compose build --no-cache
        docker compose up -d
        echo "   ✅ Clean rebuild completed"
        ;;
    --host-only)
        echo "   ✅ Host permissions fixed only (no container rebuild needed)"
        ;;
    --set-umask)
        echo "🔧 Setting umask for 777 permissions on new files..."
        # Add umask to user's shell config for 777 permissions
        if ! grep -q "umask 000" ~/.bashrc 2>/dev/null; then
            echo "umask 000" >> ~/.bashrc
            echo "   ✅ Added umask 000 to ~/.bashrc (new files will have 666, directories 777)"
        fi
        if ! grep -q "umask 000" ~/.profile 2>/dev/null; then
            echo "umask 000" >> ~/.profile
            echo "   ✅ Added umask 000 to ~/.profile"
        fi
        echo "   💡 Reload shell or run: source ~/.bashrc"
        ;;
    --set-777)
        echo "🔓 Setting 777 permissions for full Docker access..."
        if [ -d "$HOMESTAY_API_PATH" ]; then
            # Set ownership trước
            sudo chown -R $CURRENT_USER:$CURRENT_USER "$HOMESTAY_API_PATH"
            
            # Set 777 cho toàn bộ project
            sudo chmod -R 777 "$HOMESTAY_API_PATH"
            
            echo "   ✅ Set 777 permissions for entire Docker workspace"
            echo "   💡 All files and directories now have full access (rwxrwxrwx)"
        else
            echo "   ⚠️  HomeStay API directory not found: $HOMESTAY_API_PATH"
        fi
        ;;
    --auto-777)
        echo "🤖 Setting up SMART automatic 777 permissions for new files..."
        
        # 1. Set umask (nhanh)
        echo "   📝 Configuring umask for automatic permissions..."
        
        if ! grep -q "umask 000" ~/.bashrc 2>/dev/null; then
            echo "" >> ~/.bashrc
            echo "# Auto 777 permissions for Docker workspace" >> ~/.bashrc
            echo "umask 000" >> ~/.bashrc
            echo "   ✅ Added umask 000 to ~/.bashrc"
        fi
        
        if ! grep -q "umask 000" ~/.profile 2>/dev/null; then
            echo "umask 000" >> ~/.profile
            echo "   ✅ Added umask 000 to ~/.profile"
        fi
        
        umask 000
        echo "   ✅ Set umask 000 for current session"
        
        # 2. Fix quyền CHỈ CHO CÁC THỦ MỤC QUAN TRỌNG (nhanh)
        echo "   🎯 Fixing permissions for essential directories only..."
        
        if [ -d "$HOMESTAY_API_PATH" ]; then
            # Chỉ fix ownership cho root folder (nhanh)
            sudo chown $CURRENT_USER:$CURRENT_USER "$HOMESTAY_API_PATH"
            sudo chmod 777 "$HOMESTAY_API_PATH"
            
            # Danh sách thư mục QUAN TRỌNG cần 777 (loại bỏ vendor, node_modules)
            ESSENTIAL_DIRS=(
                "$HOMESTAY_API_PATH/storage"
                "$HOMESTAY_API_PATH/bootstrap/cache" 
                "$HOMESTAY_API_PATH/database/migrations"
                "$HOMESTAY_API_PATH/database/seeders"
                "$HOMESTAY_API_PATH/app"
                "$HOMESTAY_API_PATH/config"
                "$HOMESTAY_API_PATH/routes"
                "$HOMESTAY_API_PATH/resources"
                "$HOMESTAY_API_PATH/public"
            )
            
            for dir in "${ESSENTIAL_DIRS[@]}"; do
                if [ -d "$dir" ]; then
                    echo "     🔧 $dir"
                    sudo chown -R $CURRENT_USER:$CURRENT_USER "$dir"
                    sudo chmod -R 777 "$dir"
                fi
            done
            
            # Fix root files (không recursive, chỉ files ở root)
            find "$HOMESTAY_API_PATH" -maxdepth 1 -type f -exec sudo chmod 777 {} \; 2>/dev/null || true
            
            echo "   ✅ Fixed essential directories only (skipped vendor/node_modules for speed)"
        fi
        
        # 3. Tạo script monitor THÔNG MINH (chỉ check essential dirs)
        echo "   🔧 Creating smart auto-777 monitor script..."
        cat > ~/auto-777-monitor.sh << 'EOF'
#!/bin/bash
# Smart monitor script - chỉ check essential directories

# Auto-detect project path
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HOMESTAY_PATH="$(dirname "$SCRIPT_DIR")"

if [ ! -d "$HOMESTAY_PATH/homestay-api" ]; then
    for path in "$HOME/Documents/Homestay" "$HOME/homestay" "$HOME/Homestay" "/Users/macos/Documents/Homestay"; do
        if [ -d "$path/homestay-api" ]; then
            HOMESTAY_PATH="$path"
            break
        fi
    done
fi

# CHỈ monitor essential directories (nhanh)
WATCH_DIRS=(
    "$HOMESTAY_PATH/homestay-api/storage"
    "$HOMESTAY_PATH/homestay-api/bootstrap/cache"  
    "$HOMESTAY_PATH/homestay-api/database"
    "$HOMESTAY_PATH/homestay-api/app"
    "$HOMESTAY_PATH/homestay-api/config"
    "$HOMESTAY_PATH/homestay-api/routes"
    "$HOMESTAY_PATH/homestay-api/public"
)

# Smart fix function - chỉ fix files mới (modified trong 5 phút)
fix_new_files() {
    local fixed_count=0
    
    for dir in "${WATCH_DIRS[@]}"; do
        if [ -d "$dir" ]; then
            # Chỉ fix files modified trong 5 phút gần đây (nhanh hơn)
            while IFS= read -r -d '' file; do
                if [ ! -w "$file" ] || [ ! -x "$file" ]; then
                    chmod 777 "$file" 2>/dev/null && ((fixed_count++))
                fi
            done < <(find "$dir" -type f -newermt '5 minutes ago' -print0 2>/dev/null)
            
            # Fix directories nếu cần
            while IFS= read -r -d '' dir_path; do
                if [ ! -w "$dir_path" ] || [ ! -x "$dir_path" ]; then
                    chmod 777 "$dir_path" 2>/dev/null && ((fixed_count++))
                fi
            done < <(find "$dir" -type d -newermt '5 minutes ago' -print0 2>/dev/null)
        fi
    done
    
    if [ $fixed_count -gt 0 ]; then
        echo "$(date): Fixed $fixed_count new files/directories" >> /tmp/auto-777.log
    fi
}

fix_new_files
EOF
        
        chmod +x ~/auto-777-monitor.sh
        echo "   ✅ Created smart monitor (only checks recent files)"
        
        # 4. Setup cron job (giữ nguyên)
        echo "   📅 Setting up cron job..."
        (crontab -l 2>/dev/null; echo "*/1 * * * * $HOME/auto-777-monitor.sh") | crontab -
        echo "   ✅ Added cron job (runs every minute)"
        
        # 5. Tạo aliases THÔNG MINH
        if ! grep -q "alias fix777" ~/.bashrc 2>/dev/null; then
            echo "" >> ~/.bashrc
            echo "# Smart 777 aliases" >> ~/.bashrc  
            echo "alias fix777='sudo chmod -R 777'" >> ~/.bashrc
            echo "alias own777='sudo chown -R \$(whoami):\$(whoami) . && sudo chmod -R 777 .'" >> ~/.bashrc
            echo "alias homestay777-essential='sudo chmod -R 777 \"$HOMESTAY_API_PATH/storage\" \"$HOMESTAY_API_PATH/bootstrap/cache\" \"$HOMESTAY_API_PATH/database\" \"$HOMESTAY_API_PATH/app\"'" >> ~/.bashrc
            echo "alias homestay777-full='sudo chmod -R 777 \"$HOMESTAY_API_PATH\"'" >> ~/.bashrc
            echo "alias homestay777='homestay777-essential'" >> ~/.bashrc  # Mặc định dùng essential
            echo "alias homestay777-monitor='bash ~/auto-777-monitor.sh'" >> ~/.bashrc
            echo "alias homestay777-log='tail -f /tmp/auto-777.log'" >> ~/.bashrc
            echo "   ✅ Added smart aliases (homestay777 = essential only, homestay777-full = everything)"
        fi
        
        echo ""
        echo "🎯 SMART 777 SETUP COMPLETED!"
        echo "   ✅ umask 000 configured"
        echo "   ✅ Essential directories fixed (fast)"
        echo "   ✅ Smart monitor created (only checks recent files)"  
        echo "   ✅ Cron job scheduled"
        echo "   ✅ Smart aliases added"
        echo ""
        echo "💡 Available commands:"
        echo "   homestay777           # Fix essential dirs only (FAST)"
        echo "   homestay777-full      # Fix entire project (SLOW)"
        echo "   homestay777-monitor   # Manual check"
        echo "   homestay777-log       # View log"
        echo ""
        echo "🚀 To apply: source ~/.bashrc"
        ;;
    --fix-container-umask)
        echo "🐳 PERMANENT FIX: Setting up container umask for 777 permissions..."
        
        # Kiểm tra container có chạy không
        if ! docker ps | grep -q honestay_php_fpm; then
            echo "   ⚠️  Container honestay_php_fpm is not running"
            echo "   💡 Start container first: docker compose up -d"
            exit 1
        fi
        
        # 1. Fix umask trong container PHP-FPM
        echo "   🔧 Setting umask 000 in PHP-FPM container..."
        docker exec honestay_php_fpm bash -c "
            # Set umask cho root user trong container
            echo 'umask 000' >> /root/.bashrc
            echo 'umask 000' >> /root/.profile
            
            # Set umask cho www-data user nếu có
            if id -u www-data >/dev/null 2>&1; then
                echo 'umask 000' >> /home/www-data/.bashrc 2>/dev/null || true
                echo 'umask 000' >> /home/www-data/.profile 2>/dev/null || true
            fi
            
            # Set umask cho session hiện tại
            umask 000
            
            echo 'Container umask configured!'
        "
        
        # 2. Tạo script startup trong container
        echo "   📝 Creating startup script for container..."
        docker exec honestay_php_fpm bash -c "
            cat > /usr/local/bin/setup-777-umask.sh << 'EOF'
#!/bin/bash
# Auto-setup umask 000 cho tất cả users trong container
umask 000
export UMASK=000

# Set quyền cho files mới tạo bởi PHP
if [ -f /usr/local/etc/php/php.ini ]; then
    # Đảm bảo PHP tạo files với quyền cao
    sed -i 's/;session.save_path.*/session.save_path = \"\/tmp\"/' /usr/local/etc/php/php.ini 2>/dev/null || true
fi

# Set quyền cho Laravel artisan
if [ -d /var/www/html ]; then
    find /var/www/html -type d -exec chmod 777 {} \; 2>/dev/null || true
    find /var/www/html -name '*.php' -exec chmod 777 {} \; 2>/dev/null || true
fi

echo 'Container 777 umask setup completed'
EOF

            chmod +x /usr/local/bin/setup-777-umask.sh
            echo 'Startup script created!'
        "
        
        # 3. Fix quyền cho files hiện có trong container
        echo "   🔒 Fixing existing files in container..."
        docker exec honestay_php_fpm bash -c "
            umask 000
            
            # Fix quyền cho thư mục Laravel quan trọng
            if [ -d /var/www/html ]; then
                find /var/www/html/storage -type d -exec chmod 777 {} \; 2>/dev/null || true
                find /var/www/html/storage -type f -exec chmod 777 {} \; 2>/dev/null || true
                
                find /var/www/html/bootstrap/cache -type d -exec chmod 777 {} \; 2>/dev/null || true
                find /var/www/html/bootstrap/cache -type f -exec chmod 777 {} \; 2>/dev/null || true
                
                find /var/www/html/database -type d -exec chmod 777 {} \; 2>/dev/null || true
                find /var/www/html/database -type f -exec chmod 777 {} \; 2>/dev/null || true
                
                find /var/www/html/app -type d -exec chmod 777 {} \; 2>/dev/null || true
                find /var/www/html/app -type f -exec chmod 777 {} \; 2>/dev/null || true
                
                echo 'Fixed existing files permissions'
            fi
        "
        
        # 4. Tạo aliases cho container commands
        echo "   🎯 Setting up container command aliases..."
        if ! grep -q "alias artisan-777" ~/.bashrc 2>/dev/null; then
            echo "" >> ~/.bashrc
            echo "# Docker container 777 aliases" >> ~/.bashrc
            echo "alias artisan-777='docker exec honestay_php_fpm bash -c \"umask 000 && cd /var/www/html && php artisan \$*\"'" >> ~/.bashrc
            echo "alias composer-777='docker exec honestay_php_fpm bash -c \"umask 000 && cd /var/www/html && composer \$*\"'" >> ~/.bashrc
            echo "alias php-777='docker exec honestay_php_fpm bash -c \"umask 000 && cd /var/www/html && php \$*\"'" >> ~/.bashrc
            echo "alias container-777='docker exec -it honestay_php_fpm bash -c \"umask 000 && /usr/local/bin/setup-777-umask.sh && bash\"'" >> ~/.bashrc
            echo "   ✅ Added container 777 aliases"
        fi
        
        # 5. Test tạo file trong container
        echo "   🧪 Testing container file creation..."
        docker exec honestay_php_fpm bash -c "
            umask 000
            cd /var/www/html
            
            # Test tạo file PHP
            echo '<?php echo \"Test 777 permissions\"; ?>' > test-777-container.php
            
            # Kiểm tra quyền
            PERM=\$(stat -c '%a' test-777-container.php 2>/dev/null || echo 'unknown')
            echo \"Test file permission: \$PERM\"
            
            # Xóa test file
            rm -f test-777-container.php
        "
        
        echo ""
        echo "🎯 CONTAINER UMASK SETUP COMPLETED!"
        echo "   ✅ Container umask 000 configured permanently"
        echo "   ✅ Startup script created for container"
        echo "   ✅ Existing files fixed to 777"
        echo "   ✅ Container command aliases added"
        echo ""
        echo "💡 Available container commands:"
        echo "   artisan-777 make:controller TestController  # Tạo controller với 777"
        echo "   composer-777 require package-name          # Install package với 777" 
        echo "   php-777 script.php                         # Chạy PHP với 777"
        echo "   container-777                              # Vào container với 777 setup"
        echo ""
        echo "🔄 Files created in container will now automatically have 777 permissions!"
        echo "🚀 To apply aliases: source ~/.bashrc"
        ;;
    *)
        echo "   ✅ Host permissions fixed - rebuild containers when ready"
        ;;
esac

echo "✅ Permission fix completed!"
