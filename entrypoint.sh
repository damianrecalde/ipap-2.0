#!/bin/sh

set -e

cd /var/www/html

# Ejecutar composer install solo si composer.json existe
if [ -f composer.json ]; then
  echo "🏗 Ejecutando composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "⚠️ No se encontró composer.json en /var/www/html"
fi

# Lógica opcional: ejecutar migrations, build de frontend, etc.
# npm install && npm run build

# Iniciar PHP-FPM (debe ser el último comando)
exec php-fpm
