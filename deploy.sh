#!/bin/sh
cd -
log=/var/www/vektorprogrammet/var/logs/deploy.log
cd ~/cron
./vektorprogrammet-backup >> $log 2>&1
cd -
cd $(dirname "$0")
echo "Starting deploy" >>$log
date >>$log
git checkout . >>$log 2>&1
git pull origin master >>$log 2>&1
SYMFONY_ENV=prod php ./composer.phar install --no-dev --optimize-autoloader >>$log 2>&1
export NODE_ENV=prod
npm install >>$log 2>&1
./node_modules/.bin/gulp build:prod >>$log 2>&1
php bin/console cache:clear --env=prod >>$log 2>&1
php bin/console doctrine:migrations:migrate -n --env=prod >>$log 2>&1
echo "---------------------------------------------------" >>$log

exit 0
