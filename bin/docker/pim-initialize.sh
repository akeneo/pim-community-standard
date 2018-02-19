#!/usr/bin/env bash

docker-compose exec fpm rm -rf var/cache/*

docker-compose exec fpm bin/console --env=prod pim:install --force --symlink --clean

docker-compose run --rm node yarn run webpack-dev
