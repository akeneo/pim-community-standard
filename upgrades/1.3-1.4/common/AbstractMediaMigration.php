<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pim\Upgrade\Common;

use AppKernel;
use Doctrine\DBAL\Driver\Connection;
use Pim\Component\Catalog\FileStorage;
use Pim\Upgrade\SchemaHelper;
use Pim\Upgrade\UpgradeHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Julien Janvier <jjanvier@akeneo.com>
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
abstract class AbstractMediaMigration
{
    const MEDIA_DIR = '/uploads/product/';
    const MEDIA_TABLE = 'pim_catalog_product_media';
    const TEMPLATE_TABLE = 'pim_catalog_product_template';

    /** @var ContainerInterface */
    protected $container;

    /** @var Connection */
    protected $ormConnection;

    /** @var SchemaHelper */
    protected $schemaHelper;

    /** @var UpgradeHelper */
    protected $upgradeHelper;

    /** @var ConsoleOutput */
    protected $output;

    /** @var string */
    protected $productMediaTable;

    /** @var string */
    protected $mediaDirectory;

    /**
     * @param ConsoleOutput $output
     * @param ArgvInput     $input
     */
    public function __construct(ConsoleOutput $output, ArgvInput $input)
    {
        $this->output = $output;

        $kernel = $this->bootKernel($input->getParameterOption(['-e', '--env'], 'dev'));

        $this->container     = $kernel->getContainer();
        $this->ormConnection = $this->container->get('database_connection');
        $this->schemaHelper  = new SchemaHelper($this->container);
        $this->upgradeHelper = new UpgradeHelper($this->container);

        $this->mediaDirectory = $input->getParameterOption(
            ['--media-directory'],
            $this->container->getParameter('kernel.root_dir') . self::MEDIA_DIR
        );

        $this->productMediaTable    = $input->getParameterOption(['--product-media-table'], self::MEDIA_TABLE);
        $this->productTemplateTable = $input->getParameterOption(['--product-template-table'], self::TEMPLATE_TABLE);

        if (!is_dir($this->mediaDirectory)) {
            throw new \RuntimeException(sprintf('The media directory "%s" does not exist', $this->mediaDirectory));
        }
    }

    /**
     * Create the akeneo_file_storage_file_info table with temporary fields to ease the migration.
     */
    public function createFileInfoTable()
    {
        $this->writeConsole('Creating table <comment>akeneo_file_storage_file_info</comment>...');
        $this->ormConnection->exec(
            'CREATE TABLE akeneo_file_storage_file_info (
                id INT AUTO_INCREMENT NOT NULL,
                file_key VARCHAR(255) NOT NULL,
                original_filename VARCHAR(255) NOT NULL,
                mime_type VARCHAR(255) NOT NULL,
                size INT DEFAULT NULL,
                extension VARCHAR(10) NOT NULL,
                hash VARCHAR(100) DEFAULT NULL,
                storage VARCHAR(255) DEFAULT NULL,
                UNIQUE INDEX UNIQ_F19B3719A5D32530 (file_key),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );

        $this->writeConsole('Adding temporary fields to table <comment>akeneo_file_storage_file_info</comment>...');
        $this->ormConnection->exec('ALTER TABLE akeneo_file_storage_file_info ADD old_file_key VARCHAR(255)');
        $this->ormConnection->exec('CREATE UNIQUE INDEX old_file_key ON akeneo_file_storage_file_info (old_file_key)');
    }

    /**
     * Store each file located in the directory to the
     * catalog filesystem in the table akeneo_file_storage_file_info.
     *
     * At the end of this method, all local files are stored in the new filesystem,
     * and for each "new" media, we know the identifier of the "old" media
     */
    public function storeLocalMedias()
    {
        $this->writeConsole(sprintf(
            'Storing medias located in <comment>%s</comment> to the catalog filesystem...',
            $this->mediaDirectory
        ));

        $storer = $this->container->get('akeneo_file_storage.file_storage.file.file_storer');
        $em     = $this->container->get('doctrine.orm.entity_manager');

        $finder      = new Finder();
        $storedFiles = 1;
        foreach ($finder->files()->followLinks()->in($this->mediaDirectory) as $file) {
            $fileInfo = $storer->store($file, FileStorage::CATALOG_STORAGE_ALIAS);
            $em->clear();
            $this->ormConnection->update(
                'akeneo_file_storage_file_info',
                ['old_file_key' => $file->getFilename()],
                ['id' => $fileInfo->getId()]
            );
            $storedFiles++;
            if (0 === $storedFiles % 500) {
                $this->writeConsole(sprintf('Stored files = %d', $storedFiles));
            }
        }
    }

    /**
     * Remove temporary fields to akeneo_file_storage_file_info
     */
    public function cleanFileInfoTable()
    {
        $this->writeConsole('Removing temporary fields to table <comment>akeneo_file_storage_file_info</comment>...');
        $this->ormConnection->exec('ALTER TABLE akeneo_file_storage_file_info DROP old_file_key');
    }

    /**
     * Remove old media table
     *
     * @param string $productMediaTable
     */
    public function dropFormerMediaTable($productMediaTable)
    {
        $this->writeConsole(sprintf('Dropping table <comment>%s</comment>...', $productMediaTable));
        $this->ormConnection->exec(sprintf('DROP TABLE %s', $productMediaTable));
    }

    /**
     * End migration
     */
    public function close()
    {
        $this->writeConsole('');
        $this->writeConsole('<info>Done!</info>');
    }

    /**
     * @return mixed
     */
    public function getProductMediaTable()
    {
        return $this->productMediaTable;
    }

    /**
     * @return SchemaHelper
     */
    public function getSchemaHelper()
    {
        return $this->schemaHelper;
    }

    /**
     * Boot kernel
     *
     * @param string $env
     *
     * @return AppKernel
     */
    protected function bootKernel($env)
    {
        $kernel = new AppKernel($env, $env === 'dev');
        $kernel->loadClassCache();
        $kernel->boot();

        return $kernel;
    }

    /**
     * @param string $message
     */
    protected function writeConsole($message)
    {
        $this->output->writeln(sprintf('<info>%s -</info> %s', date('Y-m-d H:i:s'), $message));
    }
}
