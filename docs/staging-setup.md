# Staging Server Setup

One-time setup for the DigitalOcean droplet.

## Prerequisites

- Docker and Docker Compose installed
- Caddy installed (for reverse proxy + auto-TLS)
- DNS: `staging.vektorprogrammet.no` pointing to droplet IP

## Initial Setup

```bash
mkdir -p /opt/staging
cd /opt/staging
git clone https://github.com/vektorprogrammet/vektorprogrammet.git
cd vektorprogrammet
git checkout staging
docker compose up -d
```

## Caddy Config

Add to `/etc/caddy/Caddyfile`:

```
staging.vektorprogrammet.no {
    reverse_proxy localhost:8000
}
```

Reload: `sudo systemctl reload caddy`

## GitHub Secrets

Set in repo Settings > Secrets > Actions:

- `STAGING_HOST`: droplet IP
- `STAGING_USER`: deploy user (e.g., `deploy`)
- `STAGING_SSH_KEY`: contents of `~/.ssh/id_ed25519` for the deploy user

## Deploy

Push to the `staging` branch to trigger auto-deploy:

```bash
git push origin HEAD:staging
```

## Prod DB Smoke Test

Override DATABASE_* vars to point at production (read-only recommended):

```bash
DATABASE_HOST=prod-db-host DATABASE_USER=readonly DATABASE_PASSWORD=secret \
  SKIP_FIXTURES=1 docker compose up app
```

## Reset Staging Data

```bash
docker compose down -v   # removes mysql volume
docker compose up -d     # fresh fixtures
```

## Run Tests in Container

```bash
docker compose exec app php -d memory_limit=512M bin/phpunit
```
