FROM php:8.1-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libzip-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring xml zip pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js 18 via NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# PHP deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Node deps + build
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js assets/ ./assets/
RUN npm run build:prod

# App source
COPY . .
RUN composer run-script post-install-cmd --no-interaction 2>/dev/null || true \
    && php bin/console cache:clear --env=prod --no-debug

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
