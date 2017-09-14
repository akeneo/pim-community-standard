#!/usr/bin/env bash

docker-compose exec fpm bin/console --env=prod cache:clear --no-warmup

docker-compose exec fpm bin/console --env=prod pim:install --force --symlink --clean

docker-compose run --rm node yarn run webpack
