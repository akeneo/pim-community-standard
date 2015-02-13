Akeneo PIM Community Standard Edition
=====================================
Welcome to Akeneo PIM Standard Edition.

This repository contains the minimal application needed to start a new project based on Akeneo PIM.
Practically, it means Akeneo PIM is declared as a dependency and will reside in the vendor directory.

If you want to contribute to Akeneo PIM, please use the PIM Community Dev repository located at
https://github.com/akeneo/pim-community-dev

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/badges/quality-score.png?s=05ef3d5d2bbfae2f9a659060b21711d275f0c1ff)](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/)

Requirements
------------
## System
 - PHP 5.4.* above 5.4.4
 - PHP Modules:
    - php5-curl
    - php5-gd
    - php5-intl
    - php5-mysql
    - php5-mcrypt
    - php-apc for PHP 5.4 (opcode and data cache)
    - php5-apcu for PHP 5.5 (for data cache, as opcode cache usually included)
 - PHP memory_limit at least at 256 MB on Apache side and 728 MB on CLI side (needed for installation, can be lowered to 512MB after installation for PHP-CLI)
 - MySQL 5.1 or above
 - Apache mod rewrite enabled

## Web browsers
 - tested: Chrome & Firefox
 - should work: IE 10, Safari
 - will not work: IE < 10

Installation instructions
-------------------------
### Recommended installation
To install Akeneo PIM for a PIM project or for evaluation, please follow:
http://docs.akeneo.com/master/installation/installation_workstation.html

### Create a Akeneo PIM project with Composer

Alternatively, you can install Akeneo PIM with Composer, but please make sure that all requirements are fulfilled.

If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

```
    $ curl -s https://getcomposer.org/installer | php
```

Due to some Oro Platform limitations, you **MUST** create your database before launching composer.

Please note that you will certainly need to provide your GitHub credentials with this method,
A lot of our dependencies are coming from GitHub and this reaches the max limit of 50 API calls
from anonymous users.

```
    $ php composer.phar create-project --prefer-dist akeneo/pim-community-standard ./pim-project "1.3.*@stable"
```

After that, follow the instructions here:
http://docs.akeneo.com/master/installation/installation_workstation.html#installing-akeneo

#### Write permissions

The following directories must be writable for both the CLI user and the Apache user:
- app/cache
- app/logs
- web/bundles
- app/uploads/product
- app/archive

See http://docs.akeneo.com/master/installation/installation_workstation.html#apache for an alternative solution.

### Add translation packs (optional)

You can download translation packs from crowdin: http://crowdin.net/project/akeneo

The Akeneo PIM archive contains a 'Community' directory.

To add a pack you have to :
* rename the directories by following the rule 'src/Pim/Bundle/EnrichBundle' to 'PimEnrichBundle'
* move this directory to app/Resources/
* run php app/console oro:translation:dump fr de en (if you use en, fr and de locales)

Connect to your PIM application
-------------------------------

Go to http://akeneo-pim.local/ for production mode or http://akeneo-pim.local/ for production mode.

You can now connect as Akeneo administrator with the following credentials:
- username: "admin"
- password: "admin"

Generating a clean database
---------------------------

By default, when you install the PIM, the database is preconfigured with demo data.

If you want to get only the bare minimum of data to have a clean but functional PIM,
just change the following config line in app/config/parameters.yml:

```
    installer_data: PimInstallerBundle:minimal
```

Then relaunch the install with the db option:

$ php app/console pim:installer:db --env=prod

Known issues
------------
 - with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500:
`xdebug.max_nesting_level=500`

 - not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with `php -i | grep memory` that you have enough memory according to the requirements

 - some segmentation fault and `zend_mm_heap corrupted` error can be caused as well by the circular references collector. You can disable it with the following setting in your php.ini files:
`zend.enable_gc = 0`

 - When installing with `php composer.phar create-project...` command, error about `Unable to parse file "<path>/Resources/config/web.xml".`. It seems an external issue related to libxml, you can downgrade to `libxml2.x86_64 0:2.6.26-2.1.21.el5_9.1`. Look at: http://www.akeneo.com/topic/erreur-with-php-composer-phar-beta4/ for more informations.

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/akeneo/pim-community-dev/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
