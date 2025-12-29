#!/bin/bash

# MovieMatcher Auto-Update Script
# Checks Git for updates and deploys automatically if changes are detected

set -e

# Configuration
PROJECT_DIR="${PROJECT_DIR:-/opt/moviematcher}"
LOG_FILE="${LOG_FILE:-/var/log/moviematcher-autoupdate.log}"
PORTAINER_URL="${PORTAINER_URL:-http://localhost:9000}"
PORTAINER_API_TOKEN="${PORTAINER_API_TOKEN:-}"
STACK_ID="${STACK_ID:-}"

# Logging function
log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Change to project directory
cd "$PROJECT_DIR" || {
  log "ERROR: Project directory not found: $PROJECT_DIR"
  exit 1
}

log "Checking for updates..."

# Fetch latest from remote
git fetch origin main 2>&1 | tee -a "$LOG_FILE"

# Get current and remote commit hashes
LOCAL_COMMIT=$(git rev-parse HEAD)
REMOTE_COMMIT=$(git rev-parse origin/main)

# Check if update is needed
if [ "$LOCAL_COMMIT" = "$REMOTE_COMMIT" ]; then
  log "Already up to date (commit: ${LOCAL_COMMIT:0:7})"
  exit 0
fi

log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
log "UPDATE DETECTED!"
log "Current:  ${LOCAL_COMMIT:0:7}"
log "New:      ${REMOTE_COMMIT:0:7}"
log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Get commit message
COMMIT_MSG=$(git log --format=%B -n 1 origin/main | head -n 1)
log "Latest commit: $COMMIT_MSG"

# Deploy via Portainer API if configured
deploy_via_portainer() {
  if [ -z "$PORTAINER_API_TOKEN" ] || [ -z "$STACK_ID" ]; then
    return 1
  fi

  log "Deploying via Portainer API..."

  RESPONSE=$(curl -s -w "\n%{http_code}" \
    -X PUT \
    -H "X-API-Key: $PORTAINER_API_TOKEN" \
    -H "Content-Type: application/json" \
    "$PORTAINER_URL/api/stacks/$STACK_ID/git/redeploy" \
    -d '{"RepositoryAuthentication": false, "Prune": true}' 2>&1)

  HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

  if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "204" ]; then
    log "✓ Stack redeployment triggered via Portainer API"
    log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    return 0
  else
    log "✗ Portainer API request failed (HTTP $HTTP_CODE)"
    return 1
  fi
}

# Deploy via docker-compose
deploy_via_docker_compose() {
  log "Deploying via docker-compose..."

  # Pull latest code
  log "→ Pulling latest code..."
  git reset --hard origin/main 2>&1 | tee -a "$LOG_FILE"

  # Rebuild and restart
  log "→ Rebuilding containers..."
  docker-compose down 2>&1 | tee -a "$LOG_FILE"
  docker-compose build --no-cache 2>&1 | tee -a "$LOG_FILE"

  log "→ Starting containers..."
  docker-compose up -d 2>&1 | tee -a "$LOG_FILE"

  log "✓ Deployment completed via docker-compose"
  log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

# Try Portainer API first, fall back to docker-compose
if ! deploy_via_portainer; then
  deploy_via_docker_compose
fi

log "Deployment successful! Current commit: ${REMOTE_COMMIT:0:7}"
