#!/bin/bash

# chmod +x diff-migrations.sh

docker compose exec php bin/console doctrine:migrations:diff