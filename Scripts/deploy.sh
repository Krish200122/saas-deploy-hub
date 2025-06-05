#!/bin/bash
set -e

IMAGE_TAG=$1
APP_NAME=$2

cd /home/ApplicationContainer

echo "🚀 Starting deployment for: $APP_NAME (Customer: $IMAGE_TAG)"

# Function to find available port
find_available_port() {
  BASE_PORT=7000
  MAX_PORT=7300
  while [ $BASE_PORT -le $MAX_PORT ]; do
    if ! lsof -i :$BASE_PORT &>/dev/null; then
      echo $BASE_PORT
      return
    fi
    ((BASE_PORT++))
  done
  echo ""
}

# Find available port
CONTAINER_PORT=$(find_available_port)
if [ -z "$CONTAINER_PORT" ]; then
  echo "❌ No available port found"
  exit 1
fi

echo "📡 Using port: $CONTAINER_PORT"
echo "$CONTAINER_PORT" > /home/ApplicationContainer/port.txt

# Load Docker image
docker_image_tar="${APP_NAME}.tar"
if [ ! -f "$docker_image_tar" ]; then
  echo "❌ Docker image file not found: $docker_image_tar"
  exit 1
fi

echo "📦 Loading Docker image..."
docker load -i "$docker_image_tar"

# Stop and remove existing container if running
echo "🧹 Cleaning up existing containers..."
docker stop "${APP_NAME}-${IMAGE_TAG}" 2>/dev/null || true
docker rm "${APP_NAME}-${IMAGE_TAG}" 2>/dev/null || true

# Create data directory
mkdir -p /home/ApplicationContainer/${IMAGE_TAG}

echo "🚀 Deploying application..."

# Simple docker run command
docker run -d \
  --name ${APP_NAME}-${IMAGE_TAG} \
  -p $CONTAINER_PORT:80 \
  -e CUSTOMER_ID=$IMAGE_TAG \
  -v /home/ApplicationContainer/${IMAGE_TAG}:/app/data \
  --restart unless-stopped \
  ${APP_NAME}:${IMAGE_TAG}

# Verify deployment
echo "⏳ Verifying deployment..."
sleep 5

if docker ps | grep -q "${APP_NAME}-${IMAGE_TAG}"; then
  echo "✅ Container ${APP_NAME}-${IMAGE_TAG} is running on port $CONTAINER_PORT"
  echo "🌐 Access your app at: http://$(curl -s ifconfig.me):$CONTAINER_PORT"
  
  echo "📊 Deployment Summary:"
  echo "   Application: $APP_NAME"
  echo "   Customer: $IMAGE_TAG"
  echo "   Port: $CONTAINER_PORT"
  echo "   Status: Running ✅"
else
  echo "❌ Container failed to start"
  echo "📋 Container logs:"
  docker logs "${APP_NAME}-${IMAGE_TAG}" 2>/dev/null || true
  exit 1
fi
