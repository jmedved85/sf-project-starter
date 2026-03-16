#!/bin/bash

# chmod +x qa.sh

set -e

# 1. Clearing cache and compiling assets
echo " "
echo "Clearing cache and compiling assets..."
echo " "
docker compose exec php bin/console cache:clear
docker compose exec php bin/console asset-map:compile

# 2. Fast syntax checks (fail-fast)
echo " "
echo "Running syntax checks..."
echo " "
docker compose exec php bin/console lint:yaml config
docker compose exec php bin/console lint:twig templates
docker compose exec php bin/console lint:container

# 3. Code style
echo " "
echo "Running code style checks..."
echo " "
docker compose exec php ./vendor/bin/php-cs-fixer fix

# 4. Database validation
echo " "
echo "Validating database schema..."
echo " "
docker compose exec php bin/console doctrine:schema:validate

# 5. Static analysis
echo " "
echo "Running static analysis..."
echo " "
docker compose exec php ./vendor/bin/phpstan analyze --memory-limit=1G

# 6. Security checks
echo " "
echo "Running security checks..."
echo " "
composer audit
symfony check:security

# 7. Tests
# echo " "
# echo "Running tests..."
# echo " "
# docker compose exec php ./vendor/bin/phpunit