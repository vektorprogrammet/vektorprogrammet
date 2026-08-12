FROM oven/bun:1.3.10 AS preview-seed
WORKDIR /seed
COPY infra/preview/generate-seed.ts infra/preview/seed-policy.json ./
RUN bun generate-seed.ts --output-dir /generated

FROM php:8.4-fpm-bookworm

ENV APP_ENV=prod APP_DEBUG=0 PORT=8000 PREVIEW_EGRESS_POLICY=deny-by-default \
  PREVIEW_HOST=p20.vektor.phibkro.org PREVIEW_CONTAINER_NAME=vektor-p20-container \
  PREVIEW_RESOURCE_PREFIX=vektor-p20 SEED_OUTPUT_DIR=/app/var/preview-seed \
  DATABASE_NAME=vektor_p20 DATABASE_USER=vektor_p20 DATABASE_PASSWORD=vektor_p20 \
  DATABASE_ROOT_PASSWORD=vektor_p20_root MARIADB_HOST=127.0.0.1 MARIADB_PORT=3306 \
  HTTP_PORT=8000
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends mariadb-server nginx git unzip curl ca-certificates libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev libonig-dev libxml2-dev procps \
  && rm -rf /var/lib/apt/lists/* \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd intl mbstring xml zip pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-progress --prefer-dist --no-interaction --no-autoloader \
  && composer dump-autoload --classmap-authoritative --no-dev --no-interaction
COPY . .
COPY --from=preview-seed /generated/seed.sql /app/var/preview-seed/seed.sql
COPY --from=preview-seed /generated/manifest.json /app/var/preview-seed/manifest.json
COPY infra/preview/entrypoint.sh /usr/local/bin/preview-entrypoint
COPY infra/preview/nginx.conf /etc/nginx/sites-enabled/default
RUN chmod 0755 /usr/local/bin/preview-entrypoint \
  && chmod 0444 /app/var/preview-seed/seed.sql /app/var/preview-seed/manifest.json \
  && mkdir -p /app/var/cache /app/var/log \
  && chown -R www-data:www-data /app/var
EXPOSE 8000
ENTRYPOINT ["/usr/local/bin/preview-entrypoint"]
