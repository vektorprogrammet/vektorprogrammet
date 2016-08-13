#!/bin/sh
log=app/logs/deploy.log
cd $(dirname "$0")
echo "Starting deploy" >> $log
date >> $log
git pull origin master >> $log
SYMFONY_ENV=prod composer install --no-dev --optimize-autoloader >> $log
npm install >> $log
./node_modules/.bin/gulp build:prod >> $log
php app/console cache:clear --env=prod >> $log
echo "---------------------------------------------------" >> $log

exit 0
