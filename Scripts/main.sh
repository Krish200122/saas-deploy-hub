#!/bin/bash
set -e

# ========== 🎯 Input Arguments ==========
IMAGE_TAG=$1       # Example: prod, dev, v2
APP_NAME=$2        # Example: krish_leathers_erp or arun_fabrics_portal
DB_HOST=$3
DB_USER=$4
DB_PASS=$5
SKIP_DB_CLONE=$6   # Optional: "true" to skip cloning

if [[ -z "$IMAGE_TAG" || -z "$APP_NAME" || -z "$DB_HOST" || -z "$DB_USER" || -z "$DB_PASS" ]]; then
  echo "❌ Usage: $0 <IMAGE_TAG> <APP_NAME> <DB_HOST> <DB_USER> <DB_PASS> [SKIP_DB_CLONE]"
  exit 1
fi

# ========== 📁 Paths ==========
BASE_DIR="/home/ApplicationContainer"
MODULE_DIR="$BASE_DIR/Modules"

# ========== 🧠 Source Utilities ==========
if [ ! -f "$MODULE_DIR/utils.sh" ]; then
  echo "❌ Missing: $MODULE_DIR/utils.sh"
  exit 1
fi
source "$MODULE_DIR/utils.sh"

# ========== 🔠 Parse Customer & Project ==========
CUSTOMER=$(echo "$APP_NAME" | cut -d'_' -f1)
PROJECT=$(echo "$APP_NAME" | cut -d'_' -f2)

# ========== ⚙️ Detect DB Engine ==========
if [[ "$APP_NAME" == *"erp"* ]]; then
  DB_ENGINE="mssql"
else
  DB_ENGINE="mysql"
fi
echo "🧠 Detected DB Engine: $DB_ENGINE"

# ========== 🔁 Find Available DB Version ==========
VERSION=1
while true; do
  DB_NAME="${IMAGE_TAG}_${PROJECT}_${CUSTOMER}_v${VERSION}_db"

  if [[ "$DB_ENGINE" == "mssql" ]]; then
    EXISTS=$(/opt/mssql-tools/bin/sqlcmd -S "$DB_HOST" -U sa -P "$DB_PASS" \
      -Q "SELECT name FROM sys.databases WHERE name = '$DB_NAME';" -h -1 -W | grep -w "$DB_NAME" || true)
  else
    EXISTS=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" \
      -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -w "$DB_NAME" || true)
  fi

  if [ -z "$EXISTS" ]; then
    break
  fi

  VERSION=$((VERSION + 1))
done

# ========== 🧩 Source DB Mapping ==========
BASE_NAME=$(extract_base_name "$APP_NAME")
SOURCE_DB=$(map_source_db "$BASE_NAME")

if [ -z "$SOURCE_DB" ]; then
  echo "❌ Unable to map base name '$BASE_NAME' to a source database."
  exit 1
fi

echo "🚀 Starting deployment for: $APP_NAME"
echo "📁 Source DB: $SOURCE_DB → Target DB: $DB_NAME"

# ========== 🗄️ Clone Database ==========
if [[ "$SKIP_DB_CLONE" != "true" ]]; then
  echo "🗄️ Cloning database..."
  if [[ "$DB_ENGINE" == "mssql" ]]; then
    bash "$MODULE_DIR/clone_mssql.sh" "$DB_HOST" "$DB_PASS" "$SOURCE_DB" "$DB_NAME"
  else
    bash "$MODULE_DIR/clone_mysql.sh" "$DB_HOST" "$DB_USER" "$DB_PASS" "$SOURCE_DB" "$DB_NAME"
  fi
else
  echo "⏭️ Skipping DB clone for $APP_NAME"
fi

# ========== 🌐 Assign Available Port ==========
PORT=$(find_available_port)
if [ -z "$PORT" ]; then
  echo "❌ No available port found."
  exit 1
fi
echo "📡 Using port: $PORT"
echo "$PORT" > "/home/ApplicationContainer/${APP_NAME}_port.txt"

# ========== 🧾 Generate env.js ==========
bash "$MODULE_DIR/generate_env_file.sh" "$APP_NAME" "$DB_HOST" "$PORT" "$IMAGE_TAG"

# ========== 🐳 Run Docker Container ==========
bash "$MODULE_DIR/docker_run.sh" "$APP_NAME" "$IMAGE_TAG" "$DB_HOST" "$DB_USER" "$DB_PASS" "$DB_NAME" "$PORT"

# ========== ✅ Done ==========
echo "✅ Deployment completed for $APP_NAME on port $PORT"
echo "🌐 Access at: http://$(curl -s ifconfig.me):$PORT"
