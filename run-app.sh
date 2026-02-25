#!/bin/bash
# Run migrations and start the application
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod

# Start PHP-FPM and Nginx (Railway's Nixpacks provides both)
# If Nginx is available, use it; otherwise fallback to PHP built-in server
if command -v nginx &> /dev/null && [ -f /assets/scripts/prestart.mjs ]; then
    node /assets/scripts/prestart.mjs /assets/nginx.template.conf /nginx.conf
    php-fpm -y /assets/php-fpm.conf &
    nginx -c /nginx.conf
else
    php -S 0.0.0.0:${PORT:-8080} -t public
fi
