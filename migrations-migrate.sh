#!/bin/bash

# chmod +x migrate-migrations.sh

docker compose exec php bin/console doctrine:migrations:migrate