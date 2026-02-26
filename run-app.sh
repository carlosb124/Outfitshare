#!/bin/bash
# Install JS vendor assets (gitignored)
php bin/console importmap:install

# Install bundle assets (EasyAdmin, etc.)
php bin/console assets:install public --no-interaction

# Build assets
php bin/console tailwind:build --minify
php bin/console asset-map:compile

# Create uploads directory
mkdir -p public/uploads/images

# Run migrations and clear cache
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod

# Start server
if command -v nginx &> /dev/null && [ -f /assets/scripts/prestart.mjs ]; then
    node /assets/scripts/prestart.mjs /assets/nginx.template.conf /nginx.conf
    php-fpm -y /assets/php-fpm.conf &
    nginx -c /nginx.conf
else
    php -S 0.0.0.0:${PORT:-8080} -t public
fi
