#!/usr/bin/env bash
set -Eeuo pipefail

# Run manually as root. Never grant the legacy HTTP webhook sudo access.
if (( EUID != 0 )); then
  echo "Run this deployment manually as root; HTTP webhook deployment is unsupported." >&2
  exit 1
fi

# Keep Bash's input stable when git updates this very file during deployment.
if [[ ${VEKTOR_DEPLOY_STABLE:-0} != 1 ]]; then
  export DEPLOY_APP_DIR=${DEPLOY_APP_DIR:-$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)}
  STABLE_SCRIPT=$(mktemp /tmp/vektor-deploy.XXXXXX)
  trap 'rm -f -- "$STABLE_SCRIPT"' EXIT
  cp -- "${BASH_SOURCE[0]}" "$STABLE_SCRIPT"
  export VEKTOR_DEPLOY_STABLE=1
  if bash "$STABLE_SCRIPT" "$@"; then
    exit 0
  else
    exit "$?"
  fi
fi

APP=${DEPLOY_APP_DIR:-$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)}
BRANCH=${DEPLOY_BRANCH:-master}
BACKUP_ROOT=${DEPLOY_BACKUP_DIR:-/root/vektor-deploy-backups}
HEALTH_URL=${DEPLOY_HEALTH_URL:-http://127.0.0.1:8081}
PHP_BIN=${DEPLOY_PHP_BIN:-php}
DATABASE=${DEPLOY_DATABASE:-vektorprogrammet}
FPM_SERVICE=${DEPLOY_FPM_SERVICE:-php8.5-fpm}
RUN_MIGRATIONS=${DEPLOY_RUN_MIGRATIONS:-0}
TARGET=${1:-${DEPLOY_COMMIT:-}}
BACKUP=
CHANGES_STARTED=0
TIMETABLE_BUNDLE=web/js/scheduling/build.js

cd "$APP"
git check-ref-format --branch "$BRANCH" >/dev/null
case "$RUN_MIGRATIONS" in 0|1) ;; *) echo "DEPLOY_RUN_MIGRATIONS must be 0 or 1." >&2; exit 1 ;; esac

umask 077
exec 9>"${DEPLOY_LOCK_FILE:-/run/lock/vektor-deploy.lock}"
flock -n 9 || { echo "Another deployment is running." >&2; exit 1; }

repair_permissions() {
  chgrp -R www-data "$APP" || return "$?"
  chmod -R g+rX "$APP" || return "$?"
  for path in var/cache var/logs web/images web/files web/img signatures; do
    if [[ -d "$APP/$path" ]]; then
      chown -R www-data:www-data "$APP/$path" || return "$?"
    fi
  done
  install -d -o www-data -g www-data -m 0750 "$APP/web/media/cache" || return "$?"
  chown -R www-data:www-data "$APP/web/media/cache" || return "$?"
}

finish() {
  local result=$?
  trap - EXIT
  if (( CHANGES_STARTED )); then
    # Restore only the frozen timetable artifact if a build changed or removed it.
    if [[ -s "$BACKUP/timetable-build.js" ]] &&
       ! cmp -s "$BACKUP/timetable-build.js" "$TIMETABLE_BUNDLE"; then
      echo "Timetable bundle changed unexpectedly; restoring the saved working bundle." >&2
      cp -- "$BACKUP/timetable-build.js" "$TIMETABLE_BUNDLE" || result=1
      result=1
    fi
    if ! repair_permissions; then
      echo "Permission repair failed." >&2
      result=1
    fi
  fi
  if (( result != 0 )); then
    echo "Deployment failed. Do not assume the site rolled back." >&2
    [[ -z "$BACKUP" ]] || echo "Backup and log: $BACKUP" >&2
  fi
  exit "$result"
}
trap finish EXIT

if [[ $(git branch --show-current) != "$BRANCH" ]]; then
  echo "Checkout must already be on $BRANCH. Set DEPLOY_BRANCH for a custom branch." >&2
  exit 1
fi
if ! git diff --quiet || ! git diff --cached --quiet; then
  echo "Tracked local changes exist; nothing reset or discarded." >&2
  exit 1
fi

for command in "$PHP_BIN" docker mysqldump rsync curl nginx systemctl; do
  command -v "$command" >/dev/null
done
"$PHP_BIN" -r 'if (PHP_VERSION_ID < 80512) { fwrite(STDERR, "PHP 8.5.12 or newer is required.\n"); exit(1); }'
systemctl is-active --quiet mysql
systemctl is-active --quiet "$FPM_SERVICE"
systemctl is-active --quiet nginx
docker info >/dev/null
nginx -t
curl -fsS --max-time 20 -o /dev/null "$HEALTH_URL/"
test -r app/config/parameters.yml

# This legacy Vue app cannot yet be rebuilt with matching production styling.
# Preserve its reviewed artifact; fail before changing code if it is missing.
if [[ ! -s "$TIMETABLE_BUNDLE" ]]; then
  echo "Missing timetable bundle: $TIMETABLE_BUNDLE. Restore the verified working artifact before deploying." >&2
  exit 1
fi

git fetch --no-tags origin "refs/heads/$BRANCH:refs/remotes/origin/$BRANCH"
COMMIT=$(git rev-parse --verify "${TARGET:-origin/$BRANCH}^{commit}")
if ! git merge-base --is-ancestor "$COMMIT" "origin/$BRANCH" ||
   ! git merge-base --is-ancestor HEAD "$COMMIT"; then
  echo "Target must be a forward commit on origin/$BRANCH." >&2
  exit 1
fi

install -d -m 700 "$BACKUP_ROOT"
BACKUP=$(mktemp -d "$BACKUP_ROOT/$(date -u +%Y%m%dT%H%M%SZ)-XXXXXX")
exec > >(tee -a "$BACKUP/deploy.log") 2>&1
git rev-parse HEAD > "$BACKUP/previous-commit.txt"
printf '%s\n' "$COMMIT" > "$BACKUP/target-commit.txt"

mysqldump --single-transaction --quick --no-tablespaces \
  --set-gtid-purged=OFF --hex-blob --default-character-set=utf8mb4 \
  --routines --events --triggers --databases "$DATABASE" > "$BACKUP/database.sql"
grep -q '^-- Dump completed on ' "$BACKUP/database.sql"
cp app/config/parameters.yml "$BACKUP/parameters.yml"
chmod 600 "$BACKUP/parameters.yml"
cp -- "$TIMETABLE_BUNDLE" "$BACKUP/timetable-build.js"
for path in web/files signatures; do
  if [[ -d "$path" ]]; then
    install -d -m 700 "$BACKUP/$path"
    rsync -a "$path/" "$BACKUP/$path/"
  fi
done

CHANGES_STARTED=1
git merge --ff-only "$COMMIT"

# Back up only image paths the asset build can overwrite, not all uploads.
install -d -m 700 "$BACKUP/image-overlaps"
comm -z -12 \
  <(find app/Resources/assets/images -type f -printf '%P\0' | LC_ALL=C sort -z) \
  <(find web/images -type f -printf '%P\0' | LC_ALL=C sort -z) \
  | rsync -a --from0 --files-from=- web/images/ "$BACKUP/image-overlaps/"

export COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=prod SYMFONY_ENV=prod SYMFONY_DEBUG=0
"$PHP_BIN" composer.phar install --no-dev --optimize-autoloader --no-interaction
"$PHP_BIN" composer.phar check-platform-reqs --no-dev
"$PHP_BIN" bin/console app:migrations:upgrade-metadata --check --env=prod --no-debug
if [[ "$RUN_MIGRATIONS" == 1 ]]; then
  "$PHP_BIN" bin/console doctrine:migrations:migrate --no-interaction --env=prod --no-debug
fi
"$PHP_BIN" bin/console doctrine:migrations:up-to-date --env=prod --no-debug

docker run --rm --name vektor-production-assets \
  --mount type=bind,src="$APP",dst=/app -w /app node:14.21.3-buster \
  bash -lc '
    set -euo pipefail
    NODE_ENV=development npm ci --ignore-scripts
    node node_modules/node-sass/scripts/install.js
    npm rebuild optipng-bin jpegtran-bin gifsicle --scripts-prepend-node-path=true
    test -x node_modules/optipng-bin/vendor/optipng
    test -x node_modules/jpegtran-bin/vendor/jpegtran
    test -x node_modules/gifsicle/vendor/gifsicle
    NODE_ENV=production node node_modules/gulp/bin/gulp.js build:prod 2>&1 | tee /tmp/vektor-gulp.log
    if grep -Eq "Plumber found unhandled error|Error in plugin|spawn .* ENOENT" /tmp/vektor-gulp.log; then
      echo "Gulp logged an error; stopping." >&2
      exit 1
    fi
  '

if ! cmp -s "$BACKUP/timetable-build.js" "$TIMETABLE_BUNDLE"; then
  echo "Asset build changed the frozen timetable bundle; stopping." >&2
  exit 1
fi

"$PHP_BIN" bin/console cache:clear --env=prod --no-debug
repair_permissions
nginx -t
curl -fsS --max-time 20 -o /dev/null "$HEALTH_URL/"
curl -fsS --max-time 20 -o /dev/null "$HEALTH_URL/images/HS_24.jpg"
# Check the actual JavaScript body, not just a 200 HTML/login response.
curl -fsS --max-time 20 -o "$BACKUP/timetable-served.js" "$HEALTH_URL/js/scheduling/build.js"
if ! cmp -s "$BACKUP/timetable-build.js" "$BACKUP/timetable-served.js"; then
  echo "Timetable HTTP response does not match the preserved JavaScript bundle." >&2
  exit 1
fi
CHANGES_STARTED=0
echo "PRODUCTION_DEPLOY_OK branch=$BRANCH commit=$COMMIT backup=$BACKUP"
