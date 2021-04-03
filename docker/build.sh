#!/usr/bin/env bash

docker_dir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
project_dir=$( cd "$( dirname "$docker_dir" )" && pwd )

cd $project_dir

set -e # exit on error
set -x # show commands

docker-compose down --remove-orphans
docker-compose up -d nginx postgres_event_store postgres_registered_users_projections mysql_lists_projections redis
printf "\033[0;34mwaiting...\033[0m\n"
sleep 15
docker-compose run --rm php composer install -o -a --ansi
docker-compose run --rm php bin/console cache:warmup
docker-compose run --rm php bin/console streak:snapshots:storage:reset
docker-compose run --rm php bin/console streak:event-store:schema:drop
docker-compose run --rm php bin/console streak:event-store:schema:create
docker-compose run --rm php bin/console rabbitmq:setup-fabric
docker-compose up -d supervisord
# setup users
docker-compose run --rm php bin/console app:users:register-user 8e5ebf2b-f78c-430d-b15f-0f3e710b284b adam@example.com password
docker-compose run --rm php bin/console app:users:register-user 0f50a722-e1bf-4d6b-bc5d-acce50386a79 john@example.com password
# setup lists
docker-compose run --rm php bin/console app:productivity:create-list 09adf1dc-ed73-46ab-95b8-1f475fb19c13 "My List #2 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-list fb72c276-0902-4494-9d34-4d192864cfbf "My List #3 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-list 1a32b318-3cd1-474e-832b-8d92fcc6fcd6 "My List #2 - john@example.com" john@example.com
docker-compose run --rm php bin/console app:productivity:create-list 26d467c9-bf3c-42ac-a99f-adca3a453f5c "My List #3 - john@example.com" john@example.com
# setup tasks
docker-compose run --rm php bin/console app:productivity:create-task 09adf1dc-ed73-46ab-95b8-1f475fb19c13 336913b4-0149-4a44-b5c5-2ed42d378d8b "My List #2 - My Task #1 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-task 09adf1dc-ed73-46ab-95b8-1f475fb19c13 8af624c4-58cd-4c3f-b44a-a904db59d01a "My List #2 - My Task #2 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-task fb72c276-0902-4494-9d34-4d192864cfbf aea6b91b-edd3-4776-9ad5-afce776c4d9b "My List #3 - My Task #1 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-task fb72c276-0902-4494-9d34-4d192864cfbf 00b39886-9683-4a3d-a930-b1c0c5c74d9c "My List #3 - My Task #2 - adam@example.com" adam@example.com
docker-compose run --rm php bin/console app:productivity:create-task 1a32b318-3cd1-474e-832b-8d92fcc6fcd6 a5041e9f-7288-4b3b-ae4e-85bc6b87552f "My List #2 - My Task #1 - john@example.com" john@example.com
docker-compose run --rm php bin/console app:productivity:create-task 1a32b318-3cd1-474e-832b-8d92fcc6fcd6 986bfb90-ff36-400d-bf7c-e9273d8398aa "My List #2 - My Task #2 - john@example.com" john@example.com
docker-compose run --rm php bin/console app:productivity:create-task 26d467c9-bf3c-42ac-a99f-adca3a453f5c 0328c89f-34c8-44ac-8b2b-3f5a21ad8a48 "My List #3 - My Task #1 - john@example.com" john@example.com
docker-compose run --rm php bin/console app:productivity:create-task 26d467c9-bf3c-42ac-a99f-adca3a453f5c a53f4d93-bec8-4e27-a223-aeb26593f66a "My List #3 - My Task #2 - john@example.com" john@example.com

docker-compose ps
