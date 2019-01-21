#!/usr/bin/env bash

set -e
cd "$(dirname "$0")"
cd ./../../

if [ ! -f ./app/config/parameters.yml ]; then
    cp ./app/config/parameters.yml.dist ./app/config/parameters.yml
    sed -i "s/database_host:.*localhost/database_host: mysql/g" ./app/config/parameters.yml
    sed -i "s/localhost: 9200/elastic:changeme@elasticsearch:9200/g" ./app/config/parameters.yml
fi
