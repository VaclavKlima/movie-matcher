#!/bin/bash
set -e

# MovieMatcher Auto-Deploy Script
# This script pulls the latest code and restarts the stack via Portainer API

echo "========================================="
echo "MovieMatcher Auto-Deploy"
echo "Started at: $(date)"
echo "========================================="

# Configuration - Set these via environment variables or edit here
PORTAINER_URL="${PORTAINER_URL:-http://localhost:9000}"
PORTAINER_API_TOKEN="${PORTAINER_API_TOKEN:-}"
STACK_ID="${STACK_ID:-}"
PROJECT_DIR="${PROJECT_DIR:-/opt/moviematcher}"

# Method 1: Deploy via Portainer API (requires API token and stack ID)
deploy_via_portainer() {
  echo "→ Deploying via Portainer API..."

  if [ -z "$PORTAINER_API_TOKEN" ] || [ -z "$STACK_ID" ]; then
    echo "✗ PORTAINER_API_TOKEN or STACK_ID not set"
    echo "  Falling back to direct docker-compose method"
    return 1
  fi

  # Trigger stack webhook or API update
  RESPONSE=$(curl -s -w "\n%{http_code}" \
    -X PUT \
    -H "X-API-Key: $PORTAINER_API_TOKEN" \
    -H "Content-Type: application/json" \
    "$PORTAINER_URL/api/stacks/$STACK_ID/git/redeploy" \
    -d '{"RepositoryAuthentication": false, "Prune": true}')

  HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

  if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "204" ]; then
    echo "✓ Stack redeployment triggered via Portainer API"
    return 0
  else
    echo "✗ Portainer API request failed (HTTP $HTTP_CODE)"
    echo "  Response: $(echo "$RESPONSE" | head -n-1)"
    return 1
  fi
}

# Method 2: Direct docker-compose deployment
deploy_via_docker_compose() {
  echo "→ Deploying via docker-compose..."

  cd "$PROJECT_DIR"

  # Pull latest code
  echo "→ Pulling latest code from Git..."
  git fetch origin main
  git reset --hard origin/main

  # Pull/rebuild images
  echo "→ Building and restarting containers..."
  docker-compose down
  docker-compose build --no-cache
  docker-compose up -d

  echo "✓ Deployment completed via docker-compose"
}

# Try Portainer API first, fall back to docker-compose
if ! deploy_via_portainer; then
  deploy_via_docker_compose
fi

echo "========================================="
echo "Deployment completed at: $(date)"
echo "========================================="
