# BioTree Production Dockerfile
# Optimized for Coolify with FrankenPHP/Octane

# ─────────────────────────────────────────────
# Stage 1: PHP / Composer dependencies
# ─────────────────────────────────────────────
FROM composer:2.7 AS vendor

WORKDIR /app

# Copy only composer files for better layer caching
COPY composer.json composer.lock* ./

RUN composer config --no-interaction --no-scripts \
 && composer install --no-dev --no-autoloader --prefer-dist --ignore-platform-reqs \
 && composer dump-autoload --no-scripts --optimize

# ─────────────────────────────────────────────
# Stage 2: Frontend build
# ─────────────────────────────────────────────
FROM node:20-alpine AS node_builder

WORKDIR /app

# Copy package files
COPY package.json package-lock.json* ./

RUN npm ci --frozen-lockfile

# Copy source and build
COPY . .
RUN npm run build

# ─────────────────────────────────────────────
# Stage 3: Production image
# ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine

# Runtime system dependencies (shared libraries the app needs at runtime).
# Build-time -dev packages are handled by install-php-extensions below.
RUN apk add --no-cache \
    bash \
    curl \
    git \
    librsvg \
    supervisor \
    fcgi \
    openssl \
    openssh-client

# PHP extensions — installed via mlocati/docker-php-extension-installer.
# It pulls extension sources from GitHub releases and auto-manages build deps,
# permanently avoiding the flaky/deprecated pecl.php.net channel that caused
# builds to fail with "No releases available for package pecl.php.net/redis".
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
 && install-php-extensions \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    bcmath \
    opcache \
    pcntl \
    redis

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy vendor from stage 1
COPY --from=vendor /app/vendor ./vendor

# Copy built assets from stage 2
COPY --from=node_builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (FrankenPHP/Otane will listen here)
EXPOSE 80 443

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Start with PHP built-in server for simplicity
# For production with Octane, use: php artisan octane:frankenphp
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
