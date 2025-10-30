#!/bin/bash

# chmod +x migrations-migrate.sh

docker compose exec php bin/console doctrine:migrations:migrate