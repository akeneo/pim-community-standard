# UPGRADE FROM 2.1 TO 2.2

**Table of Contents:**

- [Disclaimer](#disclaimer)
- [Migrate your standard project](#migrate-your-standard-project)
- [Important note](#important-note)
- [Migrate your custom code](#migrate-your-custom-code)

## Disclaimer

> Please check that you're using Akeneo PIM v2.1

> We're assuming that you created your project from the standard distribution

> This documentation helps to migrate projects based on the Community Edition

> Please perform a backup of your database before proceeding to the migration. You can use tools like [mysqldump](https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html).

> Please perform a backup of your codebase if you don't use a VCS (Version Control System).

## Migrate your standard project

1. Download it from the website [PIM community standard](http://www.akeneo.com/download/) and extract:

    ```bash
    wget http://download.akeneo.com/pim-community-standard-v2.2-latest.tar.gz
    tar -zxf pim-community-standard-v2.2-latest.tar.gz
    cd pim-community-standard/
    ```

2. Copy the following files to your PIM installation:

    ```bash
    export PIM_DIR=/path/to/your/pim/installation

    mv $PIM_DIR/app/config/config.yml $PIM_DIR/app/config/config.yml.bak
    cp app/config/config.yml $PIM_DIR/app/config

    mv $PIM_DIR/app/config/pim_parameters.yml $PIM_DIR/app/config/pim_parameters.yml.bak
    cp app/config/pim_parameters.yml $PIM_DIR/app/config

    mv $PIM_DIR/app/config/parameters.yml.dist $PIM_DIR/app/config/parameters.yml.dist.bak
    cp app/config/parameters.yml.dist $PIM_DIR/app/config

    mv $PIM_DIR/composer.json $PIM_DIR/composer.json.bak
    cp composer.json $PIM_DIR/
    ```

3. Remove your old upgrades folder:

    ```bash
    rm -rf $PIM_DIR/upgrades/schema
    ```

4. [Optional] Update your dependencies:

    If you added dependencies to your project, you will need to do it again in your `composer.json`.
    You can display the differences of your previous composer.json in `$PIM_DIR/composer.json.bak`.

    ```JSON
    "require": {
       "your/dependency": "version",
       "your/other-dependency": "version",
    }
    ```

5. Run a composer update:

   Then run the command to update your dependencies:

    ```bash
    cd $PIM_DIR
    php -d memory_limit=3G ../composer.phar update
    ```

    **This step will copy the upgrades folder from `pim-community-dev/` to your Pim project root in order to migrate.**
    If you have custom code in your project, this step may raise errors in the "post-script" command.
    In this case, go to the chapter "Migrate your custom code" before running the database migration.

6. Migrate your database:

    ```bash
    rm -rf var/cache
    bin/console doctrine:migration:migrate --env=prod
    ```

7. Then re-generate the PIM assets:

    ```bash
    bin/console pim:installer:assets --symlink --clean --env=prod
    yarn run webpack
    ```

8. After all those steps, it's possible that your OPCache is out of date. So remember to restart your php-fpm daemon or apache.

## Important note

**IMPORTANT**: In the 2.2, it's now possible to configure the export of product models like regular products. Unfortunately, they now need a channel to know which product models to export. As we cannot define this value for you, **you will need to update your existing product model export profiles**. To do so, you only need to go the the product model profiles, check that everything fits your needs (especially in the content tab) and save them. After that your product model exports should work as expected. For more details about this feature you can visit our help center: [The power of the Product Export Builder](https://help.akeneo.com/articles/product-export-builder.html)

## Migrate your custom code

Several classes and services have been moved or renamed. The following commands help to migrate references to them:

```bash
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\ProductAndProductModelQueryBuilderFactory/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\ProductAndProductModelQueryBuilderFactory/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\CursorFactory/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\CursorFactory/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\Cursor/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\Cursor/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\AbstractCursor/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\AbstractCursor/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\IdentifierResults/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\IdentifierResults/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Elasticsearch\\IdentifierResult/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\IdentifierResult/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\ProductQueryBuilder\\MassEditProductAndProductModelQueryBuilder/Pim\\Component\\Catalog\\Query\\ProductAndProductModelQueryBuilder/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Catalog\\Updater\\ProductPropertyAdder/Pim\\Component\\Catalog\\Updater\\PropertyAdder/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Catalog\\Updater\\ProductPropertyRemover/Pim\\Component\\Catalog\\Updater\\PropertyRemover/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Catalog\\Updater\\ProductPropertyCopier/Pim\\Component\\Catalog\\Updater\\PropertyCopier/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.query\.product_and_product_model_query_builder_factory/pim_catalog\.query\.product_and_product_model_query_builder_factory/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.query\.product_and_product_model_query_builder_factory\.with_product_and_product_model_cursor/pim_catalog\.query\.product_and_product_model_query_builder_factory\.with_product_and_product_model_cursor/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.factory\.product_and_product_model_cursor/pim_catalog\.factory\.product_and_product_model_cursor/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.updater\.product_property_adder/pim_catalog\.updater\.property_adder/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.updater\.product_property_remover/pim_catalog\.updater\.property_remover/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.updater\.product_property_copier/pim_catalog\.updater\.property_copier/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.query\.elasticsearch\.product_and_model_query_builder_factory\.class/pim_catalog\.query\.elasticsearch\.product_and_model_query_builder_factory\.class/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.query\.mass_edit_product_and_product_model_query_builder\.class/pim_catalog\.query\.product_and_product_model_query_builder\.class/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_enrich\.elasticsearch\.cursor_factory\.class/pim_catalog\.elasticsearch\.cursor_factory\.class/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.updater\.product_property_adder\.class/pim_catalog\.updater\.property_adder\.class/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.updater\.product_property_remover\.class/pim_catalog\.updater\.property_remover\.class/g'
```
