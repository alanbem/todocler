#!/usr/bin/env bash

docker_dir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
project_dir=$( cd "$( dirname "$docker_dir" )" && pwd )

cd $project_dir

set -e # exit on error
set -x # show commands

docker-compose down --remove-orphans
docker-compose up -d nginx postgres_event_store postgres_projections redis
docker-compose run --rm php composer install -o -a --ansi
docker-compose run --rm php php bin/console cache:warmup
docker-compose run --rm php php bin/console streak:snapshots:storage:reset
docker-compose run --rm php php bin/console streak:event-store:schema:drop
docker-compose run --rm php php bin/console streak:event-store:schema:create
docker-compose ps

curl http://127.0.0.1:8080
