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
6. **Environment variables**:
   ```
   APP_URL=https://your-domain.com
   CLOUDFLARE_TUNNEL_TOKEN=your-tunnel-token-here
   DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/YOUR_WEBHOOK_ID/YOUR_WEBHOOK_TOKEN
   ```
   Replace:
   - `your-domain.com` with your actual domain (e.g., `moviematcher.example.com`)
   - `your-tunnel-token-here` with the token from Cloudflare (the long string after `--token` in the command)
   - Discord webhook URL with your actual webhook URL (optional - for deployment notifications)
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

## Cloudflare Tunnel Setup

### What is Cloudflare Tunnel?

Cloudflare Tunnel creates a secure connection between your server and Cloudflare's network without exposing your server's IP address or opening firewall ports. Your app is accessible via your custom domain with Cloudflare's security and CDN benefits.

### Prerequisites

1. **Cloudflare account** with a domain added
2. **Cloudflare Tunnel created** in the Cloudflare dashboard
   - Go to: Cloudflare Dashboard → Zero Trust → Access → Tunnels
   - Click "Create a tunnel"
   - Name your tunnel (e.g., "moviematcher")
   - Copy the tunnel token (long string starting with `eyJ...`)

### Setup Steps

#### 1. Configure Public Hostname in Cloudflare

In your Cloudflare Tunnel settings:
- **Public hostname**: `moviematcher.yourdomain.com` (or your preferred subdomain)
- **Service Type**: `HTTP`
- **URL**: `app:8000` (this connects to your Docker container via internal network)

#### 2. Add Environment Variable in Portainer

In your Portainer stack's environment variables:
```
CLOUDFLARE_TUNNEL_TOKEN=eyJhIjoiMzMyNGJlYWZkYTFmODRjNzYwNWQ3ZmU2OTdmZDM1Y2YiLCJ0IjoiNGRiMWQ3NjEtZGQwYi00ZTk4LTkyZTktYTdiMDFlNjY4ZTc3IiwicyI6IlptWTVaR1l4TmpndE4ySmtOaTAwWkRFeExXRmxNbU10TjJaaVpHSmtOamMzTURFMiJ9
APP_URL=https://moviematcher.yourdomain.com
```

Replace:
- The token with **your actual token** from Cloudflare
- The APP_URL with **your actual domain**

#### 3. Deploy/Redeploy Stack

Click **"Pull and redeploy"** in Portainer. The `cloudflared` container will start and connect to Cloudflare.

#### 4. Verify Connection

1. **Check tunnel status** in Cloudflare Dashboard → Zero Trust → Access → Tunnels
   - Status should show "HEALTHY" with a green indicator
2. **Visit your domain**: `https://moviematcher.yourdomain.com`
   - Your app should be accessible!

### Viewing Cloudflare Tunnel Logs

In Portainer:
1. Go to **Containers**
2. Find the `cloudflared` container (e.g., `moviematcher-cloudflared-1`)
3. Click **"Logs"** to see connection status

Expected log output when working:
```
INF Connection registered connIndex=0
INF Connection registered connIndex=1
INF Connection registered connIndex=2
INF Connection registered connIndex=3
```

### Updating Tunnel Configuration

If you need to change your domain or tunnel settings:

1. **Update in Cloudflare Dashboard**:
   - Go to your tunnel settings
   - Update the public hostname or service URL

2. **Update APP_URL in Portainer**:
   - Edit your stack's environment variables
   - Update `APP_URL` to match your new domain
   - Click **"Update the stack"**

### Troubleshooting Cloudflare Tunnel

#### Tunnel shows "DOWN" in Cloudflare Dashboard
```bash
# Check cloudflared container logs
docker logs moviematcher-cloudflared-1

# Common issues:
# - Invalid token → Check CLOUDFLARE_TUNNEL_TOKEN in Portainer
# - Network issues → Check container can access internet
# - App container not running → Check app container is healthy
```

#### "502 Bad Gateway" when accessing domain
```bash
# The tunnel is working but can't reach your app
# Check the Service URL in Cloudflare Tunnel settings:
# - Should be: app:8000
# - NOT: localhost:8000 or 127.0.0.1:8000

# Verify app container is running:
docker ps | grep moviematcher-app

# Check app container logs:
docker logs moviematcher-app-1
```

#### "Unable to reach origin" error
- Make sure your app container is named `app` (or update Service URL in Cloudflare)
- Verify the `depends_on: - app` configuration in docker-compose.yml
- Check both containers are in the same Docker network

### Security Notes

- ✅ Your server's IP address is hidden behind Cloudflare
- ✅ SSL/TLS is handled automatically by Cloudflare
- ✅ No need to open port 8000 to the internet (tunnel only)
- ✅ Cloudflare's DDoS protection is enabled
- ⚠️ Keep your tunnel token secret (don't commit it to Git)

### Optional: Remove Public Port Exposure

For maximum security, you can remove the public port mapping since traffic goes through the tunnel:

**In docker-compose.yml**, remove or comment out:
```yaml
# ports:
#   - "8000:8000"
```

This way, your app is **only** accessible via the Cloudflare Tunnel, not directly on port 8000.

---

## Discord Deployment Notifications

### What are Discord Notifications?

Get real-time notifications in your Discord server whenever your app is deployed or updated. You'll receive:
- 🚀 **Deployment Starting** - When the deployment begins
- ✅ **Deployment Successful** - When deployment completes successfully
- ❌ **Deployment Failed** - If deployment encounters an error

Each notification includes:
- Environment (production/local)
- Git commit hash
- Git branch
- Application URL
- Timestamp

### Setup Discord Webhook

#### 1. Create a Discord Webhook

1. Open your Discord server
2. Go to **Server Settings** → **Integrations** → **Webhooks**
3. Click **"New Webhook"**
4. Name it (e.g., "MovieMatcher Deployments")
5. Select the channel where you want notifications
6. Click **"Copy Webhook URL"**

Your webhook URL will look like:
```
https://discord.com/api/webhooks/1234567890/AbCdEfGhIjKlMnOpQrStUvWxYz...
```

#### 2. Add to Portainer Environment Variables

In your Portainer stack's environment variables, add:
```
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/YOUR_WEBHOOK_ID/YOUR_WEBHOOK_TOKEN
```

#### 3. Deploy/Redeploy

Click **"Pull and redeploy"** and you'll immediately get a notification in Discord!

### Testing Notifications

After setting up the webhook, redeploy your stack to test:
1. In Portainer, click **"Pull and redeploy"**
2. Watch your Discord channel for notifications:
   - First: "🚀 Deployment Starting"
   - Then: "✅ Deployment Successful" (if successful)

### Disabling Notifications

Simply remove the `DISCORD_WEBHOOK_URL` environment variable from your Portainer stack and redeploy. The app will continue to work without sending notifications.

---

## Automatic Deployments from Git

### Overview

Automatically deploy your app when you push to GitHub using a polling script that checks for updates every 5 minutes.

**How it works:**
1. You push code to GitHub
2. Auto-update script checks Git every 5 minutes
3. If updates found, pulls latest code and restarts containers
4. Discord notification sent automatically (if configured)

**No public IP or webhooks required!** Works perfectly behind firewall.

### Quick Setup

**See [`deploy/README.md`](deploy/README.md) for complete setup instructions.**

**Summary:**

1. **On your Debian server:**
   ```bash
   cd /opt/moviematcher
   sudo chmod +x deploy/auto-update.sh

   # Install systemd timer
   sudo cp deploy/moviematcher-autoupdate.* /etc/systemd/system/
   sudo systemctl daemon-reload
   sudo systemctl enable moviematcher-autoupdate.timer
   sudo systemctl start moviematcher-autoupdate.timer
   ```

2. **Verify it's running:**
   ```bash
   sudo systemctl list-timers | grep moviematcher
   ```

3. **Test:**
   ```bash
   git commit --allow-empty -m "Test auto-deploy"
   git push origin main
   # Wait up to 5 minutes, check logs:
   tail -f /var/log/moviematcher-autoupdate.log
   ```

That's it! Now every push to `main` branch will automatically deploy within 5 minutes. 🎉

### Configuration

**Adjust check interval** (default: 5 minutes):
```bash
sudo nano /etc/systemd/system/moviematcher-autoupdate.timer
# Change OnUnitActiveSec=5min to your preference (1min, 15min, 1h, etc.)
sudo systemctl daemon-reload
sudo systemctl restart moviematcher-autoupdate.timer
```

**Optional: Configure Portainer API for faster deploys**

Create `/opt/moviematcher/deploy/.env`:
```bash
PORTAINER_URL=http://localhost:9000
PORTAINER_API_TOKEN=your-api-token-here
STACK_ID=your-stack-id-here
```

This uses Portainer's API instead of docker-compose for faster, cleaner deploys.

---

## Environment Variables

Key environment variables you can override in Portainer:

| Variable | Default | Production Value | Required |
|----------|---------|------------------|----------|
| `APP_ENV` | `production` | `production` | Yes |
| `APP_DEBUG` | `false` | `false` | Yes |
| `APP_URL` | `http://localhost:8000` | `https://your-domain.com` | Yes |
| `CLOUDFLARE_TUNNEL_TOKEN` | *(none)* | Your tunnel token | **Yes** (for tunnel) |
| `DISCORD_WEBHOOK_URL` | *(none)* | Your Discord webhook URL | No (optional) |
| `LOG_LEVEL` | `debug` | `error` or `warning` | No |

---

## File Permissions

The following directories need write permissions:
- `storage/` - Logs, cache, sessions
- `database/` - SQLite database file

These are handled automatically via Docker volumes defined in `docker-compose.yml`.
