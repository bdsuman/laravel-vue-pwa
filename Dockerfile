# ===============================
# Laravel Vue PWA - Dockerfile
# ===============================

# -------------------------------
# 1. Vendor Stage (Composer only)
# -------------------------------
FROM composer:2.7 AS vendor

WORKDIR /app
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --optimize-autoloader --no-interaction \
    --no-plugins --no-scripts \
    --ignore-platform-req=ext-pcntl \
    --ignore-platform-req=ext-gd


# -------------------------------
# 2. Node.js Stage (Build Frontend)
# -------------------------------
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# -------------------------------
# 3. PHP-FPM Stage (app target)
# -------------------------------
FROM php:8.2-fpm-alpine AS app

RUN apk add --no-cache \
    postgresql-dev \
    sqlite-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    tzdata \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) \
    pdo pdo_pgsql pdo_sqlite zip gd intl bcmath opcache

RUN pecl install redis && docker-php-ext-enable redis

# Install supervisor for queue workers
RUN apk add --no-cache supervisor

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .

# Create .env.testing for tests
RUN echo 'APP_ENV=testing' > /app/.env.testing && \
    echo 'DB_CONNECTION=sqlite' >> /app/.env.testing && \
    echo 'DB_DATABASE=:memory:' >> /app/.env.testing && \
    echo 'CACHE_DRIVER=array' >> /app/.env.testing && \
    echo 'QUEUE_CONNECTION=sync' >> /app/.env.testing && \
    echo 'SESSION_DRIVER=array' >> /app/.env.testing

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public && \
    chmod -R 775 /app/storage /app/bootstrap/cache


# -------------------------------
# 4. Nginx Stage
# -------------------------------
FROM nginx:1.25-alpine

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=node_builder /app/public /usr/share/nginx/html

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
