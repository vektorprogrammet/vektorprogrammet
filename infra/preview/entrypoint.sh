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

case "${PREVIEW_HOST:-p20.vektor.phibkro.org}" in
  *vektorprogrammet.no*) echo 'refusing forbidden preview host' >&2; exit 1 ;;
esac
[ "${PREVIEW_CONTAINER_NAME:-vektor-p20-container}" = "vektor-p20-container" ] || { echo 'refusing unexpected container name' >&2; exit 1; }
[ "${PREVIEW_RESOURCE_PREFIX:-vektor-p20}" = "vektor-p20" ] || { echo 'refusing unexpected resource prefix' >&2; exit 1; }

if command -v mariadbd >/dev/null 2>&1 && ! pgrep -x mariadbd >/dev/null 2>&1; then
  mariadbd --user="${MARIADB_OS_USER:-mysql}" --datadir="${MARIADB_DATA_DIR:-/var/lib/mysql}" --bind-address="${MARIADB_HOST}" --port="${MARIADB_PORT}" >/tmp/mariadb-preview.log 2>&1 &
fi

wait_for_mariadb() {
  i=0
  while ! mariadb-admin ping -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u root --password="${DATABASE_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    i=$((i + 1)); [ "$i" -le "${MARIADB_WAIT_ATTEMPTS:-60}" ] || { echo 'MariaDB readiness timeout' >&2; exit 1; }; sleep 1
  done
}
wait_for_mariadb

mariadb -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u root --password="${DATABASE_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${DATABASE_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DATABASE_USER}'@'localhost' IDENTIFIED BY '${DATABASE_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DATABASE_NAME}\`.* TO '${DATABASE_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

if [ "${SKIP_MIGRATIONS:-0}" != "1" ]; then
  php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env="${MIGRATION_ENV}"
fi

[ -f "${SEED_OUTPUT_DIR}/seed.sql" ] || { echo "missing generated seed artifact: ${SEED_OUTPUT_DIR}/seed.sql" >&2; exit 1; }
[ -f "${SEED_OUTPUT_DIR}/manifest.json" ] || { echo "missing generated seed manifest: ${SEED_OUTPUT_DIR}/manifest.json" >&2; exit 1; }
SEED_SHA256=$(sha256sum "${SEED_OUTPUT_DIR}/seed.sql" | cut -d ' ' -f 1)
EXPECTED_SHA256=$(php -r '$m=json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); echo $m["digests"]["artifactSha256"];' "${SEED_OUTPUT_DIR}/manifest.json")
[ "${SEED_SHA256}" = "${EXPECTED_SHA256}" ] || { echo 'seed artifact digest mismatch' >&2; exit 1; }
mariadb -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u "${DATABASE_USER}" --password="${DATABASE_PASSWORD}" "${DATABASE_NAME}" < "${SEED_OUTPUT_DIR}/seed.sql"

TABLE_COUNT=$(mariadb -N -s -h "${MARIADB_HOST}" -P "${MARIADB_PORT}" -u "${DATABASE_USER}" --password="${DATABASE_PASSWORD}" "${DATABASE_NAME}" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '${DATABASE_NAME}'")
[ "$TABLE_COUNT" -ge 65 ] || { echo "migration/seed table count too low: ${TABLE_COUNT}" >&2; exit 1; }
echo "preview MariaDB ready: database=${DATABASE_NAME} container=vektor-p20-container tables=${TABLE_COUNT} seed_sha256=${SEED_SHA256} replacement_rehydrates=true"

if [ "${START_SYMFONY:-1}" = "1" ]; then
  exec php -S "0.0.0.0:${HTTP_PORT}" -t public
fi
