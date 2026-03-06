# Staging Environment Design

**Date**: 2026-03-05
**Status**: Approved
**Goal**: Enable safe validation of PR #1592 (and future changes) before production deployment.

## Context

- Production: DigitalOcean droplet, bare PHP + MySQL, deploy.sh pulls master
- No staging environment exists — main = production
- PR #1592 has 14 commits of modernization work blocked on validation
- Need: local replication, team QA access, test suite in prod-like env, prod DB smoke testing

## Architecture

```
docker-compose.yml
├── app    (PHP 8.5-cli, Vite-built assets, php -S on :8000)
└── mysql  (8.0, fixtures loaded on first boot)
```

On droplet: Caddy reverse proxy for `staging.vektorprogrammet.no` with auto-TLS.

## Components

### Dockerfile (multi-target)

Update existing Dockerfile to support staging:
- `base` stage: PHP 8.5-cli, system deps, Composer, Node 22
- `staging` target: copies app, builds assets, installs entrypoint
- Uses `php -S 0.0.0.0:8000 -t public` (matches production)

### docker-compose.yml

```yaml
services:
  app:
    build: .
    ports: ["8000:8000"]
    environment:
      APP_ENV: prod
      DATABASE_URL: mysql://vektor:vektor@mysql:3306/vektor
    depends_on:
      mysql:
        condition: service_healthy

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: vektor
      MYSQL_USER: vektor
      MYSQL_PASSWORD: vektor
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql
    healthcheck:
      test: mysqladmin ping -h localhost
      interval: 5s
      retries: 10

volumes:
  mysql_data:
```

### docker/entrypoint.sh

On first boot:
1. Wait for MySQL healthy
2. `php bin/console doctrine:schema:create`
3. `php bin/console doctrine:fixtures:load -n`
4. `php bin/console cache:clear --env=prod`
5. Start PHP built-in server

### Two modes

| Mode | Command | Database |
|------|---------|----------|
| Fixtures (default) | `docker compose up` | Fresh MySQL + fixtures |
| Prod snapshot | `DATABASE_URL=mysql://...prod... docker compose up app` | Skip local MySQL, connect to prod |

### Droplet deployment

- Caddy config: `staging.vektorprogrammet.no` reverse proxy to `localhost:8000`
- Auto-TLS via Caddy (Let's Encrypt)
- `docker compose up -d` from staging branch checkout

### GitHub Actions: staging.yml

Trigger: push to `staging` branch.
Steps:
1. SSH to droplet
2. `cd /opt/staging && git pull`
3. `docker compose build && docker compose up -d`

## Validation workflow

1. **Local dev**: `docker compose up` → browse `localhost:8000`
2. **Test suite**: `docker compose exec app php -d memory_limit=512M bin/phpunit` (1030 tests against MySQL)
3. **Team QA**: Push to `staging` branch → auto-deploys to `staging.vektorprogrammet.no`
4. **Prod DB smoke test**: Override `DATABASE_URL` to production, verify app boots and pages load

## New files

| File | Purpose |
|------|---------|
| `docker-compose.yml` | Service orchestration |
| `docker/entrypoint.sh` | Schema create + fixture load on first boot |
| `.github/workflows/staging.yml` | Auto-deploy on staging branch push |
| `config/packages/staging/` | Staging-specific config (if needed) |

## Acceptance criteria

- [ ] `docker compose up` starts app + mysql, fixtures loaded, accessible at localhost:8000
- [ ] Login works with fixture test users
- [ ] API endpoints respond at /api/*
- [ ] `docker compose exec app composer test` — 1030 tests pass
- [ ] Prod DB override works: `DATABASE_URL=... docker compose up app`
- [ ] Staging branch push triggers deploy to `staging.vektorprogrammet.no`
- [ ] Team members can access staging URL and QA the app
