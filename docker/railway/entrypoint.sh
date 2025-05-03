#!/bin/sh
set -e

# Naudojamas PORT (Railway nustato jį automatiškai)
if [ "$PORT" = "" ]; then
  PORT=8000
fi

echo "Using PORT from environment: $PORT"

# Aplinkos kintamųjų tikrinimas
if [ "$DATABASE_URL" != "" ]; then
  echo "Using DATABASE_URL from environment"
fi

if [ "$APP_ENV" = "prod" ]; then
  echo "Running in production mode"
  php bin/console cache:clear --no-warmup
  php bin/console cache:warmup
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
  echo "Running database migrations"
  php bin/console doctrine:migrations:migrate --no-interaction
fi

# Galiausiai paleidžiam PHP built-in serverį
exec php -S 0.0.0.0:$PORT -t public/
