#!/usr/bin/env sh
set -e

cd /app

# Wait for DB if DB_HOST is set (helps when Coolify boots services in parallel)
if [ -n "${DB_HOST:-}" ] && [ -n "${DB_PORT:-}" ]; then
  echo "Waiting for database ${DB_HOST}:${DB_PORT}..."
  for i in $(seq 1 30); do
    if php -r "exit(@fsockopen(getenv('DB_HOST'), (int)getenv('DB_PORT')) ? 0 : 1);"; then
      echo "Database is reachable."
      break
    fi
    sleep 2
  done
fi

# Generate APP_KEY if missing (idempotent — only on first boot if not provided)
if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is empty — generating a new one (set APP_KEY in Coolify to persist it)."
  php artisan key:generate --force --no-interaction || true
fi

# Run framework-level setup only for the web role (queue/reverb skip these)
ROLE="${CONTAINER_ROLE:-web}"

if [ "$ROLE" = "web" ]; then
  php artisan storage:link || true
  php artisan migrate --force --no-interaction || true
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache || true
fi

case "$ROLE" in
  queue)
    exec php artisan queue:work --tries=3 --timeout=90 --sleep=3
    ;;
  reverb)
    exec php artisan reverb:start --host=0.0.0.0 --port="${REVERB_SERVER_PORT:-8080}"
    ;;
  scheduler)
    exec php artisan schedule:work
    ;;
  web|*)
    exec "$@"
    ;;
esac
