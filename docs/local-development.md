# Local development and testing

This guide runs the Symfony website on your computer with fixture data. The
[README quick start](../README.md#local-quick-start) is the shortest path. Use the
sections below for explanations, repeat runs and troubleshooting.

Run commands from the repository root unless another directory is specified.
The examples use Bash-compatible syntax (macOS, Linux or WSL).

## Requirements

| Tool | Requirement / purpose |
| --- | --- |
| PHP | **8.5.12 or newer**; the version constraint is in `composer.json` |
| PHP extensions | PDO, `pdo_sqlite`, SQLite support, GD, intl, mbstring, curl, XML/DOM, ctype, iconv, fileinfo, OpenSSL and zip |
| Composer | Composer 2; use the included `composer.phar` with your selected PHP |
| Git | Clone and update the source |
| Docker | Runs the pinned legacy asset tools without installing Node 14 on your host |
| Node (optional) | **14.21.3** for a native asset build; not needed on the host when using the Docker command |

Install the required PHP and extensions using your platform's package manager,
then check which executable your terminal uses:

```bash
command -v php
php -v
php --ini
php -m
docker info
```

The local PHP server uses **CLI PHP**, not a system PHP-FPM service. A different
PHP version installed elsewhere does not change the executable found in `PATH`.
Do not bypass Composer's platform checks with `--ignore-platform-reqs`.

### Apple Silicon Macs

Use native PHP on the Mac and `--platform=linux/amd64` for the Node container.
The old node-sass package expects an x86 Linux binary in that container. Docker
Desktop must be running. An exited asset container is normal: its output remains
in `web/`, and Docker does not need to serve the website.

The repository also contains a complete PHP/Node `Dockerfile`. That image has not
been verified end to end as part of this documentation work, so it is not the
default setup here. Do not assume `npm run docker:setup` initializes a working
environment; use the explicit steps below.

## First-time setup

### 1. Clone and select the development environment

```bash
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet
unset APP_ENV SYMFONY_ENV SYMFONY_DEBUG
mkdir -p var/data var/cache var/logs web/media/cache signatures
```

`bin/console` defaults to `dev`. The development web router always uses
`app_dev.php`, which boots the dev kernel with debugging enabled. Keep the server
bound to `127.0.0.1`; it is a development server, not a production server.

### 2. Install PHP dependencies and configure the app

```bash
php composer.phar install --no-interaction
php composer.phar check-platform-reqs
```

Use `install`, not `update`, to use the versions in `composer.lock`. Include dev
dependencies: they provide SQLite test support, PHPUnit and Symfony's dev tools.
Composer also builds parameters, clears cache and installs bundle assets.

The first installation creates the ignored `app/config/parameters.yml` from
`parameters.yml.dist`. For a fresh local setup, leave the template's values:

| Setting | Local default |
| --- | --- |
| `database_driver` | `pdo_sqlite` |
| `database_path` | `%kernel.root_dir%/../var/data/dev.db` |
| `slack_disabled` | `true` |
| `sms_disable` | `true` |
| `ipinfo_token` | Empty string, so IP lookup is skipped |
| Google API / reCAPTCHA | Placeholder/empty values; no production credentials needed |

This project uses that YAML file; creating a Laravel-style `.env` file does not
configure its database. Do not copy the server's production parameters or Google
token cache into your local clone.

Local mail uses `Swift_NullTransport`, so no real email is delivered. Google API
calls are disabled in the base configuration, SMS and Slack delivery are disabled
by the local defaults, and reCAPTCHA is disabled outside the production override.
A successful local contact form does **not** verify Gmail delivery.

### 3. Initialize a fresh local database

**Fresh, empty local database only:**

```bash
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:fixtures:load --env=dev --no-interaction
```

The first command creates the current schema; the second loads sample data and
accounts. Fixtures purge existing data. Do not rerun these commands to restart
the website, and do not point them at a production database. No MySQL service,
production dump or old migration history is required for this fixture setup.

If you already have local data, back up `var/data/dev.db` before making schema or
fixture changes. Do not blindly run `npm run setup`, `db:update` or `db:reload`:
these legacy convenience scripts contain destructive schema/fixture operations.

### 4. Build frontend assets

Recommended, including on Apple Silicon:

```bash
docker run --rm --platform=linux/amd64 \
  -v "$PWD:/app" --tmpfs /app/node_modules:exec \
  -w /app node:14.21.3-buster \
  bash -lc 'NODE_ENV=development npm ci --ignore-scripts && node node_modules/node-sass/scripts/install.js && node node_modules/gulp/bin/gulp.js build:dev'
```

The repository is mounted into `/app`. `node_modules` is temporary inside the
container, preventing Linux dependencies from being mixed with host dependencies.
Generated CSS, JS, fonts, vendor files and images are written into `web/` and
remain after the container exits. Repeating this command installs the container
dependencies again; it does not reset your PHP database.

The development build copies images without running production image optimizers.
For the production Gulp build, optipng/jpegtran/gifsicle binaries also need their
install scripts; see the tested container sequence in `deploy.sh`. Do not use the
root-only production deploy script to build assets on your laptop.

Native alternative on a compatible host with Node **14.21.3**:

```bash
node --version
npm ci
npm run build:dev
```

Do not share `node_modules` between that native build and an x86 Linux container.

### 5. Start the website

```bash
php -d upload_max_filesize=2M -d post_max_size=10M \
  -S 127.0.0.1:8000 -t web web/router.php
```

Open **<http://127.0.0.1:8000>**. Keep this terminal running; stop it with
**Ctrl+C**. The router serves existing static files and forwards application
URLs to `app_dev.php`. There is no Symfony `server:run` command in this version.
If port 8000 is occupied, use 8001 in the command and browser URL.

The `-d` options give the local CLI server the intended **2 MiB per-file** limit.
The larger POST limit leaves room for the multipart form. The receipt form also
validates its own 2 MiB limit. Changing `/etc/php/.../fpm/conf.d` does not configure
this local CLI server; production FPM configuration is documented separately.

## Fixture logins

Open `/login`. All four accounts use password **`1234`**:

| Role | Username | Useful for |
| --- | --- | --- |
| Assistant | `assistent` | Min side and own receipts |
| Team member | `teammember` | Basic kontrollpanel access |
| Team leader | `teamleader` | Interview administration and timetable access |
| Admin | `admin` | Page edit mode and full administration |

These accounts exist only after loading fixtures. Log out before testing another
role, or use a separate browser profile. A 403 for a lower-privilege account can
be expected; it does not automatically indicate a broken page.

## Daily workflow

1. Start the PHP server with the command above. Keep the same hostname/port in
   the browser so sessions and form tokens remain consistent.
2. Edit PHP/Twig and refresh. For configuration changes, run
   `php bin/console cache:clear --env=dev` as your normal local user.
3. For SCSS, JS or images, repeat the Docker asset command. Hard refresh if the
   browser still displays old files.
4. Run a focused test, then the full checks before opening a PR.
5. Stop the PHP server with Ctrl+C. Your development SQLite file remains.

For continuous asset watching, run this in a **second terminal**:

```bash
docker run --rm -it --platform=linux/amd64 \
  -v "$PWD:/app" --tmpfs /app/node_modules:exec \
  -w /app node:14.21.3-buster \
  bash -lc 'NODE_ENV=development npm ci --ignore-scripts && node node_modules/node-sass/scripts/install.js && node node_modules/gulp/bin/gulp.js'
```

That starts the main Gulp development build and watcher; it is not a separate
PHP server. With native Node dependencies installed, `npm start` instead runs
both the PHP server and Gulp watcher. Do not run both PHP startup methods on the
same port. Restart PHP explicitly if changing its `-d` upload options.

## Automated tests and checks

### Full PHPUnit suite

```bash
php -d memory_limit=512M bin/phpunit --configuration app/phpunit.xml.dist
```

`app/testBootstrap.php` deletes and recreates `var/data/test.db`, loads the schema
and fixtures, and saves `test.db.bk`. `BaseWebTestCase` restores the database
between functional tests. The test configuration explicitly uses SQLite; your
`var/data/dev.db` is separate. Keep the bootstrap/configuration intact and run
only one test process per checkout: the suite shares those test database files.

### One class or one test

```bash
php -d memory_limit=512M bin/phpunit --configuration app/phpunit.xml.dist --filter ReceiptControllerTest
php -d memory_limit=512M bin/phpunit --configuration app/phpunit.xml.dist --filter 'ReceiptControllerTest::testCreate'
```

BrowserKit functional tests make HTTP-style requests but **do not execute browser
JavaScript**. Test interactive browser behavior separately.

### Dependency, configuration and style checks

```bash
php composer.phar validate --strict
php composer.phar check-platform-reqs
php bin/console lint:container --env=dev
php bin/console lint:twig app/Resources/views --env=dev
php bin/console doctrine:schema:validate --skip-sync --env=dev
php bin/php-cs-fixer fix src/ --dry-run --diff
php composer.phar audit
```

`--skip-sync` checks mappings, not whether the database schema matches them.
`composer audit` can report dependency advisories independently of test failures.
Use the dry-run style command to inspect formatting; `npm run cs` rewrites files.
The PHPUnit workflow in `.github/workflows/tests.yml` defines the core CI checks.

## Browser smoke tests

| Area | What to verify locally |
| --- | --- |
| Public site | Homepage, login, navigation, CSS and JS load |
| Role permissions | Assistant versus team leader/admin access |
| Page editing | As admin, enter edit mode, open an editor, save and reload a local change |
| Receipts (`/utlegg`) | Save a real image/PDF below 2 MiB; reject a larger file with the form open and an error visible |
| Profile photos | Load an original image and its generated thumbnail; `web/media/cache` must be writable |
| Integrations | Local forms can be checked, but disabled mail/SMS/Google delivery is not an integration test |

### Timetable (Timeplan)

The main asset command does **not** build the separate timetable Vue application.
Its source is in `src/AppBundle/AssistantScheduling/Webapp`; its served bundle is
`web/js/scheduling/build.js`. A fresh clone will not contain that ignored file,
so the timetable can be blank even when the rest of the website works.

For matching current production appearance, obtain the verified compiled bundle
from a maintainer and place it at that path. Do not copy database dumps or secret
parameters just to obtain the JavaScript file. Rebuilding the current timetable
source/dependencies changed its styling during migration and is not a verified
replacement. Main Gulp builds can still be used for the rest of the website.

## Logs and troubleshooting

```bash
tail -n 60 var/logs/dev/dev.log
php bin/console debug:router --env=dev
php bin/console about --env=dev
```

The PHP server terminal also shows request errors. The Symfony dev toolbar and
profiler can help inspect form validation and exceptions. Avoid sharing tokens,
cookies, bank details or complete private form responses when reporting issues.

| Symptom | Check / action |
| --- | --- |
| Composer rejects PHP or an extension | Check `php -v`, `php --ini`, `php -m`; fix the selected CLI installation instead of ignoring platform requirements |
| Missing FOSCKEditorBundle / autoload class | Run `php composer.phar install`, including dev dependencies, from the root |
| SQLite "unable to open database" / missing driver | Create `var/data`, check local write permission and enable `pdo_sqlite`; verify the YAML path |
| "Table already exists" during setup | A database already exists; stop and preserve it rather than repeating schema creation |
| Fixtures/users missing | Check the dev database setting and whether fixtures were loaded into this local file |
| Page has no styling / image 404 | Build the main assets; check `web/css`, `web/js` and `web/images` |
| node-sass binary error | Use the pinned x86 Docker asset command; do not reuse ARM/macOS node_modules inside Linux |
| Timetable is blank or changes styling | Follow the timetable notes; the main build and separate Vue build are different |
| Receipt returns HTTP 200 without saving | Look for form errors; HTTP 200 can be a rejected form, not a successful save |
| Cache/log/thumbnail write error | Run commands as your local user and check ownership of `var/` and `web/media/cache`; do not use production ownership commands or chmod 777 |
| Login/CSRF behaves inconsistently | Use one browser origin, log in again and reload the form after a cache/configuration change |
| Email did not arrive | Local mail is intentionally discarded; real Gmail delivery requires a separate integration test |

This document is based on the checked-in configuration and existing deployment
experience. The complete fresh-machine setup was not executed during the
documentation update; PHP and Docker were unavailable in the authoring workspace.
Keep that distinction when reporting a reproducible setup failure.
