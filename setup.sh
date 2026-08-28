#!/bin/bash

# chmod +x setup.sh
# SF Project Starter - Complete installation and setup script
# Used for Docker development setup and VPS deployment preparation

set -e  # Stop script if any command fails

echo "================================================"
echo "  SF Project Starter - Complete Installation"
echo "================================================"
echo ""

# Function to generate APP_SECRET
generate_secret() {
    openssl rand -hex 16 2>/dev/null || head -c 16 /dev/urandom | xxd -p
}

# 0. Check prerequisites
echo "Step 0: Checking prerequisites..."
if ! command -v docker > /dev/null 2>&1; then
    echo "❌ Docker is not installed! Please install Docker."
    exit 1
fi

if ! docker compose version > /dev/null 2>&1; then
    echo "❌ Docker Compose is not available! Please install Docker Compose."
    exit 1
fi

echo "✓ Docker and Docker Compose are installed"
echo ""

# 1. Create/check .env.local file
echo "Step 1: Checking .env.local file..."
if [ ! -f .env.local ]; then
    echo "⚠️  .env.local does not exist! Creating new file..."

    # Generate APP_SECRET
    APP_SECRET=$(generate_secret)

    cat > .env.local << EOF
###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=${APP_SECRET}
APP_DEBUG=true
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# MariaDB connection for Docker development
DATABASE_URL=mysql://root:pass1234@mariadb-sf_project_starter:3306/sf-project-starter_dev
APP_ENV=dev
APP_DEBUG=true
###< doctrine/doctrine-bundle ###

###> symfony/mailer ###
# Mailpit for local email testing
MAILER_DSN=smtp://mailpit-sf_project_starter:1025
###< symfony/mailer ###
EOF
    echo "✓ Created .env.local file with basic configuration"
    echo "  ⚠️  IMPORTANT: Set RECAPTCHA3_KEY and RECAPTCHA3_SECRET if using contact forms!"
    echo "  ⚠️  IMPORTANT: Add OPENAI_API_KEY if using AI features!"
    echo ""
else
    echo "✓ .env.local already exists"

    # Check if APP_SECRET exists
    if ! grep -q "APP_SECRET=" .env.local || grep -q "APP_SECRET=$" .env.local || grep -q "APP_SECRET=\"\"" .env.local; then
        echo "⚠️  APP_SECRET is not set. Generating new one..."
        APP_SECRET=$(generate_secret)
        
        if grep -q "APP_SECRET=" .env.local; then
            # Replace existing empty APP_SECRET
            sed -i "s/^APP_SECRET=.*/APP_SECRET=${APP_SECRET}/" .env.local
        else
            # Add APP_SECRET if it doesn't exist
            echo "APP_SECRET=${APP_SECRET}" >> .env.local
        fi
        echo "✓ APP_SECRET has been generated and added to .env.local"
    fi
    echo ""
fi

# 2. Start Docker containers
echo "Step 2: Starting Docker containers..."
echo "  (This may take a few minutes on first run)"
docker compose up --build -d
echo "✓ Docker containers started"
echo ""

# 3. Wait for MariaDB to be ready
echo "Step 3: Waiting for MariaDB to be ready..."
echo "  Checking MariaDB connection..."
MAX_TRIES=30
COUNTER=0
until docker compose exec -T mariadb mariadb -uroot -ppass1234 -e "SELECT 1" >/dev/null 2>&1; do
    COUNTER=$((COUNTER + 1))
    if [ $COUNTER -gt $MAX_TRIES ]; then
        echo "❌ MariaDB did not become available after ${MAX_TRIES} attempts"
        echo "   Check container status: docker compose ps"
        echo "   Check logs: docker compose logs mariadb"
        exit 1
    fi
    echo "  Waiting for MariaDB... (attempt ${COUNTER}/${MAX_TRIES})"
    sleep 2
done
echo "✓ MariaDB is ready and accepting connections"
echo ""

# 4. Install Composer dependencies
echo "Step 4: Installing Composer dependencies..."
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "  Installing Composer packages..."
    docker compose exec -T php composer install --no-interaction --optimize-autoloader
    echo "✓ Composer dependencies installed"
else
    echo "  Vendor directory exists, updating dependencies..."
    docker compose exec -T php composer install --no-interaction --optimize-autoloader
    echo "✓ Composer dependencies updated"
fi
echo ""

# 5. Create development database
echo "Step 5: Creating development database..."
docker compose exec -T php bin/console doctrine:database:create --if-not-exists
echo "✓ Database 'sf-project-starter_dev' created (or already exists)"
echo ""

# 6. Run migrations on development database
echo "Step 6: Running migrations on development database..."
docker compose exec -T php bin/console doctrine:migrations:migrate --no-interaction
echo "✓ Migrations executed on development database"
echo ""

# 7. Create test database
echo "Step 7: Creating test database..."
docker compose exec -T mariadb mariadb -uroot -ppass1234 -e "CREATE DATABASE IF NOT EXISTS \`sf-project-starter_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo "✓ Test database 'sf-project-starter_test' created (or already exists)"
echo ""

# 8. Synchronize test database with development schema
echo "Step 8: Synchronizing test database with development schema..."
docker compose exec -T mariadb sh -c "mariadb-dump -uroot -ppass1234 --no-data sf-project-starter_dev | mariadb -uroot -ppass1234 sf-project-starter_test"
echo "✓ Test database synchronized with development schema"
echo ""

# 9. Load fixtures (initial data)
echo "Step 9: Loading fixtures (initial data)..."
if docker compose exec -T php bin/console list | grep -q "doctrine:fixtures:load"; then
    echo "  Loading fixtures into development database..."
    docker compose exec -T php bin/console doctrine:fixtures:load --no-interaction 2>/dev/null && \
        echo "✓ Fixtures loaded into development database" || \
        echo "⚠️  Fixtures command available but no fixtures found or failed to load"
    
    echo "  Loading fixtures into test database..."
    docker compose exec -T -e APP_ENV=test php bin/console doctrine:fixtures:load --no-interaction 2>/dev/null && \
        echo "✓ Fixtures loaded into test database" || \
        echo "⚠️  Fixtures command available but no fixtures found or failed to load"
else
    echo "⚠️  Doctrine fixtures bundle not installed (install with: composer require --dev doctrine/doctrine-fixtures-bundle)"
fi
echo ""

# 10. Install assets dependencies
echo "Step 10: Installing assets dependencies..."
docker compose exec -T php bin/console importmap:install
echo "✓ Assets dependencies installed"
echo ""

# 11. Compile frontend assets
echo "Step 11: Compiling frontend assets..."
docker compose exec -T php bin/console asset-map:compile
echo "✓ Assets compiled"
echo ""

# 12. Clear cache
echo "Step 12: Clearing cache..."
docker compose exec -T php bin/console cache:clear
docker compose exec -T php bin/console cache:warmup
echo "✓ Cache cleared and warmed up"
echo ""

# 13. Set permissions (for Linux/Mac)
echo "Step 13: Setting permissions..."
if [[ "$OSTYPE" != "msys" && "$OSTYPE" != "win32" ]]; then
    chmod -R 777 var/ 2>/dev/null || true
    echo "✓ Permissions set"
else
    echo "✓ Windows detected, skipping chmod"
fi
echo ""

echo "================================================"
echo "  ✅ Installation completed successfully!"
echo "================================================"
echo ""
echo "🎉 Application is ready to use!"
echo ""
echo "Access points:"
echo "  🌐 Web application:   http://localhost:8080"
echo "  📊 Admin Dashboard:   http://localhost:8080/admin"
echo "  🗄️ phpMyAdmin:        http://localhost:8090"
echo "  📧 Mailpit (email):   http://localhost:8025"
echo ""
echo "Database credentials:"
echo "  MariaDB Port:         3308 (localhost:3308)"
echo "  MariaDB User:         root"
echo "  MariaDB Password:     pass1234"
echo "  Dev Database:         sf-project-starter_dev"
echo "  Test Database:        sf-project-starter_test"
echo ""
echo "Useful scripts:"
echo "  ./up.sh                    - Start containers"
echo "  ./down.sh                  - Stop containers"
echo "  ./migrations-migrate.sh    - Run migrations"
echo "  ./asset-map-compile.sh     - Compile assets"
echo "  ./test.sh                  - Run tests"
echo "  ./clear-cache.sh           - Clear cache"
echo "  ./qa.sh                    - Code quality checks (PHPStan, CS-Fixer)"
echo ""