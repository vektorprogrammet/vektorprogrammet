# PHP 8.5 deployment guide

## Scope

This upgrade changes Symfony 3 to Symfony 6.4, Twig 2 to Twig 3, Doctrine DBAL 2 to 3 and Doctrine Migrations 1 to 3. Symfony is installed as individual component and bundle packages. Application authentication, controllers, forms, events and fixtures have been adapted. PHP must be at least **8.5.12**. Existing bcrypt password hashes remain supported.

This is a framework migration, so a successful local run alone does not establish production readiness. External Gmail, Google Workspace, SMS, Slack, reCAPTCHA and Sentry integrations need staging verification with the appropriate configuration. Their real delivery has not been exercised locally. Existing browser sessions should be treated as invalidated at cutover; users can log in again.

## Prepare a replacement server

The current DigitalOcean host reports Ubuntu 16.04 with Debian Stretch PHP packages and several running PHP-FPM versions. Create a replacement Droplet with a supported Ubuntu release and a trusted repository that supplies patched PHP 8.5.12 or newer. Avoid installing Debian packages into Ubuntu or switching the current site's FPM socket in place.

Before cutover:

1. Back up the database, uploaded files, integration configuration and nginx configuration to storage outside the Droplet. Confirm that they can be restored. A Git checkout does not include uploaded receipts or photos.
2. Check available disk space. The old machine reported only about 1 GB free. Preserve useful log evidence and configure rotation; do not delete uploaded receipts to make space.
3. Stage the code, database copy and uploads on the replacement server. Review production settings in the ignored `app/config/parameters.yml`. Point staging integrations at test destinations or disable delivery.
4. Install PHP CLI and PHP-FPM with the required extensions. Verify **both** the CLI version and the FPM executable/pool used by nginx. Installing CLI PHP does not change nginx's configured runtime.
5. Install Composer dependencies with `SYMFONY_ENV=prod php composer.phar install --no-dev --optimize-autoloader --no-interaction` and build production assets with Node 14 (`NODE_ENV=development npm ci`, then `NODE_ENV=production ./node_modules/.bin/gulp build:prod`). Deploy build artifacts without overwriting uploaded files in `web/images`.
6. Give the service account write access to cache, logs and upload destinations, without making the entire checkout world writable.

## Replacing the monolithic Symfony package in an existing checkout

A fresh checkout has no compiled cache. When updating an existing installation that used `symfony/symfony`, its cached container can still reference files inside that removed package. During maintenance, move the compiled environment caches aside **before** installing dependencies. Do this as the deployment user, with no requests or scheduled jobs using this checkout:

```sh
cd /var/www/vektorprogrammet
VEKTOR_CACHE_BACKUP="$(mktemp -d)"
for environment in dev test prod staging; do
    if [ -d "var/cache/$environment" ]; then
        mv "var/cache/$environment" "$VEKTOR_CACHE_BACKUP/$environment"
    fi
done
SYMFONY_ENV=prod php composer.phar install --no-dev --optimize-autoloader --no-interaction
```

Composer rebuilds the cache and installs bundle assets using the individual package paths. The backup directory contains the previous generated caches; keep it private and remove it after a successful deployment. Application data and uploads stay in their existing directories.

## Preserve the migration history

Doctrine Migrations 3 records fully qualified class names. Version 1 recorded short identifiers. Without conversion, previously executed migrations can appear pending. Work on the staging database copy first, with an independently restorable backup.

Preview the conversion (this command does not change application tables or execute migrations):

```sh
php bin/console app:migrations:upgrade-metadata --env=prod
```

Review every mapping against the old migration history. Unknown identifiers and duplicate destinations cause the command to fail; resolve those discrepancies before continuing. After review and backup, convert only the recorded identifiers, then synchronize the metadata table structure:

```sh
php bin/console app:migrations:upgrade-metadata --apply --env=prod
php bin/console doctrine:migrations:sync-metadata-storage --env=prod
php bin/console app:migrations:upgrade-metadata --check --env=prod
php bin/console doctrine:migrations:status --env=prod
php bin/console doctrine:migrations:migrate --dry-run --env=prod
```

An already deployed database should not suddenly list the historical migrations as pending. Review all proposed SQL before applying any genuinely new migration. Do not use `doctrine:schema:update --force` or reload fixtures on production.

`deploy.sh` now checks the CLI PHP version and refuses to run migrations if legacy identifiers remain. The backup script must exist, and installation/build failures stop deployment. The script still pulls `master`; complete and review this migration before merging into the branch that triggers the production webhook.

## Verify and cut over

On staging, check public pages, HS_25.jpg, login/logout, role permissions, receipts (upload, edit, download), admissions, surveys, certificates, scheduled jobs and each external integration. Test with a copy of production data; SQLite fixtures do not reproduce MySQL behavior or real account history. Confirm backups, nginx's web root (`web`), TLS, FPM socket, cron paths and PHP executable version.

For the actual cutover, briefly pause writes/jobs, take a final consistent database/files backup, restore it to the new host and repeat the reviewed metadata conversion. Apply only reviewed pending migrations. Warm the production cache, switch traffic and monitor errors. Retain the old host and backup for rollback. Coordinate database changes and writes when rolling back; changing DNS alone cannot undo newly written data.

The native local development server is not a production web server.
