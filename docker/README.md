# HoneStay Docker Configuration

Docker configuration for HoneStay Homestay Booking Platform API.

## 🐳 Services

This Docker Compose setup includes:

- **Nginx** - Web server and reverse proxy
- **PHP-FPM** - PHP application server  
- **MySQL 8.0** - Database server
- **Redis 7.0** - Cache and session store

## 🚀 Quick Start

### Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Git

### 1. Clone this repository

```bash
git clone git@github.com:homestays/docker.git
cd docker
```

### 2. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Edit .env file with your specific configurations
nano .env
```

### 3. Start Services

```bash
# Build and start all services
docker-compose up -d --build

# Check services status
docker-compose ps
```

## 📁 Structure

```
docker/
├── docker-compose.yml          # Main Docker Compose configuration
├── .env.example               # Environment variables template
├── .gitignore                 # Git ignore rules
├── nginx/                     # Nginx configuration
│   ├── Dockerfile
│   ├── nginx.conf.template
│   ├── default.conf.template
│   └── start.sh
├── php-fpm/                   # PHP-FPM configuration
│   ├── Dockerfile
│   ├── php.ini.template
│   ├── www.conf.template
│   ├── supervisord.conf
│   └── start.sh
├── mysql/                     # MySQL configuration
│   ├── Dockerfile
│   ├── my.cnf.template
│   ├── start.sh
│   └── init/                  # Initialization scripts
└── redis/                     # Redis configuration
    ├── Dockerfile
    ├── redis.conf.template
    └── start.sh
```

## 🔧 Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Application Code Path
APP_CODE_PATH_HOST=../homestay-api

# Database Configuration
MYSQL_DATABASE=honestay
MYSQL_USER=honestay
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=root

# Timezone
WORKSPACE_TIMEZONE=UTC

# Ports
NGINX_HOST_HTTP_PORT=80
NGINX_HOST_HTTPS_PORT=443
MYSQL_PORT=3306
REDIS_PORT=6379
```

### Service Ports

| Service | Internal Port | External Port | Description |
|---------|--------------|---------------|-------------|
| Nginx | 80, 443 | 80, 443 | Web server |
| PHP-FPM | 9000 | - | Application server |
| MySQL | 3306 | 3306 | Database |
| Redis | 6379 | 6379 | Cache |

## 🛠 Docker Commands

```bash
# Start services
docker-compose up -d

# Stop services  
docker-compose down

# Rebuild services
docker-compose up -d --build

# View logs
docker-compose logs -f [service_name]

# Access containers
docker exec -it honestay_nginx bash
docker exec -it honestay_php_fpm bash
docker exec -it honestay_mysql bash
docker exec -it honestay_redis redis-cli
```

## 📊 Monitoring

### Health Checks

All services include health checks:

```bash
# Check all services health
docker-compose ps

# Check specific service logs
docker-compose logs [service_name]
```

### Resource Usage

```bash
# Monitor resource usage
docker stats

# View container details
docker inspect [container_name]
```

## 🔍 Troubleshooting

### Common Issues

**1. Port Conflicts**
```bash
# Check if ports are in use
lsof -i :80
lsof -i :3306
lsof -i :6379
```

**2. Permission Issues**
```bash
# Fix file permissions
chmod +x nginx/start.sh
chmod +x php-fpm/start.sh
chmod +x mysql/start.sh
chmod +x redis/start.sh
```

**3. Container Won't Start**
```bash
# Check container logs
docker-compose logs [service_name]

# Rebuild specific service
docker-compose up -d --build [service_name]
```

**4. Database Connection Issues**
```bash
# Check MySQL logs
docker-compose logs mysql

# Test database connection
docker exec -it honestay_mysql mysql -u root -p
```

## 🚀 Production Deployment

For production deployment:

1. Update `.env` with production values
2. Use proper SSL certificates for Nginx
3. Set strong database passwords
4. Configure proper backup strategies
5. Set up monitoring and logging

## 📝 Notes

- This configuration is optimized for development
- For production, additional security measures should be implemented
- Database data is persisted in `data/mysql/` directory
- Logs are stored in `logs/` directory

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is licensed under the MIT License.
