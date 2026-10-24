FROM node:14.21.3-buster AS node

FROM php:8.5.12-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libicu-dev libonig-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd intl mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# The existing Gulp/node-sass pipeline still uses Node 14.
# Build with --platform=linux/amd64 for its published node-sass binary.
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

WORKDIR /app
EXPOSE 8000

# Development only; production uses a patched PHP-FPM service behind nginx.
CMD ["php", "-S", "0.0.0.0:8000", "-t", "web", "web/router.php"]
