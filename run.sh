#!/bin/bash
set -e

echo "==> Installing bundle assets..."
php bin/console assets:install public --no-interaction

echo "==> Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "==> Clearing cache..."
php bin/console cache:clear --env=prod

echo "==> Starting web server on port ${PORT:-8080}..."
php -S 0.0.0.0:${PORT:-8080} -t public
