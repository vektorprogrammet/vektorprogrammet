#!/bin/sh
# Need to change directory before running backup script for Google Drive sync to work
cd /home/vektorprogrammet/cron
./vektorprogrammet-backup
cd -
log=var/logs/deploy.log
cd $(dirname "$0")
echo "Starting deploy" >>$log
date >>$log
git checkout . >>$log 2>&1
git pull origin master >>$log 2>&1
SYMFONY_ENV=prod php ./composer.phar install --no-dev --optimize-autoloader >>$log 2>&1
export NODE_ENV=prod
npm install >>$log 2>&1
./node_modules/.bin/gulp build:prod >>$log 2>&1
npm run setup:client >>$log 2>&1
npm run build:client >>$log 2>&1
php bin/console cache:clear --env=prod >>$log 2>&1
php bin/console doctrine:migrations:migrate -n --env=prod >>$log 2>&1
echo "---------------------------------------------------" >>$log

exit 0
