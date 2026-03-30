# MySQL Dockerfile for HoneStay Application
ARG MYSQL_VERSION=8.0
FROM mysql:${MYSQL_VERSION}

# Set timezone
ARG TIMEZONE=Asia/Ho_Chi_Minh
ENV TZ=${TIMEZONE}

# MySQL Configuration Arguments
ARG MYSQL_ROOT_PASSWORD=rootpassword
ARG MYSQL_DATABASE=check_scam_homestay_db
ARG MYSQL_USER=rrootuserootuser
ARG MYSQL_PASSWORD=rootuserpassword

# Set MySQL environment variables
ENV MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
ENV MYSQL_DATABASE=${MYSQL_DATABASE}
ENV MYSQL_USER=${MYSQL_USER}
ENV MYSQL_PASSWORD=${MYSQL_PASSWORD}

# MySQL configuration settings
ARG MYSQL_MAX_CONNECTIONS=200
ARG MYSQL_INNODB_BUFFER_POOL_SIZE=256M
ARG MYSQL_QUERY_CACHE_SIZE=64M
ARG MYSQL_LOG_ERROR=/var/log/mysql/error.log
ARG MYSQL_SLOW_QUERY_LOG=1
ARG MYSQL_LONG_QUERY_TIME=2

# Set MySQL runtime environment variables
ENV MYSQL_MAX_CONNECTIONS=${MYSQL_MAX_CONNECTIONS}
ENV MYSQL_INNODB_BUFFER_POOL_SIZE=${MYSQL_INNODB_BUFFER_POOL_SIZE}
ENV MYSQL_QUERY_CACHE_SIZE=${MYSQL_QUERY_CACHE_SIZE}
ENV MYSQL_LOG_ERROR=${MYSQL_LOG_ERROR}
ENV MYSQL_SLOW_QUERY_LOG=${MYSQL_SLOW_QUERY_LOG}
ENV MYSQL_LONG_QUERY_TIME=${MYSQL_LONG_QUERY_TIME}

# Create necessary directories and set permissions
RUN mkdir -p /var/log/mysql && \
    chown -R mysql:mysql /var/log/mysql && \
    chmod 755 /var/log/mysql

# Copy custom MySQL configuration (static file instead of template)
COPY my.cnf /etc/mysql/conf.d/my.cnf
COPY init-scripts/ /docker-entrypoint-initdb.d/

# Create backup directory
RUN mkdir -p /var/backups/mysql && \
    chown -R mysql:mysql /var/backups/mysql

# Set timezone in container
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Expose MySQL port
EXPOSE 3306

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD mysqladmin ping -h localhost -u root -p${MYSQL_ROOT_PASSWORD} || exit 1

# Use default MySQL entrypoint
CMD ["mysqld"]