#!/bin/bash
APP_NAME=$1
DB_HOST=$2
PORT=$3
IMAGE_TAG=$4

if [[ "$APP_NAME" == *"_fullapp" ]]; then
  API_APP_NAME=$(echo "$APP_NAME" | sed 's/_fullapp/_api/')
  API_PORT_FILE="/home/ApplicationContainer/${API_APP_NAME}_port.txt"

  echo "⏳ Waiting for API port file: $API_PORT_FILE"
  for i in {1..10}; do
    if [ -f "$API_PORT_FILE" ]; then
      echo "✅ Found API port file."
      break
    fi
    sleep 2
  done

  if [ ! -f "$API_PORT_FILE" ]; then
    echo "❌ API port file not found after waiting. Exiting env generation."
    exit 1
  fi



  API_PORT=$(cat "$API_PORT_FILE")
  REACT_APP_API_URL="http://$DB_HOST:$API_PORT/"
  ENV_PATH="/home/ApplicationContainer/${IMAGE_TAG}/env.js"
mkdir -p "/home/ApplicationContainer/${IMAGE_TAG}"
echo "window.REACT_APP_API_URL='$REACT_APP_API_URL';" > "$ENV_PATH"
echo "🔍 APP_NAME=$APP_NAME"
echo "🔍 API_APP_NAME=$API_APP_NAME"
echo "🔍 API_PORT_FILE=$API_PORT_FILE"
echo "🔍 API_PORT=$API_PORT"
echo "🔍 ENV_PATH=$ENV_PATH"
fi
