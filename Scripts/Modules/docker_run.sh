#!/bin/bash
APP_NAME=$1
IMAGE_TAG=$2
DB_HOST=$3
DB_USER=$4
DB_PASS=$5
DB_NAME=$6
CONTAINER_PORT=$7

# Set default internal port
INTERNAL_PORT=80
if [[ "$APP_NAME" == *"_api" ]] && [[ "$APP_NAME" == "erp_api" ]]; then
  INTERNAL_PORT=8080
fi

# Stop and remove old container if exists
docker stop "${APP_NAME}-${IMAGE_TAG}" 2>/dev/null || true
docker rm "${APP_NAME}-${IMAGE_TAG}" 2>/dev/null || true

# Load Docker image
docker_image_tar="${APP_NAME}.tar"
docker load -i "$docker_image_tar"

# For frontend apps, prepare environment file
if [[ "$APP_NAME" == *"_fullapp" ]]; then
  ENV_DIR="/home/ApplicationContainer/${IMAGE_TAG}"
  mkdir -p "$ENV_DIR"
  
  API_APP_NAME=$(echo "$APP_NAME" | sed 's/_fullapp/_api/')
  API_PORT_FILE="/home/ApplicationContainer/${API_APP_NAME}_port.txt"
  
  # Wait for API port file
  echo "⏳ Waiting for API port file (max 20s)..."
  for i in {1..10}; do
    if [ -f "$API_PORT_FILE" ]; then
      API_PORT=$(cat "$API_PORT_FILE")
      echo "✅ Found API port: $API_PORT"
      
      # Generate env.js with proper URL
      REACT_APP_API_URL="http://${DB_HOST}:${API_PORT}"
      ENV_CONTENT="window.REACT_APP_API_URL='${REACT_APP_API_URL}';"
      echo "$ENV_CONTENT" > "${ENV_DIR}/env.js"
      chmod 644 "${ENV_DIR}/env.js"
      
      echo "🔧 Generated env.js:"
      echo "-------------------"
      cat "${ENV_DIR}/env.js"
      echo "-------------------"
      break
    fi
    sleep 2
  done

  if [ ! -f "$API_PORT_FILE" ]; then
    echo "❌ ERROR: API port file not found after waiting"
    exit 1
  fi

  # Set volume mount for env.js
  VOLUME_MOUNTS="-v ${ENV_DIR}/env.js:/usr/share/nginx/html/env.js"
else
  VOLUME_MOUNTS=""
fi

# Run Docker container with debug info
echo "🚀 Starting container with mounts: ${VOLUME_MOUNTS}"
docker run -d \
  --name "${APP_NAME}-${IMAGE_TAG}" \
  -p "$CONTAINER_PORT:$INTERNAL_PORT" \
  -e DB_HOST="$DB_HOST" \
  -e DB_USER="$DB_USER" \
  -e DB_PASS="$DB_PASS" \
  -e DB_NAME="$DB_NAME" \
  $VOLUME_MOUNTS \
  --restart unless-stopped \
  "${APP_NAME}:${IMAGE_TAG}"

# Verify mount worked
if [[ "$APP_NAME" == *"_fullapp" ]]; then
  echo "🔍 Verifying env.js in container..."
  docker exec "${APP_NAME}-${IMAGE_TAG}" ls -la /usr/share/nginx/html/env.js
  docker exec "${APP_NAME}-${IMAGE_TAG}" cat /usr/share/nginx/html/env.js
fi

# Save container port
echo "$CONTAINER_PORT" > "/home/ApplicationContainer/${APP_NAME}_port.txt"
