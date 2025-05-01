#!/bin/sh
set -e

# Nustatome default PORT reikšmę jei neegzistuoja
export PORT=${PORT:-8000}

# Laukiama kol bus pasiruošę duomenų bazės kintamieji
sleep 5

# Migracijos
if [ -n "${DATABASE_URL}" ]; then
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

# Cache valymas
php bin/console cache:clear
php bin/console cache:warmup

# Paleidžiamas serveris
exec php -S 0.0.0.0:$PORT public/index.php