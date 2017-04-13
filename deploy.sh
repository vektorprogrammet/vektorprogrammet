#!/bin/sh
# Need to change directory before running backup script for Google Drive sync to work
cd /var/backups/vektorprogrammet
./backup
cd -
log=app/logs/deploy.log
cd $(dirname "$0")
echo "Starting deploy" >>$log
date >>$log
git pull origin master >>$log 2>&1
SYMFONY_ENV=prod php ./composer.phar install --no-dev --optimize-autoloader >>$log 2>&1
npm install >>$log 2>&1
./node_modules/.bin/gulp build:prod >>$log 2>&1
php app/console cache:clear --env=prod >>$log 2>&1
php app/console doctrine:migrations:migrate -n >>$log 2>&1
echo "---------------------------------------------------" >>$log

exit 0
