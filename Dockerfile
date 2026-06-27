FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    postgresql-dev

# Install ONLY essential PHP extensions for Laravel + Postgres
RUN docker-php-ext-install pdo pdo_pgsql zip opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Copy configurations and fix line endings
COPY nginx.conf /etc/nginx/http.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN sed -i 's/\r$//' /etc/supervisor/conf.d/supervisord.conf \
    && sed -i 's/\r$//' /etc/nginx/http.d/default.conf \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Setup directory permissions for Laravel
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# Dependencies are already included in the vendor folder (pushed to git)
# No need to run composer install and risk OOM or extension errors.

# Expose port 80
EXPOSE 80

# Make entrypoint script executable and fix Windows line endings
RUN chmod +x /var/www/html/docker-entrypoint.sh \
    && sed -i 's/\r$//' /var/www/html/docker-entrypoint.sh

# Use entrypoint
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
