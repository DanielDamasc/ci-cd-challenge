# Dockefile Multi-Stage (unificado)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-fpm-alpine
# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs \
    mariadb-connector-c \
    && apk add --no-cache --virtual build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    mariadb-connector-c-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    gd \
    zip \
    exif \
    pcntl \
    bcmath \
    intl \
    opcache \
    && apk del build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies and add AWS S3 Flysystem
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && composer require -W league/flysystem-aws-s3-v3

# Copy built assets from Node stage
COPY --from=node-builder /app/public/build public/build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Nginx configuration for Laravel
RUN mkdir -p /etc/nginx/http.d && \
    cat > /etc/nginx/http.d/default.conf << 'EOF'
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Supervisor folders
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor

# Supervisor configuration
RUN cat > /etc/supervisor/supervisord.conf << 'EOF'
[supervisord]
user=root
nodaemon=true
logfile=/dev/null
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=/usr/local/sbin/php-fpm -F
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]