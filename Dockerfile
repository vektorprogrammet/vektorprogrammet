FROM php:8.4-cli-alpine

# System dependencies
RUN apk add --no-cache \
    git unzip curl libpng libjpeg-turbo freetype \
    libxml2 libzip sqlite-libs oniguruma mysql-client bash openssl \
    # Build deps (removed after compile)
    && apk add --no-cache --virtual .build-deps \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libxml2-dev libzip-dev sqlite-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring xml zip pdo_sqlite pdo_mysql \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Bun
RUN curl -fsSL https://bun.sh/install | bash
ENV PATH="/root/.bun/bin:$PATH"

WORKDIR /app

# PHP deps (fixtures bundle is in require, not require-dev)
ARG APP_ENV=prod
COPY composer.json composer.lock ./
RUN COMPOSER_PROCESS_TIMEOUT=600 composer install \
    $([ "$APP_ENV" = "prod" ] && echo "--no-dev") \
    --no-autoloader --no-scripts

# Node deps + build
COPY package.json ./
RUN bun install
COPY vite.config.mjs ./
COPY assets/ ./assets/
RUN bun run build:prod

# App source
COPY . .
RUN composer dump-autoload $([ "$APP_ENV" = "prod" ] && echo "--no-dev") --optimize
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
