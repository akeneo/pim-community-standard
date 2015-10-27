<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pim\Upgrade\MongoDB;

use Doctrine\MongoDB\Connection;
use Pim\Upgrade\Common\AbstractMediaMigration;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @author Julien Janvier <jjanvier@akeneo.com>
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class MediaMigration extends AbstractMediaMigration
{
    const PUBLISHED_MEDIA_TABLE = 'pimee_workflow_published_product_media';

    /** @var Connection */
    protected $mongoConnection;

    /**
     * @param ConsoleOutput $output
     * @param ArgvInput     $input
     */
    public function __construct(ConsoleOutput $output, ArgvInput $input)
    {
        parent::__construct($output, $input);

        $this->mongoConnection = $this->container->get('doctrine_mongodb.odm.default_connection');
    }

    /**
     * Set back the original filename to Mongo medias.
     *
     * @param string $productValueTable
     */
    public function setOriginalFilenameToMedias($productValueTable)
    {
        $db              = $this->getMongoDatabase();
        $valueCollection = new \MongoCollection($db, $productValueTable);

        $productsWithMedia = $valueCollection->find(['values.media' => ['$ne' => null]]);

        $stmt = $this->ormConnection->prepare(
            'UPDATE akeneo_file_storage_file_info fi
            SET fi.original_filename = ?
            WHERE fi.old_file_key = ?'
        );

        foreach ($productsWithMedia as $product) {
            foreach ($product['values'] as $value) {
                if (isset($value['media']) &&
                    isset($value['media']['originalFilename']) &&
                    isset($value['media']['filename'])
                ) {
                    $stmt->bindValue(1, $value['media']['originalFilename']);
                    $stmt->bindValue(2, $value['media']['filename']);
                    $stmt->execute();
                }
            }
        }
    }

    /**
     * Link files to the Mongo product values.
     *
     * @param string $productValueTable
     */
    public function migrateMediasOnProductValue($productValueTable)
    {
        $db              = $this->getMongoDatabase();
        $valueCollection = new \MongoCollection($db, $productValueTable);

        $productsWithMedia = $valueCollection->find(['values.media' => ['$ne' => null]]);

        $stmt = $this->ormConnection->prepare('SELECT fi.id FROM akeneo_file_storage_file_info fi WHERE fi.old_file_key = ?');

        foreach ($productsWithMedia as $product) {
            foreach ($product['values'] as $index => $value) {
                if (isset($value['media']) && isset($value['media']['filename'])) {
                    $stmt->bindValue(1, $value['media']['filename']);
                    $stmt->execute();
                    $fileInfo = $stmt->fetch();

                    /*
                     db.pim_catalog_product.update(
                        {
                            _id: ObjectId("55ee950c48177e12588b5ccb"),
                            "values._id": ObjectId("55ee950c48177e12588b5cd4")
                        },
                        { $set: {"values.$.media": NumberLong(666)} }
                     )
                     */
                    if ((int) $fileInfo['id'] > 0) {
                        $valueCollection->update(
                            ['_id' => new \MongoId($product['_id']), 'values._id' => new \MongoId($value['_id'])],
                            ['$set' => ['values.$.media' => (int) $fileInfo['id']]]
                        );
                    }
                }
            }
        }
    }

    /**
     * Clean product values that contained deleted media. These values were keeped in 1.3 but should be deleted for 1.4.
     *
     * Important : Should always be executed AFTER migrateMediasOnProductValue()
     *
     * @param string $productValueTable
     */
    public function removeEmptyProductValues($productValueTable)
    {
        $db              = $this->getMongoDatabase();
        $valueCollection = new \MongoCollection($db, $productValueTable);

        /*
         db.pim_catalog_product.update(
            {"values.media": {$exists: 1}},
            {$pull: {"values": {"media._id": {$exists: 1}}}},
            {"multi": 1}
        )
         */
        $valueCollection->update(
            ['values.media' => ['$exists' => true]],
            [
                '$pull' => [
                    'values' => ['media._id' => ['$exists' => true]]
                ]
            ],
            ['multiple' => true]
        );
    }

    /**
     * @return \MongoDB
     */
    protected function getMongoDatabase()
    {
        $dbName = $this->container->getParameter('mongodb_database');

        return $this->mongoConnection->getMongoClient()->$dbName;
    }
}
