# Étape 1 : Builder
FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-progress --no-interaction
COPY . .
RUN composer dump-autoload -o

# Étape 2 : Image finale
FROM php:8.4-apache

WORKDIR /var/www/html

# Extensions PHP pour PostgreSQL + utilitaires
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=builder /app /var/www/html

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
