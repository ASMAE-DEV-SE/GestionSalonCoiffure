FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    mysql-client \
    ca-certificates \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath zip pcntl exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .

RUN mkdir -p bootstrap/cache \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x start.sh \
    && php artisan storage:link || true

ENV LOG_CHANNEL=stderr \
    LOG_LEVEL=debug \
    APP_DEBUG=true

EXPOSE 8000

CMD ["sh", "/app/start.sh"]
