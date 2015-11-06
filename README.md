Akeneo PIM Community Standard Edition
=====================================
Welcome to Akeneo PIM Standard Edition.

This repository contains the minimal application needed to start a new project based on Akeneo PIM.
Practically, it means Akeneo PIM is declared as a dependency and will reside in the vendor directory.

If you want to contribute to Akeneo PIM, please use the PIM Community Dev repository located at
https://github.com/akeneo/pim-community-dev

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/badges/quality-score.png?s=05ef3d5d2bbfae2f9a659060b21711d275f0c1ff)](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/)

| [1.4][1.4] | [1.3][1.3] | [1.2][1.2] |
|:----------:|:----------:|:----------:|
| [![Build status][1.4 image]][1.4] | [![Build status][1.3 image]][1.3] | [![Build status][1.2 image]][1.2] |

  [1.4 image]: https://travis-ci.org/akeneo/pim-community-dev.svg?branch=1.4
  [1.4]: https://github.com/akeneo/pim-community-dev/tree/1.4
  [1.3 image]: https://travis-ci.org/akeneo/pim-community-dev.svg?branch=1.3
  [1.3]: https://github.com/akeneo/pim-community-dev/tree/1.3
  [1.2 image]: https://travis-ci.org/akeneo/pim-community-dev.svg?branch=1.2
  [1.2]: https://github.com/akeneo/pim-community-dev/tree/1.2

Application Technical Information
---------------------------------

The following document is designed for both clients and partners and provides all technical information required to define required server(s) to run Akeneo PIM application, check that end users workstation is compatible with Akeneo PIM application:
http://docs.akeneo.com/1.4/reference/technical_information/index.html

Installation instructions
-------------------------

To install Akeneo PIM for a PIM project or for evaluation, please follow:
http://docs.akeneo.com/1.4/developer_guide/installation/installation_workstation.html

Upgrade instructions
-------------------------

To upgrade Akeneo PIM to a newer version, please follow:
http://docs.akeneo.com/1.4/developer_guide/migration/index.html

Add translation packs (optional)
--------------------------------

You can download translation packs from crowdin: http://crowdin.net/project/akeneo

The Akeneo PIM archive contains a 'Community' directory.

To add a pack you have to :
* rename the directories by following the rule 'src/Pim/Bundle/EnrichBundle' to 'PimEnrichBundle'
* move this directory to app/Resources/
* run php app/console oro:translation:dump fr de en (if you use en, fr and de locales)

Connect to your PIM application
-------------------------------

Go to http://akeneo-pim.local/app_dev.php for development mode or to http://akeneo-pim.local/ for production mode.

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

```
$ php app/console pim:installer:db --env=prod
```

Known issues
------------
 - with XDebug on, the default value of max_nesting_level (100) is too low and can make the ACL loading fail (which causes 403 HTTP response code on every application screen, even the login screen). A working value is 500:
`xdebug.max_nesting_level=500`

 - not enough memory can cause the JS routing bundle to fail with a segmentation fault. Please check with `php -i | grep memory` that you have enough memory according to the requirements

 - some segmentation fault and `zend_mm_heap corrupted` error can be caused as well by the circular references collector. You can disable it with the following setting in your php.ini files:
`zend.enable_gc = 0`

 - When installing with `php composer.phar create-project...` command, error about `Unable to parse file "<path>/Resources/config/web.xml".`. It seems an external issue related to libxml, you can downgrade to `libxml2.x86_64 0:2.6.26-2.1.21.el5_9.1`. Look at: http://www.akeneo.com/topic/erreur-with-php-composer-phar-beta4/ for more informations.

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/akeneo/pim-community-dev/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
