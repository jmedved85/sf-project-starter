#!/bin/bash
set -e # Exit immediately if a command fails

echo
echo "===== Starting Deployment ====="
SITE_DIR=$(pwd)

echo
echo "1. Pulling latest changes..."
git pull origin main || { echo "Failed to pull from git"; exit 1; }

echo
echo "2. Installing dependencies..."
~/bin/composer install --no-dev --optimize-autoloader --prefer-dist --no-plugins || { echo "Failed to install dependencies"; exit 1; }

echo
echo "3. Clearing and warming up cache..."
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

echo
echo "4. Installing import maps and compiling assets..."
echo "   - Running importmap:install..."
php bin/console importmap:install --env=prod || echo "⚠️ Importmap warning - continuing anyway"
echo "   - Compiling assets..."
php bin/console asset-map:compile --env=prod || echo "⚠️ Asset compilation warning - continuing anyway"

echo
echo "5. Running database migrations..."

if [ -d migrations ] && [ "$(ls -A migrations)" ]; then
    # Create database backup before migrations
    # echo "   - Creating database backup..."
    # TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
    # BACKUP_FILE="db_backup_${TIMESTAMP}.sql"
    # php bin/console doctrine:database:export --env=prod > "backups/${BACKUP_FILE}" || { 
    #     echo "❌ Failed to create database backup. Aborting migrations for safety."
    #     exit 1
    # }
    
    # Run migrations
    echo "   - Running migrations..."
    php bin/console doctrine:migrations:migrate --env=prod --no-interaction || {
        echo "❌ Migration failed! Restoring from backup..."
        php bin/console doctrine:database:import --env=prod "backups/${BACKUP_FILE}"
        echo "Database restored from backup. Please check migration files."
        exit 1
    }
    
    # Verify database integrity
    echo "   - Verifying database..."
    php bin/console doctrine:schema:validate --env=prod || {
        echo "⚠️ Database schema validation failed after migration."
        echo "Please check database structure manually."
    }
fi

echo
echo "6. Setting up public_html symlinks..."
mkdir -p public_html

# Remove existing symlinks
find public_html -type l -delete

# Create symlinks for all files and directories in public
for item in public/*; do
    base_name=$(basename "$item")
    ln -sf "$SITE_DIR/$item" "$SITE_DIR/public_html/$base_name"
done

# Also link any hidden files (like .htaccess)
for item in public/.*; do
    if [ -f "$item" ]; then
        base_name=$(basename "$item")
        ln -sf "$SITE_DIR/$item" "$SITE_DIR/public_html/$base_name"
    fi
done

echo
echo "7. Setting proper permissions..."
chmod -R 755 var/
chmod -R 755 public/
chmod -R 755 public_html/

echo
echo "===== Deployment Complete ====="
echo
echo "Deployed on: $(date)"
echo "Current branch: $(git branch --show-current)"
echo "Last commit: $(git log -1 --pretty=%B)"
echo