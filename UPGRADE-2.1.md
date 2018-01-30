# UPGRADE FROM 2.0 TO 2.1

**Table of Contents:**

- [Disclaimer](#disclaimer)
- [Migrate your standard project](#migrate-your-standard-project)
- [Migrate your custom code](#migrate-your-custom-code)

## Disclaimer

> Please check that you're using Akeneo PIM v2.0

> We're assuming that you created your project from the standard distribution

> This documentation helps to migrate projects based on the Community Edition

> Please perform a backup of your database before proceeding to the migration. You can use tools like [mysqldump](https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html).

> Please perform a backup of your codebase if you don't use a VCS (Version Control System).

## Migrate your standard project

1. Download it from the website [PIM community standard](http://www.akeneo.com/download/) and extract:

    ```bash
    wget http://download.akeneo.com/pim-community-standard-v2.1-latest.tar.gz
    tar -zxf pim-community-standard-v2.1-latest.tar.gz
    cd pim-community-standard/
    ```

2. Copy the following files to your PIM installation:

    ```bash
    export PIM_DIR=/path/to/your/pim/installation
 
    mv $PIM_DIR/app/config/config.yml $PIM_DIR/app/config/config.yml.bak
    cp app/config/config.yml $PIM_DIR/app/config
 
    mv $PIM_DIR/app/config/pim_parameters.yml $PIM_DIR/app/config/pim_parameters.yml.bak
    cp app/config/pim_parameters.yml $PIM_DIR/app/config

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

7. Create the missing internal job instances:

    If the current version of your PIM is lower than 2.0.8 you'll have to execute these commands to create new internal jobs.

    * For a PIM 2.0.4 or below
    
    ```bash
    bin/console akeneo:batch:create-job "internal" "compute_completeness_of_products_family" "compute_completeness_of_products_family" "compute_completeness_of_products_family" '{"family_code":"null"}' "compute completeness of products family" --env=prod
    bin/console akeneo:batch:create-job "internal" "compute_family_variant_structure_changes" "compute_family_variant_structure_changes" "compute_family_variant_structure_changes" '{"family_variant_codes":["null"]}' "Compute family variant structure changes" --env=prod
    bin/console akeneo:batch:create-job internal add_to_existing_product_model mass_edit add_to_existing_product_model '{}' 'Add products to an existing product model' --env=prod
    ```
    
    * For a PIM 2.0.5 or 2.0.6
    
    ```bash
    bin/console akeneo:batch:create-job "internal" "compute_family_variant_structure_changes" "compute_family_variant_structure_changes" "compute_family_variant_structure_changes" '{"family_variant_codes":["null"]}' "Compute family variant structure changes" --env=prod
    bin/console akeneo:batch:create-job internal add_to_existing_product_model mass_edit add_to_existing_product_model '{}' 'Add products to an existing product model' --env=prod
    ```
    
    * For a PIM 2.0.7
    
    ```bash
    bin/console akeneo:batch:create-job internal add_to_existing_product_model mass_edit add_to_existing_product_model '{}' 'Add products to an existing product model' --env=prod
    ```
    
8. Then re-generate the PIM assets:

    ```bash
    bin/console pim:installer:assets --symlink --clean --env=prod
    yarn run webpack
    ```

## Migrate your custom code

Several classes and services have been moved or renamed. The following commands help to migrate references to them:

```bash
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.validator\.constraint\.sibling_unique_variant_axes\.class/pim_catalog\.validator\.constraint\.unique_variant_axes\.class/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/pim_catalog\.validator\.constraint\.sibling_unique_variant_axes/pim_catalog\.validator\.constraint\.unique_variant_axes/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Controller\\Rest\\ProductTemplateController/Pim\\Bundle\\EnrichBundle\\Controller\\Rest\\ValueController/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Catalog\\Validator\\Constraints\\SiblingUniqueVariantAxesValidator/Pim\\Component\\Catalog\\Validator\\Constraints\\UniqueVariantAxisValidator/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Controller\\Rest\\ValueController/Pim\\Bundle\\EnrichBundle\\Controller\\Rest\\ValuesController/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Catalog\\Validator\\Constraints\\SiblingUniqueVariantAxes/Pim\\Component\\Catalog\\Validator\\Constraints\\UniqueVariantAxis/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Connector\\Processor\\Denormalization\\AttributeFilter\\ProductModelAttributeFilter/Pim\\Component\\Catalog\\ProductModel\\Filter\\AttributeFilter\\ProductModelAttributeFilter/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Connector\\Processor\\Denormalization\\AttributeFilter\\ProductAttributeFilter/Pim\\Component\\Catalog\\ProductModel\\Filter\\AttributeFilter\\ProductAttributeFilter/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Component\\Connector\\Processor\\Denormalization\\AttributeFilter\\AttributeFilterInterface/Pim\\Component\\Catalog\\ProductModel\\Filter\\AttributeFilter\\AttributeFilterInterface/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\FilterBundle\\Filter\\Product\\CompletenessFilter/Pim\\Bundle\\FilterBundle\\Filter\\CompletenessFilter/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\Filter\\Field\\CompletenessFilter/Pim\\Bundle\\CatalogBundle\\Elasticsearch\\Filter\\Field\\CompletenessFilter/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Connector\\Processor\\QuickExport\\ProductProcessor/Pim\\Bundle\\EnrichBundle\\Connector\\Processor\\QuickExport\\ProductAndProductModelProcessor/g'
find ./src/ -type f -print0 | xargs -0 sed -i 's/Pim\\Bundle\\EnrichBundle\\Connector\\Job\\JobParameters\\ConstraintCollectionProvider\\ProductQuickExport/Pim\\Bundle\\EnrichBundle\\Connector\\Job\\JobParameters\\ConstraintCollectionProvider\\ProductAndProductModelQuickExport/g'
```
