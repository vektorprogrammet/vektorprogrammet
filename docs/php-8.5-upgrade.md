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

## Deploy subsequent updates

Run the repository's `deploy.sh` manually as root on the replacement server. It defaults to branch `master` and production mode. The checkout must already be on the selected branch; it refuses tracked local changes, backward updates, and concurrent runs. It never resets local changes. The legacy HTTP webhook cannot run this root-only script: do not grant the web service sudo access. Before merging this upgrade to master, disable or redirect any existing deployment webhook on the old server, which may still execute an older script.

Default branch, latest fetched commit:

```bash
cd /var/www/vektorprogrammet
bash ./deploy.sh
```

Custom branch and a reviewed commit:

```bash
DEPLOY_BRANCH=php-8.5-compatibility bash ./deploy.sh REVIEWED_COMMIT_SHA
```

The script updates code in place, installs locked Composer dependencies, checks platform requirements, builds assets using the tested Node 14 Docker image, clears production cache, repairs runtime permissions even on failure, and checks the local production endpoint. It requires MySQL root access through the local client configuration and active mysql, nginx, and PHP-FPM services. It assumes this server's existing www-data account and web directory layout.

Configuration environment variables:

| Variable | Default |
| --- | --- |
| `DEPLOY_BRANCH` | `master` |
| `DEPLOY_APP_DIR` | Directory containing the script |
| `DEPLOY_COMMIT` | Latest fetched branch tip; positional SHA overrides this |
| `DEPLOY_BACKUP_DIR` | `/root/vektor-deploy-backups` |
| `DEPLOY_HEALTH_URL` | `http://127.0.0.1:8081` (retain the private production listener) |
| `DEPLOY_PHP_BIN` | `php` |
| `DEPLOY_DATABASE` | `vektorprogrammet` |
| `DEPLOY_FPM_SERVICE` | `php8.5-fpm` |
| `DEPLOY_RUN_MIGRATIONS` | `0`; fail on pending migrations |

Only after reviewing pending migration SQL and confirming a restorable backup, set `DEPLOY_RUN_MIGRATIONS=1` to execute migrations. Legacy migration identifiers must already be converted. Do not use schema-update or fixtures as deployment steps.

Each run saves a local SQL dump, parameters, web/files, signatures, previous/target SHAs, build-overwritten image paths, and a deployment log under its backup directory. It does not back up all uploaded photos/receipts, node_modules, vendor, or server settings. These local backups are not a substitute for a full Droplet snapshot or external uploaded-file backup. Retention is manual.

A failure stops the script and repairs runtime permissions but does not automatically roll back code or database changes. Inspect the deployment log and saved SHAs before recovery. A deployment can briefly interrupt requests because code, dependencies, and assets are updated in place.

For the initial upgrade of the deployment script itself, fetch and copy its reviewed version outside the checkout, then invoke that copy with `DEPLOY_APP_DIR=/var/www/vektorprogrammet` and the selected branch/SHA. This avoids executing the old script to obtain its replacement. Normal runs make a private temporary copy of themselves before updating Git so Bash's input remains stable.


## Receipt upload limits

Receipt uploads are limited to **2 MiB per file**. The receipt form displays
this limit and validates it in the application, even if the server allows larger
uploads. Invalid submissions keep the form open with a visible upload error.
The versioned configuration in `deploy/php/99-vektor-uploads.ini` also sets
`upload_max_filesize = 2M` and `post_max_size = 10M`. The larger POST limit leaves
room for multipart fields and does not raise the per-file limit. These are server settings:
Composer and `deploy.sh` do not install them automatically, and application PHP
cannot raise the upload limit after the request has arrived.

On the Ubuntu replacement server, install the configuration as root:

```bash
# Back up an existing file before replacing it.
CONFIG=/etc/php/8.5/fpm/conf.d/99-vektor-uploads.ini
if [ -e "$CONFIG" ]; then
    cp -a "$CONFIG" "$CONFIG.before-$(date -u +%Y%m%dT%H%M%SZ)"
fi
install -o root -g root -m 0644 deploy/php/99-vektor-uploads.ini "$CONFIG"
php-fpm8.5 -t && systemctl reload php8.5-fpm
```

Keep nginx's `client_max_body_size` at least `10m` in the applicable server or
location block; validate with `nginx -t` before reloading nginx. Pool-level
`php_admin_value` overrides can supersede the INI file. Checking `php -i` alone
checks CLI settings, not the FPM pool serving the website.

Verify by submitting a real receipt smaller than 2 MiB through the browser,
then confirm it appears in the receipt list. Also submit a file larger than
2 MiB and verify that it is rejected with a visible error and the form left open. A rendered form with HTTP 200
does not mean that the receipt was saved. An upload rejected by nginx before
PHP runs will instead return HTTP 413.

The live server temporarily had an 8 MiB limit during diagnosis. Reinstall this
2 MiB configuration and reload FPM to restore the intended limit. When
deploying the edit-mode controller fix, remember that a manually patched
`UserController.php` is a tracked local change: save and compare it against the
reviewed repository version before reconciling it. Do not discard other local
changes to get past the deployment check.

## Verify and cut over

On staging, check public pages, HS_25.jpg, login/logout, role permissions, receipts (upload, edit, download), admissions, surveys, certificates, scheduled jobs and each external integration. Test with a copy of production data; SQLite fixtures do not reproduce MySQL behavior or real account history. Confirm backups, nginx's web root (`web`), TLS, FPM socket, cron paths and PHP executable version.

For the actual cutover, briefly pause writes/jobs, take a final consistent database/files backup, restore it to the new host and repeat the reviewed metadata conversion. Apply only reviewed pending migrations. Warm the production cache, switch traffic and monitor errors. Retain the old host and backup for rollback. Coordinate database changes and writes when rolling back; changing DNS alone cannot undo newly written data.

The native local development server is not a production web server.
