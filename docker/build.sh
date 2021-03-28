#!/usr/bin/env bash

docker_dir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
project_dir=$( cd "$( dirname "$docker_dir" )" && pwd )

cd $project_dir

set -e # exit on error
set -x # show commands

docker-compose down --remove-orphans
docker-compose up -d nginx postgres_event_store postgres_registered_users_projections mysql_lists_projections redis adminer
docker-compose run --rm php composer install -o -a --ansi
docker-compose run --rm php php bin/console cache:warmup
docker-compose run --rm php php bin/console streak:snapshots:storage:reset
docker-compose run --rm php php bin/console streak:event-store:schema:drop
docker-compose run --rm php php bin/console streak:event-store:schema:create
docker-compose run --rm php php bin/console todocler:users:register-user 8e5ebf2b-f78c-430d-b15f-0f3e710b284b me@example.com password
docker-compose run --rm php bin/console streak:subscriptions:run -v
docker-compose run --rm php bin/console streak:subscriptions:run -v
docker-compose ps
