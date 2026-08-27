#!/bin/bash

# chmod +x setup.sh

set -euo pipefail

env_file=".env.local"
database_url='mysql://root:pass1234@mariadb:3306/sf-project-starter_dev?serverVersion=10.11.2-MariaDB&charset=utf8mb4'
public_assets_dir="public/assets"

echo " "
echo "Starting application setup..."
echo " "

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker is required but was not found in PATH."
    exit 1
fi

if [[ ! -f "$env_file" ]]; then
    echo "Creating $env_file..."
    cat > "$env_file" <<EOF
APP_ENV=dev
APP_DEBUG=true
DATABASE_URL=$database_url
EOF
else
    echo "$env_file already exists, keeping current values."
fi

echo "Building and starting containers..."
docker compose up -d --build

echo "Installing Composer dependencies..."
docker compose exec -T php composer install --no-interaction

echo "Clearing cache..."
docker compose exec -T php php bin/console cache:clear

echo "Compiling asset map..."
rm -rf "$public_assets_dir"
docker compose exec -T php php bin/console asset-map:compile

echo "Creating database if needed..."
docker compose exec -T php php bin/console doctrine:database:create --if-not-exists --no-interaction

echo "Running migrations..."
docker compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures..."
docker compose exec -T php php bin/console doctrine:fixtures:load --no-interaction

echo "Installing importmap assets..."
docker compose exec -T php php bin/console importmap:install

echo "Running syntax checks..."
docker compose exec -T php php bin/console lint:yaml config
docker compose exec -T php php bin/console lint:twig templates
docker compose exec -T php php bin/console lint:container

echo "Running code style checks..."
docker compose exec -T php ./vendor/bin/php-cs-fixer fix

echo "Validating database schema..."
docker compose exec -T php php bin/console doctrine:schema:validate

echo "Running static analysis..."
docker compose exec -T php ./vendor/bin/phpstan analyze --memory-limit=1G

echo "Running security checks..."
docker compose exec -T php composer audit

echo " "
echo "Setup complete. Open http://localhost:8080 in your browser."