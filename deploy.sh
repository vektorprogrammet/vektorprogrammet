#!/bin/sh
log=app/logs/deploy.log
echo "Starting deploy" >>$log
/var/backups/vektorprogrammet/backup >>$log 2>&1
cd $(dirname "$0")
date >>$log
git pull origin master >>$log 2>&1
SYMFONY_ENV=prod php ./composer.phar install --no-dev --optimize-autoloader >>$log 2>&1
npm install >>$log 2>&1
./node_modules/.bin/gulp build:prod >>$log 2>&1
php app/console cache:clear --env=prod >>$log 2>&1
echo "---------------------------------------------------" >>$log

exit 0
