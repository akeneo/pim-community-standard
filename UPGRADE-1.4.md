# UPGRADE FROM 1.3 to 1.4

> Please perform a backup of your database before proceeding to the migration. You can use tools like  [mysqldump](http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html) and [mongodump](http://docs.mongodb.org/manual/reference/program/mongodump/).

> Please perform a backup of your codebase if you don't use any VCS.

## Update dependencies and configuration

Download the latest [PIM community standard](http://www.akeneo.com/download/) and extract it:

```
 wget http://www.akeneo.com/pim-community-standard-v1.4-latest.tar.gz
 tar -zxf pim-community-standard-v1.4-latest.tar.gz
 cd pim-community-standard-v1.4.*/
```

Copy the following files to your PIM installation:

```
 export PIM_DIR=/path/to/your/pim/installation
 cp app/PimRequirements.php $PIM_DIR/app
 cp app/SymfonyRequirements.php $PIM_DIR/app
 cp app/config/pim_parameters.yml $PIM_DIR/app/config
 rm $PIM_DIR/upgrades/schema/Version_1_3*
 cp -Rf upgrades $PIM_DIR
 cp composer.json $PIM_DIR
```

**In case your products are stored in Mongo**, don't forget to re-add the mongo dependencies to your *composer.json*:

```
 "doctrine/mongodb-odm": "v1.0.0-beta10@dev",
 "doctrine/mongodb-odm-bundle": "v3.0.0-BETA6@dev"
```

And don't forget to add your own dependencies to your *composer.json* in case you have some.

Merge the following files into your PIM installation:
 - *app/AppKernel.php*: we have registered some new bundles (*PimAnalyticsBundle*, *PimReferenceDataBundle*, *PimConnectorBundle*, *AkeneoClassificationBundle*, *OneupFlysystemBundle*, AkeneoFileStorageBundle). We also removed a lot of Oro bundles and dependencies. The easiest way to merge is to copy the PIM-1.4 *AppKernel.php* file into your installation (`cp app/AppKernel.php $PIM_DIR/app/`), and then register your custom bundles. Don't forget to register *DoctrineMongoDBBundle* in case your products are stored with *MongoDB*.
 - *app/config/routing.yml*: we have added the entries *pim_analytics*, *pim_user*, *pim_reference_data* and *_liip_imagine*. The entry *_imagine* has been removed. The easiest way to merge is copy the PIM-1.4 *routing.yml* file into your installation (`cp app/config/routing.yml $PIM_DIR/app/config/`), and then register your custom routes.
 - *app/config/config.yml*: the entry *framework* has changed. The entries *doctrine.dbal.connections.report_source*, *doctrine.dbal.connections.report_target* and *doctrine.orm.class_metadata_factory_name* have been deleted, whereas **doctrine.dbal.connections.session* has been added. The entries *pim_reference_data* and *akeneo_storage_utils* have been added. The easiest way to merge is copy the PIM-1.4 *config.yml* file into your installation (`cp app/config/config.yml $PIM_DIR/app/config/`), and then register your own bundles' configuration.
 - *app/config/security.yml*: the entries *security.providers* and *security.encoders* have changed. The easiest way to merge is copy the PIM-1.4 *security.yml* file into your installation (`cp app/config/security.yml $PIM_DIR/app/config`), and then register your own security configuration.

Now you're ready to update your dependencies:

```
 cd $PIM_DIR
 composer update
```


## Partially fix BC breaks

If you have a standard installation with some custom code inside, the following command allows to update changed services or use statements.

**It does not cover all possible BC breaks, as the changes of arguments of a service, consider using this script on versioned files to be able to check the changes with a `git diff` for instance.**

Based on a PIM standard installation, execute the following command in your project folder:

```
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\MongoDBODM\\CompletenessRepository/CatalogBundle\\Doctrine\\MongoDBODM\\Repository\\CompletenessRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\MongoDBODM\\ProductCategoryRepository/CatalogBundle\\Doctrine\\MongoDBODM\\Repository\\ProductCategoryRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\MongoDBODM\\ProductMassActionRepository/CatalogBundle\\Doctrine\\MongoDBODM\\Repository\\ProductMassActionRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\MongoDBODM\\ProductRepository/CatalogBundle\\Doctrine\\MongoDBODM\\Repository\\ProductRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\ORM\\CompletenessRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\CompletenessRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\ORM\\ProductCategoryRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\ProductCategoryRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\ORM\\ProductMassActionRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\ProductMassActionRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Doctrine\\ORM\\ProductRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\ProductRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\AssociationRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\AssociationRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\AssociationTypeRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\AssociationTypeRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\AttributeGroupRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\AttributeGroupRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\AttributeOptionRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\AttributeOptionRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\AttributeRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\AttributeRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\CategoryRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\CategoryRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\ChannelRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\ChannelRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\CurrencyRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\CurrencyRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\FamilyRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\FamilyRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\GroupRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\GroupRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\GroupTypeRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\GroupTypeRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/CatalogBundle\\Entity\\Repository\\LocaleRepository/CatalogBundle\\Doctrine\\ORM\\Repository\\LocaleRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\AbstractValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\AbstractAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\BooleanValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\BooleanAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\DateValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\DateAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\MediaValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\MediaAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\MetricValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\MetricAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\MultiSelectValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\MultiSelectAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\NumberValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\NumberAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\PriceCollectionValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\PriceCollectionAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\SimpleSelectValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\SimpleSelectAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Setter\\TextValueSetter/Pim\\Component\\Catalog\\Updater\\Setter\\TextAttributeSetter/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\CopierInterface/Pim\\Component\\Catalog\\Updater\\Copier\\AttributeCopierInterface/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\AbstractValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\AbstractAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\BaseValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\BaseAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\MediaValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\MediaAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\MetricValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\MetricAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\MultiSelectValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\MultiSelectAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\PriceCollectionValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\PriceCollectionAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\Copier\\SimpleSelectValueCopier/Pim\\Component\\Catalog\\Updater\\Copier\\SimpleSelectAttributeCopier/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Updater\\ProductTemplateUpdaterInterface/Pim\\Component\\Catalog\\Updater\\ProductTemplateUpdaterInterface/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Doctrine\\ORM\\Repository\\CategoryRepository/Pim\\Bundle\\ClassificationBundle\\Doctrine\\ORM\\Repository\\CategoryRepository/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Repository\\CategoryRepositoryInterface/Pim\\Component\\Classification\\Repository\\CategoryRepositoryInterface/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\ConfiguratorInterface/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\ConfiguratorInterface/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\ConfigurationRegistry/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\Product\\ConfigurationRegistry/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\ContextConfigurator/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\Product\\ContextConfigurator/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\FiltersConfigurator/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\Product\\FiltersConfigurator/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\GroupColumnsConfigurator/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\Product\\GroupColumnsConfigurator/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\Product\\SortersConfigurator/Pim\\Bundle\\DataGridBundle\\Datagrid\\Configuration\\Product\\SortersConfigurator/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\RequestParametersExtractor/Pim\\Bundle\\DataGridBundle\\Datagrid\\Request\\RequestParametersExtractor/g'
    find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\DataGridBundle\\Datagrid\\RequestParametersExtractorInterface/Pim\Bundle\DataGridBundle\Datagrid\Request\RequestParametersExtractorInterface/g'
```


## Migration to Symfony  2.7

PIM now uses Symfony 2.7. To ease your migration, you can read this guide: https://gist.github.com/mickaelandrieu/5211d0047e7a6fbff925.

You can execute the following commands in your project folder:

```
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\OptionsResolver\\OptionsResolverInterface;/use Symfony\\Component\\OptionsResolver\\OptionsResolver;/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/public function setDefaultOptions(OptionsResolverInterface $resolver)/public function configureOptions(OptionsResolver $resolver)/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/var OptionsResolverInterface/var OptionsResolver/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/* @return OptionsResolverInterface/* @return OptionsResolver/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Validator\\ValidatorInterface;/use Symfony\\Component\\Validator\\Validator\\ValidatorInterface;/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Form\\Extension\\Core\\View\\ChoiceView;/use Symfony\\Component\\Form\\ChoiceList\\View\\ChoiceView;/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Form\\Tests\\Extension\\Core\\Type\\TypeTestCase;/use Symfony\\Component\\Form\\Test\\TypeTestCase;/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Validator\\MetadataFactoryInterface;/use Symfony\\Component\\Validator\\Mapping\\Factory\\MetadataFactoryInterface;/g'
    find ./src -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Validator\\ExecutionContextInterface;/use Symfony\\Component\\Validator\\Context\\ExecutionContextInterface;/g'
```

In 2.7, the `Symfony\Component\Security\Core\SecurityContext` is marked as deprecated in favor of the `Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface` and `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface` (see: http://symfony.com/blog/new-in-symfony-2-6-security-component-improvements).

```
   isGranted  => AuthorizationCheckerInterface
   getToken   => TokenStorageInterface
   setToken   => TokenStorageInterface
```

To use TokenStorageInterface:
```
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Security\\Core\\SecurityContextInterface;/use Symfony\\Component\\Security\\Core\\Authentication\\Token\\Storage\\TokenStorageInterface;/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/SecurityContextInterface/TokenStorageInterface/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/$this->securityContext/$this->tokenStorage/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/getSecurityContext/getTokenStorage/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/security.context/security.token_storage/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/SecurityContext::/Security::/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/$securityContext/$tokenStorage/g'
```

To use AuthorizationCheckerInterface:
```
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/use Symfony\\Component\\Security\\Core\\SecurityContextInterface;/use Symfony\\Component\\Security\\Core\\Authorization\\AuthorizationCheckerInterface;/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/SecurityContextInterface/AuthorizationCheckerInterface/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/$this->securityContext/$this->authorizationChecker/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/getSecurityContext/getAuthorizationChecker/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/security.context/security.authorization_checker/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/SecurityContext::/Security::/g'
    find PATH_OF_SPECIFIC_CLASSES -type f -print0 | xargs -0 sed -i 's/$securityContext/$authorizationChecker/g'
```


## Enhanced updater API

The 1.4 enhances the Updater API (introduced in 1.3).

In 1.3, the API covers only update of values of a product (set and copy), with the 1.4 we:
 - provide a way to set fields and attribute values of product (`Akeneo\Component\StorageUtils\Updater\PropertySetterInterface::setData`)
 - provide a way to add data in fields and attribute values of product (`Akeneo\Component\StorageUtils\Updater\PropertyAdderInterface::addData`)
 - provide a way to remove data in fields and attribute values of product (`Akeneo\Component\StorageUtils\Updater\PropertyRemoverInterface::removeData`)
 - provide a way to copy data in fields and attribute values of product (`Akeneo\Component\StorageUtils\Updater\PropertyCopierInterface::copyData`)
 - provide updaters for other objects (`Akeneo\Component\StorageUtils\Updater\ObjectUpdaterInterface::update`)

The goal of this API is to give a straightforward and normalized way to update objects of the PIM to enhance the Developer Experience.

To achieve a consistent API and avoid BC Breaks, we depreciated a few methods from `Pim\Component\Catalog\Updater\ProductUpdater`.

To have a better consistence between updaters and normalizers, `Pim\Bundle\TransformBundle\Normalizer\Structured\ProductValueNormalizer` now returns an array with a __*data*__ key instead of __*value*__ key.
This has an impact on the table `pim_catalog_product_template` which is used by the variant groups for instance. To convert the database structure of this table, you can execute the following command in your project folder:

```
    php upgrades/1.3-1.4/common/migrate_product_template.php --env=YOUR_ENV
```


## Upgrade import/export

The Import/Export system has been reworked.

The current system has been introduced with the 1.0 and become more and more complex to understand with successive changes.

The challenge is, in one hand to provide a more straightforward and extensible system and in other hand ensure the backward compatibility.

With the current system:
 - *AkeneoBatchBundle* is responsible to provide the batch architecture and base classes (inspired by Spring Batch)
 - *PimBaseConnector* provides Readers, Processors, Writers, others technicals classes and DI which allows to import and export Catalog Data
 - *PimTransformBundle* provides Normalizers and Denormalizers to transform array to object and object to array, some Transformers kind of "extended Denormalizers"
 - *PimImportExportBundle* provides controllers, form and UI

Responsibilities are not that clear, for instance, we have different implementations for a same service, successively introduced and kept for BC concerns.

This part is often used and extended in custom projects and backward compatibility must be handled on classes and DI levels.

To make the new system more understandable, we introduce it in a new *PimConnectorBundle* and depreciate the *PimBaseConnectorBundle*.

Strategy is the following,
 - remove the deprecated *batch_jobs.yml* in the *PimBaseConnectorBundle* (to avoid automatic loading)
 - keep old services and classes in the *PimBaseConnectorBundle* to be backward compatible
 - introduce new classes and services in the new *PimConnectorBundle* and component
 - behat and specs are runned on deprecated classes and import too

## Product edit form

With Akeneo PIM 1.4 version, we introduced a new form system on the product edit form. This new system is way faster, more flexible, more dynamic and better looking. To achieve that we built an entire new architecture based on Backbonejs and REST calls to the backend.

We rebuilt this form with extensibility as our first technical goal to ease its customization. To achieve that, we developped this new form as we were integrators. It helped us to identify needs for extensibility points and create them for everyone use.

Every modifications or customizations made on the old product edit form will not be compatible with 1.4. You can follow [the cookbooks about the new product edit form](http://docs.akeneo.com/master/cookbook/ui.html) to update them.

## Medias

### A new storage system for medias

The medias management of *Akeneo PIM 1.3* had a lot of drawbacks:
* we were not using *Gaufrette* everywhere which means it was impossible to store the medias on a remote filesystem out of the box
* the [media management](https://github.com/akeneo/pim-community-dev/blob/1.3/src/Pim/Bundle/CatalogBundle/Manager/MediaManager.php) was not clean, buggy and complicated
* the business code was linked to the way files are stored
* it was impossible to introduce a new type of file without copy/pasting the `Pim\Bundle\CatalogBundle\Manager\MediaManager` (*Akeneo PIM Enterprise 1.4* now comes with several types of files to handle: asset variations)
* `Gaufrette`is quite outdated, monolithic and not very maintained anymore

All these reasons bring to us to take a few radical solutions:
* we changed the way medias are stored
* we now use [Flysystem](http://flysystem.thephpleague.com/) instead of `Gaufrette`. `Flysystem` has the following advantages:
  + it is actively maintained and followed
  + its code is really nice, clean and not monolithic
  + it has a good and up-to-date documentation
  + it's possible to copy/paste files between several adapters thanks to the [mount manager](http://flysystem.thephpleague.com/mount-manager/)
* all files information are stored in the table `akeneo_file_storage_file_info`, the table `pim_catalog_product_media` does not exist anymore
* the [Pim\Bundle\CatalogBundle\Manager\MediaManager](https://github.com/akeneo/pim-community-dev/blob/1.3/src/Pim/Bundle/CatalogBundle/Manager/MediaManager.php) has been deleted

When we built that new system, we kept the following constraints in mind:
* don't mix the business logic and the way files are stored
* be able to store files anywhere just by editing the configuration
* be able to store any kind of file (ie: not only medias that are related to products)

### How to migrate?

We provide you the script to migrate your medias from 1.3 to 1.4. Please note that this scripts will only work if you medias are stored locally. If you did any customization on the way medias are stored, you'll surely need to take inspiration from this script and make you own.

Depending on the way the products are stored, please launch either `upgrades/1.3-1.4/orm/migrate_medias.php` or `upgrades/1.3-1.4/mongodb/migrate_medias.php` via

```
# for products that are stored with ORM
php upgrades/1.3-1.4/orm/migrate_medias.php

# for products that are stored with Mongo
php upgrades/1.3-1.4/mongodb/migrate_medias.php
```

If you do not use the default product tables or the default media directory, please read the scripts to know which options are available for you.


## Upgrade the database

To help the database migration process, we rely on [DoctrineMigrationsBundle](http://symfony.com/fr/doc/current/bundles/DoctrineMigrationsBundle/index.html). The migration can be launched with `php app/console doctrine:migrations:migrate`.

## Initialize cache and assets

```
 rm app/cache/* -rf
 php app/console cache:clear --env=prod
 php app/console pim:install:assets --env=prod
```
