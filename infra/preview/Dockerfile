FROM php:8.4-fpm-bookworm

ENV APP_ENV=prod APP_DEBUG=0 PORT=8000 PREVIEW_EGRESS_POLICY=deny-by-default \
  PREVIEW_HOST=p20.vektor.phibkro.org PREVIEW_CONTAINER_NAME=vektor-p20-container \
  PREVIEW_RESOURCE_PREFIX=vektor-p20
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends mariadb-server nginx git unzip curl ca-certificates libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev libonig-dev libxml2-dev procps \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd intl mbstring xml zip pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-progress --prefer-dist --no-interaction --no-autoloader
COPY . .
COPY infra/preview/entrypoint.sh /usr/local/bin/preview-entrypoint
COPY infra/preview/nginx.conf /etc/nginx/sites-enabled/default
RUN chmod 0755 /usr/local/bin/preview-entrypoint && mkdir -p /app/var/preview-seed
EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/preview-entrypoint"]
