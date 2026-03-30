# Nginx Dockerfile for HoneStay Application
ARG NGINX_VERSION=alpine
FROM nginx:${NGINX_VERSION}

# Install necessary packages
RUN apk add --no-cache \
    gettext \
    tzdata

# Set timezone
ARG TIMEZONE=Asia/Ho_Chi_Minh
ENV TZ=${TIMEZONE}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Create nginx user and group with specific IDs
ARG CONTAINER_USER_ID=1000
ARG CONTAINER_GROUP_ID=1000
ARG CONTAINER_USER=www
ARG CONTAINER_GROUP=www

# Nginx configuration arguments
ARG NGINX_WORKER_PROCESSES=auto
ARG NGINX_WORKER_CONNECTIONS=1024
ARG NGINX_CLIENT_MAX_BODY_SIZE=2048M
ARG NGINX_KEEPALIVE_TIMEOUT=650
ARG NGINX_GZIP=on

# Set environment variables for runtime configuration
ENV NGINX_WORKER_PROCESSES=${NGINX_WORKER_PROCESSES}
ENV NGINX_WORKER_CONNECTIONS=${NGINX_WORKER_CONNECTIONS}
ENV NGINX_CLIENT_MAX_BODY_SIZE=${NGINX_CLIENT_MAX_BODY_SIZE}
ENV NGINX_KEEPALIVE_TIMEOUT=${NGINX_KEEPALIVE_TIMEOUT}
ENV NGINX_GZIP=${NGINX_GZIP}
ENV CONTAINER_USER=${CONTAINER_USER}
ENV CONTAINER_GROUP=${CONTAINER_GROUP}

RUN addgroup -g ${CONTAINER_GROUP_ID} ${CONTAINER_GROUP} && \
    adduser -D -s /bin/sh -u ${CONTAINER_USER_ID} -G ${CONTAINER_GROUP} ${CONTAINER_USER}

# Remove default nginx configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy nginx configuration template
COPY nginx.conf.template /etc/nginx/nginx.conf.template
COPY default.conf /etc/nginx/conf.d/default.conf

# Copy startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Create necessary directories
RUN mkdir -p /var/log/nginx /var/cache/nginx /var/run/nginx && \
    chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/log/nginx /var/cache/nginx /var/run/nginx && \
    chmod -R 755 /var/log/nginx /var/cache/nginx /var/run/nginx

# Create directory for application
RUN mkdir -p /var/www/html && \
    chown -R ${CONTAINER_USER}:${CONTAINER_GROUP} /var/www/html

# Expose port 80
EXPOSE 80

# Use startup script as entrypoint
ENTRYPOINT ["/usr/local/bin/start.sh"]
CMD ["nginx", "-g", "daemon off;"]
