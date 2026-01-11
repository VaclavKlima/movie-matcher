#!/usr/bin/env bash
set -euo pipefail

# Send deployment starting notification
if [ -x /usr/local/bin/notify-discord.sh ]; then
  /usr/local/bin/notify-discord.sh starting
fi

echo "🚀 Starting Laravel application setup..."

# Wait for Redis to be ready (if REDIS_HOST is set)
if [ -n "${REDIS_HOST:-}" ]; then
  echo "⏳ Waiting for Redis..."
  REDIS_READY=0
  for i in {1..30}; do
    if redis-cli -h "$REDIS_HOST" -p "${REDIS_PORT:-6379}" ping > /dev/null 2>&1; then
      echo "✓ Redis is ready!"
      REDIS_READY=1
      break
    fi
    echo "   Waiting for Redis... (attempt $i/30)"
    sleep 1
  done

  if [ $REDIS_READY -eq 0 ]; then
    echo "⚠️  Warning: Redis not available after 30 seconds, continuing anyway..."
  fi
fi

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

# Ensure APP_KEY is stable across redeploys
if [ -n "${APP_KEY:-}" ]; then
  if grep -q "^APP_KEY=" .env; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
  else
    echo "APP_KEY=${APP_KEY}" >> .env
  fi
else
  # Generate APP_KEY if it's empty (after composer install)
  if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
  fi
fi

# Create database file if it doesn't exist
if [ ! -f database/data/database.sqlite ]; then
  echo "💾 Creating SQLite database..."
  mkdir -p database/data
  touch database/data/database.sqlite
  chmod 664 database/data/database.sqlite
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

# Send deployment success notification
if [ -x /usr/local/bin/notify-discord.sh ]; then
  /usr/local/bin/notify-discord.sh success
fi

exec "$@"
