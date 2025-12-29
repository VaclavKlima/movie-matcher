# Deployment Guide

## Local Development

### Initial Setup
```bash
# Clone the repository
git clone <your-repo-url>
cd moviematcher

# Start the application
docker-compose up --build
```

The application will be available at http://localhost:8000

### Daily Development
```bash
# Start containers
docker-compose up

# Stop containers
docker-compose down

# View logs
docker-compose logs -f
```

---

## Production Deployment (Portainer)

### Initial Setup in Portainer

1. **Create a new Stack** in Portainer
2. **Select "Repository"** as the build method
3. **Enter your Git repository URL**
4. **Set the repository reference** (branch): `main`
5. **Compose path**: `docker-compose.yml`
6. **Environment variables** (override defaults):
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```
7. Click **"Deploy the stack"**

### What Happens on First Deploy

The entrypoint script automatically handles:
- ✅ Creates `.env` file from `.env.example`
- ✅ Generates Laravel application key (`APP_KEY`)
- ✅ Installs Composer dependencies
- ✅ Creates SQLite database
- ✅ Runs database migrations
- ✅ Installs npm dependencies (if needed)
- ✅ Builds frontend assets (if needed)
- ✅ Optimizes for production (caches config/routes/views)

### Updating from Git

When you push changes to Git and redeploy in Portainer:

1. **Push your changes to Git**:
   ```bash
   git add .
   git commit -m "Your changes"
   git push origin main
   ```

2. **In Portainer**:
   - Go to your stack
   - Click **"Pull and redeploy"** or **"Update the stack"**
   - Portainer will pull the latest code and rebuild

3. **The entrypoint automatically handles**:
   - ✅ Updates Composer dependencies
   - ✅ Clears all caches (config, routes, views, app cache)
   - ✅ Runs new database migrations
   - ✅ Rebuilds frontend assets (if package.json changed)
   - ✅ Re-optimizes for production

### Manual Commands (if needed)

If you need to run commands manually inside the container:

```bash
# Access container shell
docker exec -it moviematcher-app-1 bash

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate new app key (⚠️ Warning: invalidates sessions)
php artisan key:generate --force
```

### Viewing Logs in Portainer

1. Go to **Containers**
2. Click on your container name (e.g., `moviematcher-app-1`)
3. Click **"Logs"** to view application logs

---

## Troubleshooting

### "MissingAppKeyException" Error
✅ **Fixed automatically** - The entrypoint now generates the key if missing

### Container keeps restarting
```bash
# Check logs in Portainer or via CLI:
docker logs moviematcher-app-1

# Common issues:
# - Missing .env file → Auto-created by entrypoint
# - Database errors → Check migrations
# - Permission errors → Check volume permissions
```

### Changes not reflected after update
```bash
# Make sure to "Pull and redeploy" in Portainer
# This rebuilds the image with the latest code

# If still not working, try:
# 1. Stop the stack
# 2. Remove the stack
# 3. Recreate it (will pull latest code)
```

### Database issues
```bash
# Access container
docker exec -it moviematcher-app-1 bash

# Check database exists
ls -la database/database.sqlite

# Re-run migrations
php artisan migrate:fresh --force  # ⚠️ Warning: drops all tables
```

---

## Environment Variables

Key environment variables you can override in Portainer:

| Variable | Default | Production Value |
|----------|---------|------------------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://localhost:8000` | `https://your-domain.com` |
| `LOG_LEVEL` | `debug` | `error` or `warning` |

---

## File Permissions

The following directories need write permissions:
- `storage/` - Logs, cache, sessions
- `database/` - SQLite database file

These are handled automatically via Docker volumes defined in `docker-compose.yml`.
