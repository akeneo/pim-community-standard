Akeneo PIM Community Standard Edition
=====================================

Welcome to Akeneo PIM.

This repository is used to create a new PIM project based on Akeneo PIM.

If you want to contribute to the Akeneo PIM (and we will be pleased if you do!), you can fork the repository https://github.com/akeneo/pim-community-dev and submit a pull request.

Installation instructions
-------------------------

### Development Installation with Docker

## Requirements
 - Docker 19+
 - docker-compose >= 1.24
 - make

## Creating a project and starting the PIM
The following steps will install Akeneo PIM in the current directory (must be empty) and launch it from there:

```bash
$ docker run -u www-data -v $(pwd):/srv/pim -w /srv/pim --rm akeneo/pim-php-dev:8.1 \
    php /usr/local/bin/composer create-project --prefer-dist \
    akeneo/pim-community-standard /srv/pim "dev-master@dev"
```
```
$ make

```
For Windows using PowerShell : 
```docker run -u www-data -v ${PWD}:/srv/pim -w /srv/pim --rm akeneo/pim-php-dev:5.0 php -d memory_limit=4G /usr/local/bin/composer create-project akeneo/pim-community-standard /srv/pim "5.0.*@stable"```

The PIM will be available on http://localhost:8080/, with `admin/admin` as default credentials.

To shutdown your PIM: `make down`

### Installation without Docker


```bash
$ php /usr/local/bin/composer create-project --prefer-dist akeneo/pim-community-standard /srv/pim "dev-master@dev"
```

You will need to change the `.env` file to configure the access to your MySQL and ES server.

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
