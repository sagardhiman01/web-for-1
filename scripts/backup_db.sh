#!/usr/bin/env bash
set -euo pipefail

# Non-destructive DB backup placeholder.
# Usage:
# DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=app DB_USER=app DB_PASSWORD=secret ./scripts/backup_db.sh

ts=$(date +"%Y%m%d_%H%M%S")
mkdir -p backups

if command -v mysqldump >/dev/null 2>&1; then
  mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "${DB_USER:-root}" "-p${DB_PASSWORD:-}" "${DB_NAME:-app}" > "backups/db_${ts}.sql"
  echo "Saved backups/db_${ts}.sql"
elif command -v pg_dump >/dev/null 2>&1; then
  PGPASSWORD="${DB_PASSWORD:-}" pg_dump -h "${DB_HOST:-127.0.0.1}" -p "${DB_PORT:-5432}" -U "${DB_USER:-postgres}" "${DB_NAME:-app}" > "backups/db_${ts}.sql"
  echo "Saved backups/db_${ts}.sql"
else
  echo "Missing mysqldump/pg_dump"
  exit 1
fi
