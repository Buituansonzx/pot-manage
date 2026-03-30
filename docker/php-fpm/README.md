# Cấu hình PHP-FPM cho Môi trường Development

## Cài đặt OpCode Cache

### Vấn đề đã được giải quyết: Code thay đổi không được phản ánh ngay lập tức

**Vấn đề**: Khi thay đổi code trong Laravel controllers, API không phản ánh thay đổi ngay lập tức. Phải restart server mới thấy code mới.

**Nguyên nhân gốc**: OpCode cache được cấu hình sai cho môi trường development:
- `opcache.validate_timestamps = 0` (Sai - không kiểm tra file thay đổi)
- Điều này khiến PHP cache bytecode đã compile mà không kiểm tra file source có thay đổi hay không

**Giải pháp**: Cấu hình OpCode cache cho development hot-reload:
```ini
opcache.validate_timestamps = 1  # QUAN TRỌNG: Phải là 1 để dev hot-reload
opcache.revalidate_freq = 0      # QUAN TRỌNG: 0 = kiểm tra thay đổi ngay lập tức
```

### Cách hoạt động của cấu hình

1. **Xử lý Template**: `start.sh` xử lý `php.ini.template` → tạo ra `custom.ini`
2. **Load cấu hình**: PHP load config từ `/usr/local/etc/php/conf.d/custom.ini`
3. **Hot Reload**: Với cài đặt đúng, PHP kiểm tra source files mỗi request

### Development vs Production

**Development** (cài đặt hiện tại):
- `opcache.validate_timestamps = 1` - Kiểm tra file thay đổi
- `opcache.revalidate_freq = 0` - Kiểm tra ngay lập tức
- Kết quả: Code thay đổi được phản ánh ngay lập tức

**Production** (khuyến nghị):
- `opcache.validate_timestamps = 0` - Không kiểm tra files (hiệu suất tốt hơn)
- `opcache.revalidate_freq = 60` - Cache trong 60 giây
- Kết quả: Hiệu suất tốt hơn, nhưng cần deploy để thấy thay đổi

### Khắc phục sự cố

Nếu code thay đổi không được phản ánh:
1. Kiểm tra trạng thái OpCode cache: `docker exec container_name php -i | grep opcache`
2. Xác minh cài đặt: `validate_timestamps = On` và `revalidate_freq = 0`
3. Restart container nếu cần: `docker-compose restart php-fpm`

### Các file liên quan
- `php.ini.template` - Template cấu hình với environment variables
- `start.sh` - Xử lý template và tạo cấu hình cuối cùng
- `Dockerfile` - Copy templates và setup container
- `custom.ini` - Cấu hình cuối cùng được tạo ra (bên trong container)
