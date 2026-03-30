# 🐳 Docker Auto-777 Permissions Setup

## 📋 **Cấu hình hoàn thành trong Docker**

Container PHP-FPM đã được cấu hình để **tự động tạo files với quyền 777**:

### **🔧 Các thay đổi đã thực hiện:**

#### **1. Dockerfile Updates:**
- ✅ Set `umask 000` trong `.bashrc` và `.profile` cho tất cả users
- ✅ Thêm environment variable `UMASK=000`
- ✅ Tạo script `/usr/local/bin/setup-777-permissions.sh`
- ✅ Cấu hình automatic 777 permissions

#### **2. start.sh Updates:**
- ✅ Set `umask 000` khi container khởi động
- ✅ Chạy setup-777-permissions script
- ✅ Set chmod 777 cho storage và cache directories
- ✅ Đảm bảo PHP-FPM process chạy với umask 000

## 🚀 **Cách rebuild container:**

### **Bước 1: Stop containers hiện tại**
```bash
cd ~/homestay/docker
docker compose down
```

### **Bước 2: Rebuild container với cấu hình mới**
```bash
# Clean rebuild để áp dụng Dockerfile changes
docker compose build --no-cache php-fpm

# Hoặc rebuild tất cả services
docker compose build --no-cache
```

### **Bước 3: Start containers**
```bash
docker compose up -d
```

### **Bước 4: Verify setup**
```bash
# Check umask trong container
docker exec honestay_php_fpm bash -c "umask"
# Should return: 0000

# Test tạo file mới
docker exec honestay_php_fpm bash -c "cd /var/www/html && touch test-auto-777.txt"

# Check permissions
docker exec honestay_php_fpm bash -c "ls -la /var/www/html/test-auto-777.txt"
# Should show: -rw-rw-rw- (666) or -rwxrwxrwx (777)

# Clean up test file
docker exec honestay_php_fpm bash -c "rm -f /var/www/html/test-auto-777.txt"
```

## ✅ **Kết quả sau khi rebuild:**

### **Files mới tự động có quyền cao:**
```bash
# Artisan commands sẽ tạo files với 777
docker exec honestay_php_fpm php artisan make:controller TestController
# File sẽ có quyền 777 tự động!

# Composer install sẽ tạo files với quyền cao
docker exec honestay_php_fpm composer install
# Vendor files sẽ có quyền phù hợp

# Laravel cache/logs sẽ có quyền 777
docker exec honestay_php_fpm php artisan cache:clear
# Cache files sẽ có quyền 777
```

### **Storage và Cache directories:**
- ✅ `/var/www/html/storage/` → 777 permissions
- ✅ `/var/www/html/bootstrap/cache/` → 777 permissions  
- ✅ Files mới trong các thư mục này → 777 permissions

## 🎯 **Lợi ích:**

1. **Automatic 777**: Files mới tự động có quyền 777
2. **No manual fix needed**: Không cần chạy chmod thủ công
3. **Laravel compatible**: Hoạt động hoàn hảo với artisan commands
4. **Development friendly**: Tối ưu cho môi trường development

## 🔧 **Troubleshooting:**

### **Nếu vẫn gặp vấn đề permissions:**
```bash
# 1. Check container umask
docker exec honestay_php_fpm bash -c "umask"

# 2. Check environment variables
docker exec honestay_php_fpm bash -c "env | grep UMASK"

# 3. Manual fix nếu cần
docker exec honestay_php_fpm bash -c "chmod -R 777 /var/www/html/storage"

# 4. Restart container
docker compose restart php-fpm
```

### **Verify Dockerfile changes applied:**
```bash
# Check if setup script exists
docker exec honestay_php_fpm ls -la /usr/local/bin/setup-777-permissions.sh

# Check bashrc contains umask
docker exec honestay_php_fpm cat /root/.bashrc | grep umask
```

## ⚠️ **Important Notes:**

- **Development only**: Chỉ dùng cho development environment
- **Security**: Không dùng 777 permissions trong production
- **Rebuild required**: Cần rebuild container để áp dụng changes từ Dockerfile

## 🎉 **Thành công!**

Sau khi rebuild, container sẽ tự động:
- ✅ Set umask 000 khi khởi động
- ✅ Tạo files mới với quyền 777
- ✅ Fix permissions cho Laravel directories
- ✅ Không cần intervention thủ công

**Your Docker container is now configured for automatic 777 permissions!** 🚀
