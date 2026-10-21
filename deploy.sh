#!/bin/sh
set -eu

# PHP's webhook environment may not include the paths needed by npm scripts.
export PATH="/usr/local/bin:/usr/bin:/bin:/usr/local/sbin:/usr/sbin:/sbin${PATH:+:$PATH}"

cd "$(dirname "$0")"
log="$PWD/var/logs/deploy.log"
exec >>"$log" 2>&1
trap 'deploy_status=$?; if [ "$deploy_status" -ne 0 ]; then echo "Deployment failed (exit $deploy_status)."; fi' 0

echo "Starting deploy"
date

php -r 'if (PHP_VERSION_ID < 80512) { fwrite(STDERR, "Deployment requires PHP 8.5.12 or newer; found ".PHP_VERSION.PHP_EOL); exit(1); }'

backup_script="${DEPLOY_BACKUP_SCRIPT:-${HOME:-/var/www}/cron/vektorprogrammet-backup}"
if [ ! -x "$backup_script" ]; then
    echo "Backup script is missing or not executable: $backup_script"
    echo "Restore it or set DEPLOY_BACKUP_SCRIPT to its absolute path."
    exit 1
fi
(
    cd "$(dirname "$backup_script")"
    "./$(basename "$backup_script")"
)

git checkout .
git pull --ff-only origin master
SYMFONY_ENV=prod php ./composer.phar install --no-dev --optimize-autoloader --no-interaction
# Gulp and node-sass are devDependencies, but are required to build production assets.
NODE_ENV=development npm install --scripts-prepend-node-path=true
NODE_ENV=production ./node_modules/.bin/gulp build:prod
php bin/console cache:clear --env=prod
php bin/console app:migrations:upgrade-metadata --check --env=prod
php bin/console doctrine:migrations:migrate -n --env=prod
echo "Deploy complete"
echo "---------------------------------------------------"
