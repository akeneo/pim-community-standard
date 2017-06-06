Akeneo PIM Community Standard Edition
=====================================

Welcome to Akeneo PIM Product.

This repository is used to create a new PIM project based on Akeneo PIM.

If you want to contribute to the Akeneo PIM (and we will be pleased if you do!), you can fork the repository https://github.com/akeneo/pim-community-dev and submit a pull request.

1.7 | 1.6 | 1.5 | Scrutinizer | Crowdin
--- | --- | --- | ----------- | -------
[![1.7 status](https://core-ci.akeneo.com/buildStatus/icon?job=akeneo/pim-community-dev/1.7)](https://core-ci.akeneo.com/blue/organizations/jenkins/akeneo%2Fpim-community-dev/branches) | [![1.6 status](https://core-ci.akeneo.com/buildStatus/icon?job=akeneo/pim-community-dev/1.6)](https://core-ci.akeneo.com/blue/organizations/jenkins/akeneo%2Fpim-community-dev/branches) | [![1.5 status](https://core-ci.akeneo.com/buildStatus/icon?job=akeneo/pim-community-dev/1.5)](https://core-ci.akeneo.com/blue/organizations/jenkins/akeneo%2Fpim-community-dev/branches) | [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/badges/quality-score.png?s=05ef3d5d2bbfae2f9a659060b21711d275f0c1ff)](https://scrutinizer-ci.com/g/akeneo/pim-community-dev/) | [![Crowdin](https://d322cqt584bo4o.cloudfront.net/akeneo/localized.svg)](https://crowdin.com/project/akeneo)

Application Technical Information
---------------------------------

The following documentation is designed for both clients and partners and provides all technical information required to define required server(s) to run Akeneo PIM application and check that end users workstation is compatible with Akeneo PIM application:
https://docs.akeneo.com/1.7/developer_guide/installation/system_requirements/system_requirements.html

Installation instructions
-------------------------

### Recommended installation

To install Akeneo PIM for a PIM project or for evaluation, please follow: https://docs.akeneo.com/1.7/developer_guide/installation/installation_ce_archive.html

### Using Composer to create the project

Alternatively, you can install Akeneo PIM with Composer, but please make sure that all requirements are fulfilled.

If you don't have Composer yet, download it following the instructions on http://getcomposer.org/ or just run the following command:

```
    $ curl -s https://getcomposer.org/installer | php
```

Please note that you will certainly need to provide your GitHub credentials with this method.
A lot of our dependencies are coming from GitHub and this reaches the max limit of 50 API calls from anonymous users.

```
    $ php composer.phar create-project --prefer-dist akeneo/pim-community-standard ./pim-project "1.7.*@stable"
```

After that, follow the instructions here: https://docs.akeneo.com/1.7/developer_guide/installation/installation_ce_archive.html

Upgrade instructions
--------------------

To upgrade Akeneo PIM to a newer version, please follow:
http://docs.akeneo.com/1.7/developer_guide/migration/index.html

