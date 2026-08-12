#!/usr/bin/env bash
set -Eeuo pipefail

: "${DATABASE_URL:?DATABASE_URL is required}"
: "${PREVIEW_SEED_DIR:=/tmp/preview-seed}"
: "${PREVIEW_APP:=vektor}"
: "${PREVIEW_STAGE:=p20}"
: "${PREVIEW_SOURCE_SHA:=unknown}"
: "${PREVIEW_IMAGE_DIGEST:=unknown}"
: "${PREVIEW_SEED_DIGEST:=unknown}"

if [[ "${PREVIEW_APP}" != "vektor" || "${PREVIEW_STAGE}" != "p20" ]]; then
  echo "Preview identity mismatch" >&2
  exit 64
fi
if [[ "${DATABASE_URL}" == *vektorprogrammet.no* || "${PREVIEW_SEED_DIR}" == *backup* ]]; then
  echo "Forbidden preview input" >&2
  exit 65
fi

install -d -o mysql -g mysql /run/mysqld /var/lib/mysql
if [[ ! -d /var/lib/mysql/mysql ]]; then
  mariadb-install-db --user=mysql --datadir=/var/lib/mysql --skip-test-db >/dev/null
fi
mariadbd --user=mysql --datadir=/var/lib/mysql --bind-address=127.0.0.1 --port=3306 >/tmp/mariadb.log 2>&1 &
for attempt in {1..60}; do
  mariadb-admin --protocol=tcp --host=127.0.0.1 --port=3306 ping >/dev/null 2>&1 && break
  [[ "$attempt" == 60 ]] && { cat /tmp/mariadb.log >&2; exit 70; }
  sleep 1
done

php bin/console doctrine:database:create --if-not-exists --env=prod --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod
if [[ ! -f "${PREVIEW_SEED_DIR}/synthetic-seed.sql" ]]; then
  echo "Digest-bound synthetic seed artifact is missing" >&2
  exit 71
fi
php bin/console doctrine:query:sql "SOURCE ${PREVIEW_SEED_DIR}/synthetic-seed.sql" --env=prod >/dev/null 2>&1 || mariadb --protocol=tcp --host=127.0.0.1 --port=3306 < "${PREVIEW_SEED_DIR}/synthetic-seed.sql"

export PREVIEW_DATABASE_AUTHORITY=container-local-mariadb
export PREVIEW_REHYDRATION=synthetic-seed-on-container-replacement
php bin/console cache:clear --env=prod --no-debug
php-fpm -D
exec nginx -g 'daemon off;'
