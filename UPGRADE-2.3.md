# UPGRADE FROM 2.2 TO 2.3

**Table of Contents:**

- [Disclaimer](#disclaimer)
- [Migrate your standard project](#migrate-your-standard-project)

## Disclaimer
    
> Please check that you're using Akeneo PIM v2.2

> We're assuming that you created your project from the standard distribution

> This documentation helps to migrate projects based on the Community Edition

> Please perform a backup of your database before proceeding to the migration. You can use tools like [mysqldump](https://dev.mysql.com/doc/refman/5.7/en/mysqldump.html).

> Please perform a backup of your codebase if you don't use a VCS (Version Control System).

## Migrate your standard project

/!\ Before starting the migration process, we advise you to stop the job queue consumer daemon and start it again only when the migration process is finished.

1. Download it from the website [PIM community standard](http://www.akeneo.com/download/) and extract:

    ```bash
    wget http://download.akeneo.com/pim-community-standard-v2.3-latest.tar.gz
    tar -zxf pim-community-standard-v2.3-latest.tar.gz
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

    mv $PIM_DIR/app/config/routing.yml $PIM_DIR/app/config/routing.yml.bak
    cp app/config/routing.yml $PIM_DIR/app/config

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
5. Register PimCatalogVolumeMonitoringBundle in the AppKernel.php :

    ```php
    protected function getPimBundles()
    {
        return [
            ...
            new Pim\Bundle\CatalogVolumeMonitoringBundle\PimCatalogVolumeMonitoringBundle(),
        ];
    }
    ```
    
6. Run a composer update:

   Then run the command to update your dependencies:

    ```bash
    cd $PIM_DIR
    php -d memory_limit=3G ../composer.phar update
    ```

    **This step will copy the upgrades folder from `pim-community-dev/` to your Pim project root in order to migrate.**
    If you have custom code in your project, this step may raise errors in the "post-script" command.
    In this case, go to the chapter "Migrate your custom code" before running the database migration.

7. Migrate your database:

    ```bash
    rm -rf var/cache
    bin/console doctrine:migration:migrate --env=prod
    ```

8. Then re-generate the PIM assets:

    ```bash
    bin/console pim:installer:assets --symlink --clean --env=prod
    yarn run webpack
    ```

9. Add the new cron in your crontab:
    ```cron
    0 22  *    *    *    php /path/to/installation/pim-community-standard/bin/console pim:volume:aggregate --env=prod > /path/to/installation/pim-community-standard/var/logs/volume_aggregate.log 2>&1
    ```

10. After all those steps, it's possible that your OPCache is out of date. So remember to restart your php-fpm daemon or apache.

