#!/usr/bin/env bash
set -euo pipefail

echo "🚀 Starting Laravel application setup..."

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
  echo "📝 Creating .env file from .env.example..."
  cp .env.example .env
fi

# Install composer dependencies FIRST (needed before running any artisan commands)
if [ ! -f vendor/autoload.php ]; then
  echo "📦 Installing composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "📦 Updating composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate APP_KEY if it's empty (after composer install)
if ! grep -q "APP_KEY=base64:" .env; then
  echo "🔑 Generating application key..."
  php artisan key:generate --force
fi

# Create database file if it doesn't exist
if [ ! -f database/database.sqlite ]; then
  echo "💾 Creating SQLite database..."
  mkdir -p database
  touch database/database.sqlite
  chmod 664 database/database.sqlite
fi

# Clear caches before migrations (important for updates)
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Build frontend assets if needed
if [ -f package.json ] && [ ! -d node_modules ]; then
  echo "📦 Installing npm dependencies..."
  npm install
fi

if [ -f package.json ] && [ ! -f public/build/manifest.json ]; then
  echo "🎨 Building frontend assets..."
  npm run build
fi

# Optimize for production
if [ "${APP_ENV:-local}" != "local" ]; then
  echo "⚡ Optimizing for production..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

echo "✅ Application setup complete!"

exec "$@"
