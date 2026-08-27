#!/bin/bash
set -euo pipefail

APP_DIR="/var/www/html"
VAR_DIR="$APP_DIR/var"
UPLOADS_DIR="$VAR_DIR/uploads"
AVATARS_DIR="$UPLOADS_DIR/avatars"
DOCUMENTS_DIR="$UPLOADS_DIR/documents"
CACHE_DIR="$VAR_DIR/cache"

# Create necessary directories if they don't exist
mkdir -p "$UPLOADS_DIR" "$AVATARS_DIR" "$DOCUMENTS_DIR" "$CACHE_DIR"

# Fix ownership for var directory and its contents
chown -R www-data:www-data "$VAR_DIR" || true

# Directories: rwx for owner+group (recursively for var/)
chmod -R 770 "$VAR_DIR" || true

exec "$@"