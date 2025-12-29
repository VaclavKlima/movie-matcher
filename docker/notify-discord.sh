#!/usr/bin/env bash
# Discord notification script for deployment updates

DISCORD_WEBHOOK_URL="${DISCORD_WEBHOOK_URL:-}"
MESSAGE_TYPE="${1:-starting}"
APP_URL="${APP_URL:-http://localhost:8000}"

# Exit silently if no webhook URL is configured
if [ -z "$DISCORD_WEBHOOK_URL" ]; then
  exit 0
fi

# Get current timestamp
TIMESTAMP=$(date -u +"%Y-%m-%dT%H:%M:%SZ")

# Get git information from environment variables (set at build time)
# Fallback to trying git commands if env vars not set
if [ -n "$GIT_COMMIT" ] && [ "$GIT_COMMIT" != "unknown" ]; then
  GIT_COMMIT_SHORT="${GIT_COMMIT:0:7}"
else
  GIT_COMMIT_SHORT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
fi

if [ -z "$GIT_BRANCH" ] || [ "$GIT_BRANCH" = "main" ]; then
  GIT_BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
fi

case "$MESSAGE_TYPE" in
  starting)
    TITLE="🚀 Deployment Starting"
    DESCRIPTION="MovieMatcher is being deployed..."
    COLOR=3447003  # Blue
    ;;
  success)
    TITLE="✅ Deployment Successful"
    DESCRIPTION="MovieMatcher has been successfully deployed and is now live!"
    COLOR=3066993  # Green
    ;;
  error)
    TITLE="❌ Deployment Failed"
    DESCRIPTION="MovieMatcher deployment encountered an error."
    COLOR=15158332  # Red
    ;;
  *)
    TITLE="📦 Deployment Update"
    DESCRIPTION="MovieMatcher deployment status update"
    COLOR=10181046  # Purple
    ;;
esac

# Create JSON payload
JSON_PAYLOAD=$(cat <<EOF
{
  "embeds": [{
    "title": "$TITLE",
    "description": "$DESCRIPTION",
    "color": $COLOR,
    "fields": [
      {
        "name": "Environment",
        "value": "${APP_ENV:-production}",
        "inline": true
      },
      {
        "name": "Git Commit",
        "value": "\`$GIT_COMMIT_SHORT\`",
        "inline": true
      },
      {
        "name": "Branch",
        "value": "\`$GIT_BRANCH\`",
        "inline": true
      },
      {
        "name": "URL",
        "value": "$APP_URL",
        "inline": false
      }
    ],
    "timestamp": "$TIMESTAMP",
    "footer": {
      "text": "MovieMatcher Deployment"
    }
  }]
}
EOF
)

# Send to Discord webhook
curl -H "Content-Type: application/json" \
     -d "$JSON_PAYLOAD" \
     "$DISCORD_WEBHOOK_URL" \
     --silent --output /dev/null

exit 0
