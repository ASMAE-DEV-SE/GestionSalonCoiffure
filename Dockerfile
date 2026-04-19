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

RUN composer dump-autoload --optimize \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan storage:link || true

ENV LOG_CHANNEL=stderr \
    LOG_LEVEL=debug \
    APP_DEBUG=true

EXPOSE 8000

CMD php artisan config:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && echo "[BOOT] MAIL_MAILER=${MAIL_MAILER} | BREVO_API_KEY set? $([ -n \"$BREVO_API_KEY\" ] && echo yes || echo NO)" \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
