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
docker-compose run --rm php php bin/console app:users:register-user 8e5ebf2b-f78c-430d-b15f-0f3e710b284b adam@example.com password
docker-compose run --rm php bin/console streak:subscriptions:run -v
docker-compose run --rm php php bin/console app:productivity:create-list 09adf1dc-ed73-46ab-95b8-1f475fb19c13 "My List #2" adam@example.com
docker-compose run --rm php php bin/console app:productivity:create-task 09adf1dc-ed73-46ab-95b8-1f475fb19c13 336913b4-0149-4a44-b5c5-2ed42d378d8b "My List #2 - My Task #1" adam@example.com
docker-compose run --rm php php bin/console app:productivity:create-task 09adf1dc-ed73-46ab-95b8-1f475fb19c13 8af624c4-58cd-4c3f-b44a-a904db59d01a "My List #2 - My Task #2" adam@example.com
docker-compose run --rm php bin/console streak:subscriptions:run -v
docker-compose run --rm php php bin/console app:productivity:create-list fb72c276-0902-4494-9d34-4d192864cfbf "My List #3" adam@example.com
docker-compose run --rm php php bin/console app:productivity:create-task fb72c276-0902-4494-9d34-4d192864cfbf aea6b91b-edd3-4776-9ad5-afce776c4d9b "My List #3 - My Task #1" adam@example.com
docker-compose run --rm php php bin/console app:productivity:create-task fb72c276-0902-4494-9d34-4d192864cfbf 00b39886-9683-4a3d-a930-b1c0c5c74d9c "My List #3 - My Task #1" adam@example.com
docker-compose run --rm php bin/console streak:subscriptions:run -v
docker-compose ps
docker-compose run --rm --detach php watch --interval=0.5 --precise --no-wrap --color bin/console streak:subscriptions:run -v  # poor man's process manager
