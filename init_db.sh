#!/bin/bash

# Load environment variables from .env if it exists
if [ -f .env ]; then
    set -a
    . .env
    set +a
fi

# Use environment variables or fallback to defaults/arguments
DB_NAME=${DB_NAME:-${1:-FTdb}}
DB_USER=${DB_USER:-${2:-root}}
DB_PASSWORD=${DB_PASSWORD:-${3:-secret}}
DB_HOST=${DB_HOST:-${4:-127.0.0.1}}
DB_PORT=${DB_PORT:-${5:-5432}}

export PGPASSWORD="$DB_PASSWORD"

psql -h "$DB_HOST" -U "$DB_USER" -p "$DB_PORT" -d "$DB_NAME" -f database/init/01_create_schema.sql

unset PGPASSWORD
