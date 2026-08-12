FROM php:8.4-fpm-bookworm

ENV APP_ENV=prod APP_DEBUG=0 PORT=8000 PREVIEW_EGRESS_POLICY=deny-by-default
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends mariadb-server nginx git unzip curl ca-certificates libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev libonig-dev libxml2-dev && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd intl mbstring xml zip pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-progress --prefer-dist --no-interaction --no-autoloader
COPY . .
RUN composer dump-autoload --classmap-authoritative --no-dev
COPY infra/preview/nginx.conf /etc/nginx/sites-enabled/default
RUN chmod 0755 /usr/local/bin/preview-entrypoint
EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/preview-entrypoint"]
