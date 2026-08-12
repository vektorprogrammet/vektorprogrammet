#!/bin/sh
set -eu

: "${DATABASE_NAME:=vektor_p20}"
: "${DATABASE_USER:=vektor_p20}"
: "${DATABASE_PASSWORD:=vektor_p20}"
: "${DATABASE_ROOT_PASSWORD:=vektor_p20_root}"
: "${MARIADB_HOST:=127.0.0.1}"
: "${MARIADB_PORT:=3306}"
: "${SEED_OUTPUT_DIR:=/app/var/preview-seed}"
: "${MIGRATION_ENV:=prod}"
: "${HTTP_PORT:=8000}"
: "${PREVIEW_SOURCE_SHA:=unknown}"
: "${PREVIEW_IMAGE_DIGEST:=unknown}"
: "${PREVIEW_SEED_DIGEST:=unknown}"
: "${APP_SECRET:=p20-preview-app-secret}"
: "${DATABASE_URL:=mysql://${DATABASE_USER}:${DATABASE_PASSWORD}@${MARIADB_HOST}:${MARIADB_PORT}/${DATABASE_NAME}}"
: "${SLACK_DISABLED:=true}"
: "${SMS_DISABLE:=true}"
: "${RECAPTCHA_PUBLIC_KEY:=}"
: "${RECAPTCHA_PRIVATE_KEY:=}"
: "${GOOGLE_API_CLIENT_ID:=}"
: "${GOOGLE_API_CLIENT_SECRET:=}"
: "${GOOGLE_API_REFRESH_TOKEN:=}"
: "${LOG_CHANNEL:=preview}"
: "${GATEWAY_API_TOKEN:=}"
: "${DEFAULT_SURVEY_EMAIL:=preview@example.invalid}"
: "${IPINFO_TOKEN:=}"
: "${GEO_IGNORED_ASNS:='[]'}"
: "${DEFAULT_FROM_EMAIL:=preview@example.invalid}"
: "${ECONOMY_EMAIL:=preview@example.invalid}"
: "${SLACK_ENDPOINT:=http://127.0.0.1/disabled}"
: "${JWT_PASSPHRASE:=p20-preview-jwt-passphrase}"
: "${CORS_ALLOW_ORIGIN:=https://p20.vektor.phibkro.org}"
export DATABASE_URL APP_SECRET SLACK_DISABLED SMS_DISABLE RECAPTCHA_PUBLIC_KEY RECAPTCHA_PRIVATE_KEY \
  GOOGLE_API_CLIENT_ID GOOGLE_API_CLIENT_SECRET GOOGLE_API_REFRESH_TOKEN LOG_CHANNEL GATEWAY_API_TOKEN \
  DEFAULT_SURVEY_EMAIL IPINFO_TOKEN GEO_IGNORED_ASNS DEFAULT_FROM_EMAIL ECONOMY_EMAIL SLACK_ENDPOINT \
  JWT_PASSPHRASE CORS_ALLOW_ORIGIN

case "${PREVIEW_HOST:-p20.vektor.phibkro.org}" in
  *vektorprogrammet.no*) echo 'refusing forbidden preview host' >&2; exit 1 ;;
esac
[ "${PREVIEW_CONTAINER_NAME:-vektor-p20-container}" = "vektor-p20-container" ] || { echo 'refusing unexpected container name' >&2; exit 1; }
[ "${PREVIEW_RESOURCE_PREFIX:-vektor-p20}" = "vektor-p20" ] || { echo 'refusing unexpected resource prefix' >&2; exit 1; }
is_safe_identifier() { case "$1" in ''|*[!A-Za-z0-9_]*|[0-9]*) return 1;; esac; }
is_safe_identifier "$DATABASE_NAME" || { echo 'invalid DATABASE_NAME' >&2; exit 1; }
is_safe_identifier "$DATABASE_USER" || { echo 'invalid DATABASE_USER' >&2; exit 1; }

if command -v mariadbd >/dev/null 2>&1 && ! pgrep -x mariadbd >/dev/null 2>&1; then
  install -d -o mysql -g mysql "${MARIADB_DATA_DIR:-/var/lib/mysql}"
  if [ ! -d "${MARIADB_DATA_DIR:-/var/lib/mysql}/mysql" ]; then mariadb-install-db --user=mysql --datadir="${MARIADB_DATA_DIR:-/var/lib/mysql}" >/tmp/mariadb-init.log 2>&1; fi
  mariadbd --user=mysql --datadir="${MARIADB_DATA_DIR:-/var/lib/mysql}" --bind-address="${MARIADB_HOST}" --port="${MARIADB_PORT}" >/tmp/mariadb-preview.log 2>&1 &
fi
wait_for_mariadb() {
  i=0
  while ! mariadb-admin ping -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u root --protocol=tcp --password="${DATABASE_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    i=$((i + 1)); [ "$i" -le "${MARIADB_WAIT_ATTEMPTS:-60}" ] || { echo 'MariaDB readiness timeout' >&2; exit 1; }; sleep 1
  done
}
wait_for_mariadb
mariadb -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u root --protocol=tcp --password="${DATABASE_ROOT_PASSWORD}" -e "ALTER USER root@localhost IDENTIFIED BY '${DATABASE_ROOT_PASSWORD}';" 2>/dev/null || true
mariadb -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u root --protocol=tcp --password="${DATABASE_ROOT_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS \`${DATABASE_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS '${DATABASE_USER}'@'%' IDENTIFIED BY '${DATABASE_PASSWORD}'; GRANT ALL PRIVILEGES ON \`${DATABASE_NAME}\`.* TO '${DATABASE_USER}'@'%'; FLUSH PRIVILEGES;"
if [ "${SKIP_MIGRATIONS:-0}" != "1" ]; then php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env="${MIGRATION_ENV}"; fi
[ -f "${SEED_OUTPUT_DIR}/seed.sql" ] || { echo "missing generated seed artifact: ${SEED_OUTPUT_DIR}/seed.sql" >&2; exit 1; }
[ -f "${SEED_OUTPUT_DIR}/manifest.json" ] || { echo "missing generated seed manifest: ${SEED_OUTPUT_DIR}/manifest.json" >&2; exit 1; }
SEED_SHA256=$(sha256sum "${SEED_OUTPUT_DIR}/seed.sql" | cut -d ' ' -f 1)
EXPECTED_SHA256=$(php -r '$m=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); echo $m["digests"]["artifactSha256"];' "${SEED_OUTPUT_DIR}/manifest.json")
[ "${SEED_SHA256}" = "${EXPECTED_SHA256}" ] || { echo 'seed artifact digest mismatch' >&2; exit 1; }
mariadb -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u "${DATABASE_USER}" --protocol=tcp --password="${DATABASE_PASSWORD}" "${DATABASE_NAME}" < "${SEED_OUTPUT_DIR}/seed.sql"
TABLE_COUNT=$(mariadb -N -s -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u "${DATABASE_USER}" --protocol=tcp --password="${DATABASE_PASSWORD}" "${DATABASE_NAME}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${DATABASE_NAME}'")
[ "$TABLE_COUNT" -ge 65 ] || { echo "migration/seed table count too low: ${TABLE_COUNT}" >&2; exit 1; }
echo "preview MariaDB ready: database=${DATABASE_NAME} container=vektor-p20-container tables=${TABLE_COUNT} seed_sha256=${SEED_SHA256} replacement_rehydrates=true"
if [ "${START_SYMFONY:-1}" = "1" ]; then
  php-fpm -D
  exec nginx -g 'daemon off;'
fi
