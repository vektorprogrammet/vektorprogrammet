<img src="https://github.com/vektorprogrammet/vektorprogrammet/blob/master/app/Resources/assets/images/vektor_stor.png" alt="Vektorprogrammet" width="400">

# Development setup

## Requirements

- PHP **8.5.12 or newer**, with PDO, SQLite (development/tests), MySQL (production), GD, intl, mbstring, curl, XML, fileinfo, OpenSSL and zip.
- Composer 2 (the repository includes `composer.phar`).
- Node **14.21.3** for the existing Gulp/node-sass asset build. On Apple Silicon, use the Docker asset build below.
- Git; Docker is optional for building assets.

The application uses Symfony 6.4, Twig 3 and Doctrine ORM 2.20. The PHP upgrade does not modernize the older JavaScript toolchain.

## Install PHP dependencies

From the repository root:

```sh
php -v
php composer.phar install --no-interaction
```

The first install creates the ignored `app/config/parameters.yml`. Keep local credentials there. The default configuration uses `var/data/dev.db` (SQLite). Local mail, SMS and Slack delivery are disabled; do not copy production integration credentials into this file.

For a **new, empty local database only**:

```sh
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load --env=dev --no-interaction
```

Fixtures replace database contents. Preserve an existing development database instead of reloading fixtures. Never run these commands against production.

## Build assets

Using Node 14 on a compatible x86 Linux machine:

```sh
npm ci
npm run build:dev
```

On a Mac with Docker, run:

```sh
docker run --rm --platform=linux/amd64 \
  -v "$PWD:/app" --tmpfs /app/node_modules:exec \
  -w /app node:14.21.3-buster \
  bash -lc 'npm ci --ignore-scripts && node node_modules/node-sass/scripts/install.js && node node_modules/gulp/bin/gulp.js build:dev'
```

Gulp copies images from `app/Resources/assets/images` into the ignored `web/images` directory. Rebuild after adding an image. The Docker command above builds assets only; PHP runs natively on your Mac.

## Run locally

```sh
php -S 127.0.0.1:8000 -t web web/router.php
```

Open <http://127.0.0.1:8000>. The router serves assets and forwards application routes to the development front controller. It replaces the removed Symfony `server:run` command. With local Node dependencies installed, `npm start` also starts Gulp's watcher.

The fixture accounts all use password `1234`:

| Role | Username |
| --- | --- |
| Assistant | `assistent` |
| Team member | `teammember` |
| Team leader | `teamleader` |
| Admin | `admin` |

These accounts are for development fixtures only.

## Checks

```sh
php composer.phar validate --strict
php composer.phar audit
php bin/console lint:container
php bin/console lint:twig app/Resources/views
php bin/console doctrine:schema:validate --skip-sync
php -d memory_limit=512M bin/phpunit --configuration app/phpunit.xml.dist
```

The test bootstrap recreates `var/data/test.db`, loads fixtures and restores it between tests. It must remain separate from the development or production database. To run one test class, append `--filter ClassName` to the PHPUnit command.

`npm run cs` runs the PHP style fixer.

## PHP Docker development image

The Dockerfile targets `php:8.5.12-cli-bookworm` and includes the legacy Node 14 build tools. Build with `docker build --platform=linux/amd64 -t vektorprogrammet .`, then run with `docker run --rm -p 8000:8000 -v "$PWD:/app" vektorprogrammet`. The PHP base image is available upstream. Native PHP 8.5.12 is the verified local path; the complete Docker image has not been built and verified as part of this migration.

## Production upgrade

See [the PHP 8.5 migration guide](docs/php-8.5-upgrade.md). This branch requires a patched PHP runtime and a reviewed database migration history before deployment.
