#!/bin/sh
set -e

echo "Generating .env file"
cat > .env <<EOF
APP_ENV=${APP_ENV:-prod}
APP_SECRET=${APP_SECRET:-$(openssl rand -base64 32)}
DATABASE_URL=${DATABASE_URL}
EOF


if [ -n "${DATABASE_URL}" ]; then
  echo "Running migrations"
  php bin/console doctrine:migrations:migrate -n
fi

exec "$@"