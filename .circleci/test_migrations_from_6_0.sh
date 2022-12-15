#!/usr/bin/env bash

# This script tests that the migrations are working from a 6.0 version to the current branch.
# Here is the steps to do that:
#   - Checkout and install a PIM 6.0 with icecat catalog, using docker volumes (to keep the data in next steps)
#   - Mark the current migrations as "done"
#   - Checkout and install PIM on current branch without catalog in order to use the 6.0 data
#   - Launch migrations and check there is no errors

set -eu

PROJECT_DIR=../project
MYSQL_DATA_DIR=../mysql-data
ELASTICSEARCH_DATA_DIR=../elasticsearch-data

usage() {
  echo "Usage: $0 <BRANCH>"
  echo
  echo "Example:"
  echo "    $0 TIP-1283"
  echo
}

update_docker_compose_config_to_use_volumes() {
  OVERRIDDEN_ELASTICSEARCH_VERSION=''
  if [ "$#" -gt 0 ] && [ ! -z "$1" ]
  then
    OVERRIDDEN_ELASTICSEARCH_VERSION="image: 'elastic/elasticsearch:${1}'"
  fi

  [ ! -d "${MYSQL_DATA_DIR}" ] && mkdir -p "${MYSQL_DATA_DIR}" && sudo chown -R 1000:1000 "${MYSQL_DATA_DIR}"
  [ ! -d "${ELASTICSEARCH_DATA_DIR}" ] && mkdir -p "${ELASTICSEARCH_DATA_DIR}" && sudo chown -R 1000:1000 "${ELASTICSEARCH_DATA_DIR}"
  echo "
version: '3.4'

services:
    mysql:
        volumes:
            - '${MYSQL_DATA_DIR}:/var/lib/mysql'
    elasticsearch:
        ${OVERRIDDEN_ELASTICSEARCH_VERSION}
        volumes:
            - '${ELASTICSEARCH_DATA_DIR}:/usr/share/elasticsearch/data'
" > docker-compose.override.yml
  sudo chown 1000:1000 docker-compose.override.yml
}

install_pim_without_database() {
  source .env
  APP_ENV=dev C='fpm mysql elasticsearch httpd object-storage pubsub-emulator' make up
  # Wait for docker up. This is not mandatory but it checks the containers are working properly.
  ./docker/wait_docker_up.sh
}

copy_migrations_to_upgrades_directory() {
  [ ! -d upgrades/schema ] && sudo mkdir -p upgrades/schema
  sudo cp -r vendor/akeneo/pim-community-dev/upgrades/schema/* upgrades/schema/
  sudo chown -R 1000:1000 upgrades
}

get_executed_migrations_count() {
  # If the migration_versions table does not exist (=no migrations available) we just echo 0
    echo $(docker-compose exec --env MYSQL_PWD=${APP_DATABASE_PASSWORD} mysql \
      mysql --user ${APP_DATABASE_USER} ${APP_DATABASE_NAME} --silent --skip-column-names --execute="SELECT count(*) FROM migration_versions" || echo 0)
}

if [ $# -ne 1 ]; then
    usage
    exit 1
fi
PR_BRANCH=$1

## STEP 1: install 6.0 database and index
echo "Checkout 6.0 branch..."
git branch -D real60 || true
git checkout -b real60 --track origin/6.0
sudo chown -R 1000:1000 "${PROJECT_DIR}"

echo "Install 6.0 PIM dependencies and required files (including Makefile)..."
docker run --user www-data --rm \
  --volume $(pwd):/srv/pim --volume ~/.composer:/var/www.composer --volume ~/.ssh:/var/www/.ssh \
  --workdir /srv/pim \
  --env COMPOSER_AUTH \
  akeneo/pim-php-dev:6.0 \
  composer install --no-interaction

echo "Update docker-compose configuration to use volumes for MySQL and Elasticsearch containers..."
update_docker_compose_config_to_use_volumes

echo "Launch PIM install..."
install_pim_without_database
APP_ENV=dev make database O="--catalog vendor/akeneo/pim-community-dev/src/Akeneo/Platform/Bundle/InstallerBundle/Resources/fixtures/icecat_demo_dev"

# Currently we have a bug when we install a PIM with a new database: the migrations are not marked as done.
# Meanwhile we fix this bug, we manually mark the migrations as done.
# This command will not fail when the bug will be fixed (it's not in failure when migrations are already marked as done).
echo "Mark current migrations as done..."
copy_migrations_to_upgrades_directory
docker-compose run --user www-data --rm --volume $(pwd):/srv/pim --workdir /srv/pim php \
  php bin/console doctrine:migrations:version --add --all --no-interaction


## STEP 2: Stop the containers, checkout target branch and install new PIM using previously installed catalog
echo "Stop containers and checkout $PR_BRANCH branch..."
make down || true
sudo chown -R ${UID}:${UID} "${PROJECT_DIR}"
git clean -d --force
[ -f composer.lock ] && sudo rm composer.lock
git checkout $PR_BRANCH
sudo chown -R 1000:1000 "${PROJECT_DIR}"

echo "Install $PR_BRANCH PIM dependencies and required files (including Makefile)..."
docker run --user www-data --rm \
  --volume $(pwd):/srv/pim --volume ~/.composer:/var/www.composer --volume ~/.ssh:/var/www/.ssh \
  --workdir /srv/pim \
  --env COMPOSER_AUTH \
  akeneo/pim-php-dev:8.1 \
  composer install --no-interaction

sudo rm -rf ${PROJECT_DIR}/var/cache/*

echo "Launch PIM with elasticsearch 7.17.7 as elasticsearch need to be started with 7.17.7 before upgrading to 8.5"
update_docker_compose_config_to_use_volumes 7.17.7
source .env
APP_ENV=dev make up
./docker/wait_docker_up.sh
APP_ENV=dev make down

echo "Update docker-compose configuration to use volumes for MySQL and Elasticsearch containers..."
update_docker_compose_config_to_use_volumes

echo "Launch PIM install without database reset..."
install_pim_without_database


## STEP 3: Launch migrations
echo "Copy migrations and launch them"
EXECUTED_MIGRATIONS_COUNT_BEFORE=$(get_executed_migrations_count)
echo "Number of migrations marked as done before migration: ${EXECUTED_MIGRATIONS_COUNT_BEFORE}"
copy_migrations_to_upgrades_directory
docker-compose run --user www-data --rm --volume $(pwd):/srv/pim --workdir /srv/pim php php bin/console doctrine:migrations:migrate --no-interaction

EXECUTED_MIGRATIONS_COUNT_AFTER=$(get_executed_migrations_count)
echo "Number of migrations marked as done after migration: ${EXECUTED_MIGRATIONS_COUNT_AFTER}"

if [ "${EXECUTED_MIGRATIONS_COUNT_AFTER}" == "${EXECUTED_MIGRATIONS_COUNT_BEFORE}" ]; then
  echo "No migration are executed. Test is not relevant or an error occurred."
  exit 1
fi

echo "Done"
