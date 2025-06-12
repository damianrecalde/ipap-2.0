#!/bin/sh

# Entrypoint para Symfony + NPM
set -e

echo "🏗 Ejecutando composer install..."
composer install --no-interaction

echo "📦 Ejecutando npm install..."
npm install

echo "🎛 Ejecutando npx encore dev..."
npx encore dev

# Ejecutar PHP-FPM (comando por defecto)
exec php-fpm
