# Staging Environment Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Docker-based staging environment with env var config, local + droplet deployment, team QA access.

**Architecture:** Docker Compose (app + mysql), env vars replacing parameters.yml, Caddy reverse proxy on droplet, GitHub Actions auto-deploy on staging branch.

**Tech Stack:** Docker, PHP 8.5-cli, MySQL 8.0, Caddy, GitHub Actions, Symfony env vars.

**Design doc:** `docs/plans/2026-03-05-staging-environment-design.md`

---

### Task 1: Migrate parameters.yml to .env

Replaces legacy `config/parameters.yml` with Symfony-standard `.env` files. This is a prerequisite — Docker needs env vars, not a generated parameters file.

**Files:**
- Create: `.env`
- Create: `.env.staging`
- Modify: `config/services.yaml`
- Modify: `config/packages/doctrine.yaml`
- Modify: `config/packages/lexik_jwt_authentication.yaml`
- Delete: `config/parameters.yml.dist`
- Modify: `config/.gitignore`

**Step 1: Create `.env` with defaults**

```dotenv
###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=CreateASuperDuperSecretHere
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_DRIVER=pdo_sqlite
DATABASE_URL=sqlite:///%kernel.project_dir%/var/data/dev.db
DATABASE_HOST=localhost
DATABASE_PORT=
DATABASE_NAME=symfony
DATABASE_USER=root
DATABASE_PASSWORD=
DATABASE_PATH=%kernel.project_dir%/var/data/dev.db
###< doctrine/doctrine-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=your_jwt_passphrase_here
###< lexik/jwt-authentication-bundle ###

###> mailer ###
MAILER_DSN=smtp://127.0.0.1
DEFAULT_FROM_EMAIL=vektorbot@vektorprogrammet.no
ECONOMY_EMAIL=okonomi@vektorprogrammet.no
DEFAULT_SURVEY_EMAIL=evaluering.ntnu@vektorprogrammet.no
NO_REPLY_EMAIL_USER_CREATION=
NO_REPLY_EMAIL_CONTACT_FORM=
###< mailer ###

###> slack ###
SLACK_ENDPOINT=https://hooks.slack.com/services/PLACEHOLDER
SLACK_DISABLED=true
LOG_CHANNEL=#website_logs
###< slack ###

###> sms ###
GATEWAY_API_TOKEN=xxxxx
SMS_DISABLE=true
###< sms ###

###> google ###
GOOGLE_API_CLIENT_ID=xxxxx
GOOGLE_API_CLIENT_SECRET=xxxxx
GOOGLE_API_REFRESH_TOKEN=xxxxx
###< google ###

###> recaptcha ###
RECAPTCHA_PUBLIC_KEY=
RECAPTCHA_PRIVATE_KEY=
###< recaptcha ###

###> misc ###
IPINFO_TOKEN=
GEO_IGNORED_ASNS=[]
###< misc ###
```

**Step 2: Create `.env.staging`**

```dotenv
APP_ENV=staging
DATABASE_DRIVER=pdo_mysql
DATABASE_URL=mysql://vektor:vektor@mysql:3306/vektor
DATABASE_HOST=mysql
DATABASE_PORT=3306
DATABASE_NAME=vektor
DATABASE_USER=vektor
DATABASE_PASSWORD=vektor
DATABASE_PATH=
SLACK_DISABLED=true
SMS_DISABLE=true
RECAPTCHA_PUBLIC_KEY=
RECAPTCHA_PRIVATE_KEY=
```

**Step 3: Update `config/packages/doctrine.yaml`**

Replace the current `%database_*%` parameter references with env vars:

```yaml
doctrine:
    dbal:
        driver: '%env(DATABASE_DRIVER)%'
        host: '%env(DATABASE_HOST)%'
        port: '%env(DATABASE_PORT)%'
        dbname: '%env(DATABASE_NAME)%'
        user: '%env(DATABASE_USER)%'
        password: '%env(DATABASE_PASSWORD)%'
        path: '%env(DATABASE_PATH)%'
        charset: utf8mb4
        default_table_options:
            charset: utf8mb4
            collate: utf8mb4_unicode_ci
        mapping_types:
            enum: string
    orm:
        auto_generate_proxy_classes: '%kernel.debug%'
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                is_bundle: false
                type: attribute
                dir: '%kernel.project_dir%/src/App/Entity'
                prefix: 'App\Entity'
                alias: App
```

**Step 4: Update `config/services.yaml`**

Remove the `imports: [{ resource: parameters.yml }]` line and replace all `%param_name%` references with `%env(ENV_VAR)%`:

Key replacements in the parameters block:
- `'%google_api_client_id%'` → `'%env(GOOGLE_API_CLIENT_ID)%'`
- `'%google_api_client_secret%'` → `'%env(GOOGLE_API_CLIENT_SECRET)%'`
- `'%google_api_refresh_token%'` → `'%env(GOOGLE_API_REFRESH_TOKEN)%'`
- `'%slack_endpoint%'` → `'%env(SLACK_ENDPOINT)%'`
- `'%slack_disabled%'` → `'%env(bool:SLACK_DISABLED)%'`
- `'%log_channel%'` → `'%env(LOG_CHANNEL)%'`
- `'%gateway_api_token%'` → `'%env(GATEWAY_API_TOKEN)%'`
- `'%sms_disable%'` → `'%env(bool:SMS_DISABLE)%'`
- `'%default_from_email%'` → `'%env(DEFAULT_FROM_EMAIL)%'`
- `'%economy_email%'` → `'%env(ECONOMY_EMAIL)%'`
- `'%default_survey_email%'` → `'%env(DEFAULT_SURVEY_EMAIL)%'`
- `'%ipinfo_token%'` → `'%env(IPINFO_TOKEN)%'`
- `'%geo_ignored_asns%'` → `'%env(json:GEO_IGNORED_ASNS)%'`
- `'%recaptcha_public_key%'` → `'%env(RECAPTCHA_PUBLIC_KEY)%'`
- `'%recaptcha_private_key%'` → `'%env(RECAPTCHA_PRIVATE_KEY)%'`
- `'%jwt_passphrase%'` → `'%env(JWT_PASSPHRASE)%'`

Keep static parameters (paths, admission_notifier_limit, session.name, remember_me.name) as parameters — they don't vary by environment.

**Step 5: Update `config/packages/lexik_jwt_authentication.yaml`**

```yaml
lexik_jwt_authentication:
    secret_key: '%kernel.project_dir%/config/jwt/private.pem'
    public_key: '%kernel.project_dir%/config/jwt/public.pem'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
```

**Step 6: Update `config/packages/ewz_recaptcha.yaml`** (if it references parameters)

Check all files in `config/packages/` that reference `%recaptcha_*%` and update to env syntax.

**Step 7: Update `.gitignore`**

Add `.env.local` and `.env.*.local` (Symfony convention — local overrides, never committed). Remove `config/parameters.yml` ignore line if present.

```
# .env.local is never committed (secrets)
.env.local
.env.*.local
```

**Step 8: Remove `config/parameters.yml.dist`**

No longer needed — `.env` serves as the template.

**Step 9: Update `config/.gitignore`**

Remove the `/parameters.yml` line.

**Step 10: Run tests**

```bash
php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist --log-junit var/test-results.xml
```

Expected: 1030 tests pass. The test env uses `config/packages/test/doctrine.yaml` which hardcodes SQLite in-memory — unaffected by this migration.

**Step 11: Commit**

```bash
git add .env .env.staging .env.test config/ .gitignore
git commit -m "refactor: migrate parameters.yml to Symfony env vars

Replace legacy config/parameters.yml with .env files.
All environment-specific config now uses %env()% syntax.
Enables clean Docker/staging setup without generated parameter files."
```

---

### Task 2: Register staging as a Symfony environment

**Files:**
- Create: `config/packages/staging/monolog.yaml`
- Create: `config/packages/staging/framework.yaml`
- Verify: `config/packages/staging/ewz_recaptcha.yaml` (already exists)

**Step 1: Create staging monolog config**

File: `config/packages/staging/monolog.yaml`

```yaml
# Same as prod — structured logging, no debug
monolog:
    handlers:
        main:
            type: stream
            path: '%kernel.logs_dir%/%kernel.environment%.log'
            level: error
        console:
            type: console
            process_psr_3_messages: false
            channels: ['!event', '!doctrine']
```

**Step 2: Create staging framework config**

File: `config/packages/staging/framework.yaml`

```yaml
framework:
    rate_limiter:
        # Relaxed limits for staging QA — same as test env
        login:
            policy: token_bucket
            limit: 1000
            rate: { interval: '1 minute' }
        password_reset:
            policy: token_bucket
            limit: 1000
            rate: { interval: '1 minute' }
        contact_message:
            policy: token_bucket
            limit: 1000
            rate: { interval: '1 minute' }
        application:
            policy: token_bucket
            limit: 1000
            rate: { interval: '1 minute' }
```

**Step 3: Verify staging recaptcha config exists**

Already present at `config/packages/staging/ewz_recaptcha.yaml` with `enabled: false`. No changes needed.

**Step 4: Commit**

```bash
git add config/packages/staging/
git commit -m "feat: add staging Symfony environment config

Staging uses prod-like logging but relaxed rate limits and
disabled recaptcha for QA testing."
```

---

### Task 3: Generate JWT keys for Docker

JWT keys are gitignored. Docker needs them generated at build or boot time.

**Files:**
- Modify: `docker/entrypoint.sh` (created in Task 4)

JWT key generation command (will be included in entrypoint):

```bash
if [ ! -f config/jwt/private.pem ]; then
    mkdir -p config/jwt
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:"$JWT_PASSPHRASE" 2>/dev/null
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:"$JWT_PASSPHRASE" 2>/dev/null
    echo "JWT keys generated."
fi
```

No separate commit — included in Task 4.

---

### Task 4: Create entrypoint and Docker Compose

**Files:**
- Create: `docker/entrypoint.sh`
- Create: `docker-compose.yml`
- Modify: `Dockerfile`

**Step 1: Create `docker/entrypoint.sh`**

```bash
#!/bin/bash
set -e

echo "=== Vektorprogrammet Staging Entrypoint ==="

# Generate JWT keys if missing
if [ ! -f config/jwt/private.pem ]; then
    mkdir -p config/jwt
    openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 -pass pass:"$JWT_PASSPHRASE" 2>/dev/null
    openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:"$JWT_PASSPHRASE" 2>/dev/null
    echo "JWT keys generated."
fi

# Clear cache
php bin/console cache:clear --env="$APP_ENV" --no-debug

# Skip DB setup if SKIP_FIXTURES=1 (prod DB mode)
if [ "${SKIP_FIXTURES:-0}" != "1" ]; then
    echo "Waiting for MySQL..."
    until php bin/console doctrine:database:create --if-not-exists --env="$APP_ENV" 2>/dev/null; do
        sleep 2
    done

    # Check if schema exists (table count > 0)
    TABLE_COUNT=$(php bin/console doctrine:query:sql "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = '$DATABASE_NAME'" --env="$APP_ENV" 2>/dev/null | grep -o '[0-9]*' | tail -1 || echo "0")

    if [ "$TABLE_COUNT" -lt "5" ]; then
        echo "Creating schema..."
        php bin/console doctrine:schema:create --env="$APP_ENV"
        echo "Loading fixtures..."
        php bin/console doctrine:fixtures:load --env="$APP_ENV" -n
        echo "Fixtures loaded."
    else
        echo "Schema already exists ($TABLE_COUNT tables). Skipping fixtures."
    fi
fi

echo "Starting PHP server on 0.0.0.0:8000..."
exec php -S 0.0.0.0:8000 -t public
```

**Step 2: Make entrypoint executable**

```bash
chmod +x docker/entrypoint.sh
```

**Step 3: Update `Dockerfile`**

Replace existing Dockerfile with:

```dockerfile
FROM php:8.5-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libzip-dev libsqlite3-dev default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring xml zip pdo_sqlite pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js 22 via NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# PHP deps (include dev for fixtures)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts

# Node deps + build
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY assets/ ./assets/
RUN npm run build:prod

# App source
COPY . .
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
```

Note: `composer install` without `--no-dev` because fixtures are in `require-dev` (doctrine-fixtures-bundle). For a production image, add `--no-dev`.

**Step 4: Create `docker-compose.yml`**

```yaml
services:
  app:
    build: .
    ports:
      - "8000:8000"
    environment:
      APP_ENV: staging
      APP_SECRET: staging-secret-change-in-production
      DATABASE_DRIVER: pdo_mysql
      DATABASE_HOST: mysql
      DATABASE_PORT: "3306"
      DATABASE_NAME: vektor
      DATABASE_USER: vektor
      DATABASE_PASSWORD: vektor
      DATABASE_PATH: ""
      JWT_PASSPHRASE: staging-jwt-passphrase
      SLACK_DISABLED: "true"
      SMS_DISABLE: "true"
      DEFAULT_FROM_EMAIL: noreply@staging.vektorprogrammet.no
      ECONOMY_EMAIL: noreply@staging.vektorprogrammet.no
      DEFAULT_SURVEY_EMAIL: noreply@staging.vektorprogrammet.no
      SLACK_ENDPOINT: https://hooks.slack.com/services/disabled
      LOG_CHANNEL: "#staging"
      GATEWAY_API_TOKEN: disabled
      GOOGLE_API_CLIENT_ID: disabled
      GOOGLE_API_CLIENT_SECRET: disabled
      GOOGLE_API_REFRESH_TOKEN: disabled
      RECAPTCHA_PUBLIC_KEY: ""
      RECAPTCHA_PRIVATE_KEY: ""
      IPINFO_TOKEN: ""
      GEO_IGNORED_ASNS: "[]"
      NO_REPLY_EMAIL_USER_CREATION: ""
      NO_REPLY_EMAIL_CONTACT_FORM: ""
      MAILER_DSN: "null://null"
    depends_on:
      mysql:
        condition: service_healthy
    volumes:
      - jwt_keys:/app/config/jwt

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: vektor
      MYSQL_USER: vektor
      MYSQL_PASSWORD: vektor
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3307:3306"
    volumes:
      - mysql_data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 5s
      timeout: 5s
      retries: 10

volumes:
  mysql_data:
  jwt_keys:
```

**Step 5: Add Docker files to `.gitignore`**

Do NOT ignore docker-compose.yml or Dockerfile — they should be committed.

**Step 6: Test locally**

```bash
docker compose up --build
```

Expected: MySQL starts, app waits for healthy, schema created, fixtures loaded, server accessible at `http://localhost:8000`.

**Step 7: Commit**

```bash
git add docker/ docker-compose.yml Dockerfile
git commit -m "feat: add Docker Compose staging environment

- docker/entrypoint.sh: auto JWT keys, schema create, fixture load
- docker-compose.yml: app + mysql services with staging defaults
- Dockerfile: includes dev deps for fixtures, entrypoint-driven boot"
```

---

### Task 5: Create GitHub Actions staging deploy workflow

**Files:**
- Create: `.github/workflows/staging.yml`

**Step 1: Create the workflow**

File: `.github/workflows/staging.yml`

```yaml
name: Deploy Staging

on:
  push:
    branches:
      - staging

jobs:
  deploy:
    name: Deploy to staging
    runs-on: ubuntu-latest
    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.STAGING_HOST }}
          username: ${{ secrets.STAGING_USER }}
          key: ${{ secrets.STAGING_SSH_KEY }}
          script: |
            cd /opt/staging/vektorprogrammet
            git fetch origin staging
            git checkout staging
            git pull origin staging
            docker compose down
            docker compose up --build -d
            echo "Staging deployed at $(date)"
```

**Step 2: Document required GitHub secrets**

Add to design doc or README:
- `STAGING_HOST`: Droplet IP or `staging.vektorprogrammet.no`
- `STAGING_USER`: SSH user on droplet
- `STAGING_SSH_KEY`: Private SSH key for deployment

**Step 3: Commit**

```bash
git add .github/workflows/staging.yml
git commit -m "feat: add GitHub Actions staging auto-deploy

Deploys to staging droplet on push to staging branch.
Requires STAGING_HOST, STAGING_USER, STAGING_SSH_KEY secrets."
```

---

### Task 6: Droplet setup documentation

**Files:**
- Create: `docs/staging-setup.md`

**Step 1: Write droplet setup guide**

```markdown
# Staging Server Setup

One-time setup for the DigitalOcean droplet.

## Prerequisites

- Docker and Docker Compose installed
- Caddy installed (for reverse proxy + auto-TLS)
- DNS: `staging.vektorprogrammet.no` → droplet IP

## Initial Setup

```bash
# Clone repo
mkdir -p /opt/staging
cd /opt/staging
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet
git checkout staging

# Start services
docker compose up -d
```

## Caddy Config

Add to `/etc/caddy/Caddyfile`:

```
staging.vektorprogrammet.no {
    reverse_proxy localhost:8000
}
```

Then reload: `sudo systemctl reload caddy`

## GitHub Secrets

Set these in the repo settings (Settings → Secrets → Actions):

- `STAGING_HOST`: droplet IP
- `STAGING_USER`: deploy user (e.g., `deploy`)
- `STAGING_SSH_KEY`: contents of `~/.ssh/id_ed25519` for the deploy user

## Usage

Push to the `staging` branch to trigger auto-deploy:

```bash
git push origin HEAD:staging
```

## Prod DB Smoke Test

Override DATABASE_* vars to point at production (read-only recommended):

```bash
DATABASE_HOST=prod-db-host DATABASE_USER=readonly DATABASE_PASSWORD=... \
  SKIP_FIXTURES=1 docker compose up app
```

## Reset Staging Data

```bash
docker compose down -v  # removes mysql volume
docker compose up -d    # fresh fixtures
```
```

**Step 2: Commit**

```bash
git add docs/staging-setup.md
git commit -m "docs: add staging server setup guide

Covers droplet setup, Caddy config, GitHub secrets, prod DB smoke test."
```

---

### Task 7: Local validation

**Step 1: Build and start**

```bash
docker compose up --build
```

Expected: app accessible at `http://localhost:8000`

**Step 2: Verify pages load**

- Homepage: `http://localhost:8000/`
- Login: `http://localhost:8000/login`
- API: `http://localhost:8000/api/`

**Step 3: Verify login with fixture users**

Check `src/App/DataFixtures/ORM/LoadUserData.php` for test credentials. Try logging in.

**Step 4: Verify API endpoints**

```bash
# Get JWT token
curl -X POST http://localhost:8000/api/login \
  -H 'Content-Type: application/json' \
  -d '{"username":"admin","password":"1234"}'

# Hit an API endpoint with the token
curl http://localhost:8000/api/me \
  -H 'Authorization: Bearer <token>'
```

**Step 5: Run test suite inside container**

```bash
docker compose exec app php -d memory_limit=512M bin/phpunit -c phpunit.xml.dist
```

Expected: 1030 tests, 3095 assertions, 0 failures.

Note: Tests use in-memory SQLite (test/doctrine.yaml), not the MySQL container. This verifies the app boots and tests pass in the Docker environment.

**Step 6: Final commit (if any fixes needed)**

```bash
git commit -m "fix: staging environment adjustments from local validation"
```
