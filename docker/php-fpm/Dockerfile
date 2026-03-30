# PHP-FPM Dockerfile for HoneStay Application
ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-fpm

# Arguments
ARG INSTALL_SUPERVISOR=false
ARG CONTAINER_USER_ID=1000
ARG CONTAINER_GROUP_ID=1000
ARG CONTAINER_USER=www
ARG CONTAINER_GROUP=www
ARG COMPOSER_MEMORY_LIMIT=-1
ARG COMPOSER_PROCESS_TIMEOUT=2000
ARG COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    supervisor \
    cron \
    gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Redis extension with alternative methods
RUN set -ex \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libc-dev \
        pkg-config \
        build-essential \
    && rm -rf /var/lib/apt/lists/* \
    && ( \
        pecl install redis \
        || pecl install redis-stable \
        || ( \
            cd /tmp \
            && curl -L -o redis.tar.gz https://github.com/phpredis/phpredis/archive/develop.tar.gz \
            && tar xzf redis.tar.gz \
            && cd phpredis-develop \
            && phpize \
            && ./configure \
            && make \
            && make install \
        ) \
    ) \
    && docker-php-ext-enable redis

# Create user and group with specified IDs
RUN groupadd -g ${CONTAINER_GROUP_ID} ${CONTAINER_GROUP} \
    && useradd -u ${CONTAINER_USER_ID} -g ${CONTAINER_GROUP} -m -s /bin/bash ${CONTAINER_USER}

# Set umask 000 for 777 permissions on new files
RUN echo 'umask 000' >> /root/.bashrc \
    && echo 'umask 000' >> /root/.profile \
    && echo 'umask 000' >> /home/${CONTAINER_USER}/.bashrc \
    && echo 'umask 000' >> /home/${CONTAINER_USER}/.profile \
    && echo 'export UMASK=000' >> /etc/environment

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Composer
ENV COMPOSER_MEMORY_LIMIT=${COMPOSER_MEMORY_LIMIT}
ENV COMPOSER_PROCESS_TIMEOUT=${COMPOSER_PROCESS_TIMEOUT}
ENV COMPOSER_ALLOW_SUPERUSER=${COMPOSER_ALLOW_SUPERUSER}

# Set Composer permissions and create config directory
RUN chmod +x /usr/local/bin/composer \
    && mkdir -p /root/.composer \
    && echo '{"config": {"process-timeout": '${COMPOSER_PROCESS_TIMEOUT}', "memory-limit": "'${COMPOSER_MEMORY_LIMIT}'"}}' > /root/.composer/config.json

# Install Supervisor conditionally
RUN if [ "$INSTALL_SUPERVISOR" = "true" ]; then \
        apt-get update && apt-get install -y supervisor && rm -rf /var/lib/apt/lists/*; \
    fi
# Copy các template cấu hình
# LƯU Ý: php.ini.template chứa cài đặt OpCode cache cho development
# Template được xử lý bởi start.sh để tạo ra custom.ini với cài đặt phù hợp
# QUAN TRỌNG: opcache.validate_timestamps=1 đảm bảo code thay đổi được phản ánh ngay lập tức
COPY php.ini.template /usr/local/etc/php/php.ini.template
COPY www.conf.template /usr/local/etc/php-fpm.d/www.conf.template

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Create auto-777 setup script
RUN echo '#!/bin/bash' > /usr/local/bin/setup-777-permissions.sh \
    && echo 'umask 000' >> /usr/local/bin/setup-777-permissions.sh \
    && echo 'export UMASK=000' >> /usr/local/bin/setup-777-permissions.sh \
    && echo 'echo "Setting up 777 permissions for new files..."' >> /usr/local/bin/setup-777-permissions.sh \
    && echo 'if [ -d "/var/www/html" ]; then' >> /usr/local/bin/setup-777-permissions.sh \
    && echo '  find /var/www/html -type d -exec chmod 777 {} \; 2>/dev/null || true' >> /usr/local/bin/setup-777-permissions.sh \
    && echo '  find /var/www/html -type f -name "*.php" -exec chmod 777 {} \; 2>/dev/null || true' >> /usr/local/bin/setup-777-permissions.sh \
    && echo 'fi' >> /usr/local/bin/setup-777-permissions.sh \
    && echo 'echo "777 permissions setup completed"' >> /usr/local/bin/setup-777-permissions.sh \
    && chmod +x /usr/local/bin/setup-777-permissions.sh

# Create necessary directories
RUN mkdir -p /var/log/supervisor /var/run/php-fpm \
    && chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html /var/log/supervisor /var/run/php-fpm

# Set default umask for the container (777 permissions for new files)
ENV UMASK=000

# Set environment variables
ENV CONTAINER_USER=${CONTAINER_USER}
ENV CONTAINER_GROUP=${CONTAINER_GROUP}

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Expose port 9000
EXPOSE 9000

# Use startup script as entrypoint
ENTRYPOINT ["/usr/local/bin/start.sh"]

# Default command
CMD ["php-fpm"]