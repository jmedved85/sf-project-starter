#!/bin/bash

# chmod +x fix.sh

# Start Docker containers in the background
docker compose exec php ./vendor/bin/php-cs-fixer fix