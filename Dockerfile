FROM composer:2 AS builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-progress --no-interaction
COPY . .
RUN composer dump-autoload -o

FROM php:8.4-apache
WORKDIR /var/www/html

# Installe les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

COPY --from=builder /app /var/www/html
RUN a2enmod rewrite

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
