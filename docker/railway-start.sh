#!/bin/bash
set -e

echo "=== Railway Start ==="

# Generate JWT keys if missing
if [ ! -f config/jwt/private.pem ]; then
    mkdir -p config/jwt
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:"$JWT_PASSPHRASE" 2>/dev/null
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:"$JWT_PASSPHRASE" 2>/dev/null
    echo "JWT keys generated."
fi

# Clear and warm cache
php bin/console cache:clear --env="$APP_ENV" --no-debug

PORT="${PORT:-8000}"
echo "Starting PHP server on 0.0.0.0:$PORT..."
exec php -S 0.0.0.0:"$PORT" -t public
