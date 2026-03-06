#!/bin/bash
set -e

echo "=== Railway Pre-deploy ==="

# Generate JWT keys if missing
if [ ! -f config/jwt/private.pem ]; then
    mkdir -p config/jwt
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:"$JWT_PASSPHRASE" 2>/dev/null
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:"$JWT_PASSPHRASE" 2>/dev/null
    echo "JWT keys generated."
fi

# Clear cache
php bin/console cache:clear --env="$APP_ENV" --no-debug

# DB setup
if [ "${SKIP_FIXTURES:-0}" != "1" ]; then
    echo "Setting up database..."
    php bin/console doctrine:database:create --if-not-exists --env="$APP_ENV"

    DB_NAME=$(php -r "echo parse_url(getenv('DATABASE_URL') ?: '', PHP_URL_PATH) ? ltrim(parse_url(getenv('DATABASE_URL'), PHP_URL_PATH), '/') : 'railway';")
    TABLE_COUNT=$(php bin/console doctrine:query:sql "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = '$DB_NAME'" --env="$APP_ENV" 2>/dev/null | grep -o '[0-9]*' | tail -1 || echo "0")

    if [ "$TABLE_COUNT" -lt "5" ]; then
        echo "Creating schema..."
        php bin/console doctrine:schema:create --env="$APP_ENV"
        echo "Loading fixtures..."
        php bin/console doctrine:fixtures:load --env="$APP_ENV" -n
        echo "Fixtures loaded."
    else
        echo "Schema already exists ($TABLE_COUNT tables). Skipping fixtures."
    fi
fi

echo "Pre-deploy complete."
