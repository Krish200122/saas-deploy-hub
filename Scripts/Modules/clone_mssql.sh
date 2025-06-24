#!/bin/bash

DB_HOST=$1
DB_USER=sa
DB_PASS=$2
SOURCE_DB=$3
TARGET_DB=$4

echo "🔄 Exporting database from [$SOURCE_DB]..."
sqlpackage /Action:Export \
  /SourceConnectionString:"Server=$DB_HOST;Database=$SOURCE_DB;User Id=$DB_USER;Password=$DB_PASS;TrustServerCertificate=true;" \
  /TargetFile:"$SOURCE_DB.bacpac"

# Check if export was successful
if [ ! -f "$SOURCE_DB.bacpac" ]; then
  echo "❌ Export failed or .bacpac file not found."
  exit 1
fi

echo "📦 Creating target DB [$TARGET_DB] if not exists..."
/opt/mssql-tools/bin/sqlcmd -S "$DB_HOST" -U $DB_USER -P "$DB_PASS" -Q "
IF DB_ID('$TARGET_DB') IS NULL
BEGIN
    CREATE DATABASE [$TARGET_DB];
END;
"

echo "⬇️ Importing into [$TARGET_DB]..."
sqlpackage /Action:Import \
  /TargetConnectionString:"Server=$DB_HOST;Database=$TARGET_DB;User Id=$DB_USER;Password=$DB_PASS;TrustServerCertificate=true;" \
  /SourceFile:"$SOURCE_DB.bacpac"

echo "✅ [$SOURCE_DB] successfully cloned to [$TARGET_DB]"
