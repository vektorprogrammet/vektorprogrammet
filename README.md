<img src="https://github.com/vektorprogrammet/vektorprogrammet/blob/master/app/Resources/assets/images/vektor_stor.png" alt="Vektorprogrammet" width="400">

# Vektorprogrammet

The existing Symfony website. Backend: **PHP 8.5.12+**, Symfony 6.4, Twig 3 and
Doctrine ORM. Frontend assets: the existing **Node 14.21.3** Gulp toolchain.

## Local quick start

Requirements: Git, PHP **8.5.12 or newer** with the extensions listed in the
[local development guide](docs/local-development.md#requirements), and a running
Docker installation for the legacy asset build. No local MySQL server is needed:
development and automated tests use separate SQLite files.

**Use these commands only in a fresh local clone.** Loading fixtures replaces
database contents. Do not use a production configuration or run `deploy.sh` locally.

```bash
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet

unset APP_ENV SYMFONY_ENV SYMFONY_DEBUG
php -v
mkdir -p var/data var/cache var/logs web/media/cache signatures
php composer.phar install --no-interaction
php composer.phar check-platform-reqs

php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load --env=dev --no-interaction

docker run --rm --platform=linux/amd64 \
  -v "$PWD:/app" --tmpfs /app/node_modules:exec \
  -w /app node:14.21.3-buster \
  bash -lc 'NODE_ENV=development npm ci --ignore-scripts && node node_modules/node-sass/scripts/install.js && node node_modules/gulp/bin/gulp.js build:dev'

php -d upload_max_filesize=2M -d post_max_size=10M \
  -S 127.0.0.1:8000 -t web web/router.php
```

Open **<http://127.0.0.1:8000>**. Log in with username **`admin`** and password
**`1234`**. These are fixture credentials for local development only.
PHP runs natively; the Docker command builds CSS, JavaScript and images, then exits.

On later runs, start just the PHP command. Rebuild assets when changing SCSS,
JavaScript or source images; PHP and Twig changes normally appear on refresh.

## Run tests

In a second terminal at the repository root:

```bash
php -d memory_limit=512M bin/phpunit --configuration app/phpunit.xml.dist
```

The test bootstrap recreates `var/data/test.db`; it does not reuse the development
database. See the guide before changing database configuration or running tests
alongside another test process.

## Guides

- **[Local development and testing](docs/local-development.md):** requirements,
  step-by-step setup, fixture users, daily workflow, test filters, browser checks,
  logs and troubleshooting.
- **[Production migration and deployment](docs/php-8.5-upgrade.md):** server setup,
  backups, migration history and the manual production deployment script.

The separate timetable Vue application is not included in the main asset build.
Its current rebuild does not match the preserved production styling. See the
[timetable notes](docs/local-development.md#timetable-timeplan) before testing it.
