#!/usr/bin/env bash
set -euo pipefail

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -f database/database.sqlite ]; then
  mkdir -p database
  touch database/database.sqlite
fi

if [ -f package.json ] && [ ! -f public/build/manifest.json ]; then
  npm install
  npm run build
fi

php artisan migrate --force

exec "$@"
