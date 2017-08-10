#!/usr/bin/env bash

currentDir=$(dirname "$0")

echo "Clean previous install"

rm -rf ${currentDir}/../../app/cache/*
rm -rf ${currentDir}/../../app/logs/*

echo "Install the PIM database"

docker-compose exec akeneo app/console --env=prod pim:install --force --symlink
