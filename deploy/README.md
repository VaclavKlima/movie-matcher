# Auto-Deployment Setup

This directory contains scripts for automatic deployment when you push to GitHub.

## How It Works

1. You push code to GitHub
2. Auto-update script checks Git every 5 minutes for new commits
3. If updates found, script pulls latest code and restarts containers
4. Discord notification sent automatically (if configured)

**No public IP or webhooks required!** The script polls GitHub from your server.

---

## Setup Instructions

### 1. Install on Your Debian Server

```bash
# Clone/pull your repository
cd /opt
sudo git clone https://github.com/VaclavKlima/movie-matcher.git moviematcher
cd moviematcher

# Make script executable
sudo chmod +x deploy/auto-update.sh
```

### 2. Configure Environment Variables (Optional)

Create `/opt/moviematcher/deploy/.env` for optional configuration:

```bash
# Project configuration
PROJECT_DIR=/opt/moviematcher
LOG_FILE=/var/log/moviematcher-autoupdate.log

# Portainer API (optional - for API-based deployment)
PORTAINER_URL=http://localhost:9000
PORTAINER_API_TOKEN=your-portainer-api-token
STACK_ID=your-stack-id
```

**Get your Portainer API token (optional but recommended):**
1. Portainer → User menu → My account → Access tokens → Add access token
2. Copy the token

**Get your Stack ID:**
```bash
# List all stacks
curl -H "X-API-Key: YOUR_TOKEN" http://localhost:9000/api/stacks

# Find your stack's ID in the response (look for "Id" field)
```

### 3. Install Systemd Timer

Copy the service files to systemd:

```bash
# Copy service and timer files
sudo cp /opt/moviematcher/deploy/moviematcher-autoupdate.service /etc/systemd/system/
sudo cp /opt/moviematcher/deploy/moviematcher-autoupdate.timer /etc/systemd/system/

# Reload systemd
sudo systemctl daemon-reload

# Enable and start the timer
sudo systemctl enable moviematcher-autoupdate.timer
sudo systemctl start moviematcher-autoupdate.timer

# Check timer status
sudo systemctl status moviematcher-autoupdate.timer
```

### 4. Verify Setup

**Check timer is active:**
```bash
sudo systemctl list-timers --all | grep moviematcher
```

You should see something like:
```
Wed 2025-01-29 14:35:00 UTC  4min left    n/a                          n/a          moviematcher-autoupdate.timer
```

**Manually trigger an update check:**
```bash
sudo systemctl start moviematcher-autoupdate.service
```

**View logs:**
```bash
sudo journalctl -u moviematcher-autoupdate.service -f
# Or
tail -f /var/log/moviematcher-autoupdate.log
```

### 5. Test the Setup

**Push a test commit:**
```bash
git commit --allow-empty -m "Test auto-deploy"
git push origin main
```

**Wait up to 5 minutes**, then check the logs:
```bash
tail -f /var/log/moviematcher-autoupdate.log
```

You should see:
```
[2025-01-29 14:35:00] Checking for updates...
[2025-01-29 14:35:01] UPDATE DETECTED!
[2025-01-29 14:35:01] Current:  abc1234
[2025-01-29 14:35:01] New:      def5678
[2025-01-29 14:35:02] Latest commit: Test auto-deploy
[2025-01-29 14:35:03] Deploying via Portainer API...
[2025-01-29 14:35:05] ✓ Stack redeployment triggered via Portainer API
[2025-01-29 14:35:06] Deployment successful!
```

---

## Deployment Methods

The auto-update script supports two methods:

### Method 1: Via Portainer API (Recommended)

- **Advantages**: Clean, uses Portainer's native Git integration, faster
- **Requirements**: Portainer API token and Stack ID
- **Configuration**: Set `PORTAINER_API_TOKEN` and `STACK_ID` in `.env`

### Method 2: Direct docker-compose (Automatic Fallback)

- **Advantages**: Works without Portainer API access
- **How**: Runs `git pull` + `docker-compose down` + `docker-compose up -d --build`
- **When**: Used automatically if Portainer API method fails

---

## Configuration

### Adjust Check Interval

To change how often updates are checked, edit the timer file:

```bash
sudo nano /etc/systemd/system/moviematcher-autoupdate.timer
```

Change `OnUnitActiveSec=5min` to your preferred interval:
- `1min` - Check every minute (aggressive)
- `5min` - Check every 5 minutes (default, recommended)
- `15min` - Check every 15 minutes
- `1h` - Check every hour

After editing:
```bash
sudo systemctl daemon-reload
sudo systemctl restart moviematcher-autoupdate.timer
```

### Change Log Location

Edit the service file:
```bash
sudo nano /etc/systemd/system/moviematcher-autoupdate.service
```

Change the `StandardOutput` and `StandardError` paths.

---

## Troubleshooting

### Timer not running

```bash
# Check timer status
sudo systemctl status moviematcher-autoupdate.timer

# Check if timer is enabled
sudo systemctl is-enabled moviematcher-autoupdate.timer

# Enable if not enabled
sudo systemctl enable moviematcher-autoupdate.timer
sudo systemctl start moviematcher-autoupdate.timer
```

### Updates not being detected

```bash
# Manually run the update check
sudo systemctl start moviematcher-autoupdate.service

# Check logs for errors
sudo journalctl -u moviematcher-autoupdate.service -n 50

# Verify Git can fetch from GitHub
cd /opt/moviematcher
sudo git fetch origin main
```

### Permission errors

```bash
# Ensure script is executable
sudo chmod +x /opt/moviematcher/deploy/auto-update.sh

# Check project directory permissions
ls -la /opt/moviematcher

# Add user to docker group if needed
sudo usermod -aG docker root
```

### Deployment fails

```bash
# Check if Portainer is running
sudo systemctl status portainer

# Test Portainer API manually
curl -H "X-API-Key: YOUR_TOKEN" http://localhost:9000/api/stacks

# Check docker-compose works
cd /opt/moviematcher
sudo docker-compose ps
```

---

## Security Notes

- ✅ No public ports required (polling from server)
- ✅ Only `main` branch changes trigger deployment
- ✅ Automatic Discord notifications on deployment
- ⚠️ Ensure `/opt/moviematcher` has proper permissions
- ⚠️ Keep Portainer API token secure (don't commit to Git)

---

## Logs

**View real-time auto-update logs:**
```bash
tail -f /var/log/moviematcher-autoupdate.log
```

**View systemd logs:**
```bash
sudo journalctl -u moviematcher-autoupdate.service -f
```

**View container logs:**
```bash
cd /opt/moviematcher
docker-compose logs -f
```

**View recent timer activations:**
```bash
sudo systemctl list-timers moviematcher-autoupdate.timer
```

---

## Uninstall

To remove auto-updates:

```bash
# Stop and disable timer
sudo systemctl stop moviematcher-autoupdate.timer
sudo systemctl disable moviematcher-autoupdate.timer

# Remove service files
sudo rm /etc/systemd/system/moviematcher-autoupdate.service
sudo rm /etc/systemd/system/moviematcher-autoupdate.timer

# Reload systemd
sudo systemctl daemon-reload
```

The containers will continue running normally.
