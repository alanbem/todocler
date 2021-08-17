#!/usr/bin/env sh

set -e # exit on error
set -x # show commands

docker-compose run --rm php bin/console streak:subscriptions:run
sleep 5
