Akeneo PIM Community Standard Edition
=====================================

Welcome to Akeneo PIM.

This repository is used to create a new PIM project based on Akeneo PIM.

If you want to contribute to the Akeneo PIM (and we will be pleased if you do!), you can fork the repository https://github.com/akeneo/pim-community-dev and submit a pull request.

Installation instructions
-------------------------

## Development Installation with Docker

It installs everything you need to run the PIM: source code, database, etc. This is the easiest way to have a PIM working in development mode. This is not a production setup.

### Requirements
 - Docker 19+
 - docker-compose >= 1.24
 - make

## Creating a project and starting the PIM
The following steps will install Akeneo PIM in the current directory (must be empty) and launch it from there:

```bash
$ mkdir -p ~/.cache/yarn
$ docker run -u www-data -v $(pwd):/srv/pim -w /srv/pim --rm akeneo/pim-php-dev:4.0 \
    php -d memory_limit=4G /usr/local/bin/composer create-project --prefer-dist \
    akeneo/pim-community-standard /srv/pim "4.0.*@stable"
$ make
```

The PIM will be available on http://localhost:8080/, with `admin/admin` as default credentials.

To shut down your PIM: `make down`

### Installation without Docker

Without Docker, only the source code is installed. You have to install Mysql, Elasticsearch, etc.

```bash
$ php -d memory_limit=4G /usr/local/bin/composer create-project --prefer-dist \
    akeneo/pim-community-standard /srv/pim "4.0.*@stable"
```

You will need to change the `.env` file to configure the access to your MySQL and Elasticsearch server.

Once done, you can run:

```
$ NO_DOCKER=true make

```

For more details, please follow https://docs.akeneo.com/master/install_pim

Upgrade instructions
--------------------

To upgrade Akeneo PIM to a newer version, please follow:
https://docs.akeneo.com/master/migrate_pim/index.html

Changelog
---------
You can check out the changelog files in https://github.com/akeneo/pim-community-dev.
