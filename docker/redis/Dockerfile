# Redis Dockerfile for HoneStay Application
ARG REDIS_VERSION=alpine
FROM redis:${REDIS_VERSION}

# Set timezone
ARG TIMEZONE=Asia/Ho_Chi_Minh
ENV TZ=${TIMEZONE}

# Redis Configuration Arguments
ARG REDIS_PORT=6379
ARG REDIS_PASSWORD=""
ARG REDIS_MAXMEMORY=256mb
ARG REDIS_MAXMEMORY_POLICY=allkeys-lru
ARG REDIS_SAVE_INTERVAL="900 1 300 10 60 10000"
ARG REDIS_LOG_LEVEL=notice
ARG REDIS_DATABASES=16
ARG REDIS_TIMEOUT=300

# Set Redis runtime environment variables
ENV REDIS_PORT=${REDIS_PORT}
ENV REDIS_PASSWORD=${REDIS_PASSWORD}
ENV REDIS_MAXMEMORY=${REDIS_MAXMEMORY}
ENV REDIS_MAXMEMORY_POLICY=${REDIS_MAXMEMORY_POLICY}
ENV REDIS_SAVE_INTERVAL=${REDIS_SAVE_INTERVAL}
ENV REDIS_LOG_LEVEL=${REDIS_LOG_LEVEL}
ENV REDIS_DATABASES=${REDIS_DATABASES}
ENV REDIS_TIMEOUT=${REDIS_TIMEOUT}

# Install necessary packages
RUN apk add --no-cache \
    gettext \
    tzdata \
    su-exec

# Set timezone in container
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Create redis user with specific ID (if needed)
ARG CONTAINER_USER_ID=999
ARG CONTAINER_GROUP_ID=999
ARG CONTAINER_USER=redis
ARG CONTAINER_GROUP=redis

# Create directories for Redis
RUN mkdir -p /var/log/redis /var/lib/redis /etc/redis && \
    chown -R redis:redis /var/log/redis /var/lib/redis /etc/redis && \
    chmod 755 /var/log/redis /var/lib/redis /etc/redis

# Copy Redis configuration template
COPY redis.conf.template /etc/redis/redis.conf.template

# Copy startup script
COPY start.sh /usr/local/bin/redis-start.sh
RUN chmod +x /usr/local/bin/redis-start.sh

# Create backup directory
RUN mkdir -p /var/backups/redis && \
    chown -R redis:redis /var/backups/redis

# Expose Redis port
EXPOSE ${REDIS_PORT}

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD redis-cli ping || exit 1

# Set working directory
WORKDIR /data

# Use custom startup script
ENTRYPOINT ["/usr/local/bin/redis-start.sh"]
CMD ["redis-server", "/etc/redis/redis.conf"]
