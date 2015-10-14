<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pim\Upgrade\ORM;

use Doctrine\DBAL\Driver\Statement;
use Pim\Upgrade\Common\AbstractMediaMigration;

/**
 * @author Julien Janvier <jjanvier@akeneo.com>
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class MediaMigration extends AbstractMediaMigration
{
    /**
     * Set back the original filename to ORM medias.
     *
     * @param string $productMediaTable
     */
    public function setOriginalFilenameToMedias($productMediaTable)
    {
        $this->ormConnection->exec(sprintf(
            'UPDATE akeneo_file_storage_file_info fi
            INNER JOIN %s pm ON fi.old_file_key = pm.filename
            SET fi.original_filename = pm.original_filename',
            $productMediaTable
        ));
    }

    /**
     * Link files to the ORM product values.
     *
     * @param string $productValueTable
     * @param string $productMediaTable
     * @param string $fkMedia
     */
    public function migrateMediasOnProductValue($productValueTable, $productMediaTable, $fkMedia)
    {
        $this->writeConsole(sprintf('Adding temporary fields to table <comment>%s</comment>...',
            $productValueTable));
        $this->ormConnection->exec(sprintf(
            'ALTER TABLE %s ADD new_media_id INT(11) NULL DEFAULT NULL AFTER media_id, ADD INDEX (new_media_id)',
            $productValueTable
        ));

        // associate the "new" media ID to the product value
        //
        // UPDATE pim_catalog_product_value pv
        // LEFT JOIN pim_catalog_product_media pm ON pv.media_id = pm.id
        // LEFT JOIN akeneo_file_storage_file_info fi ON fi.old_file_key = pm.filename
        // SET pv.new_media_id=fi.id
        // WHERE pv.media_id IS NOT NULL
        $this->ormConnection->exec(sprintf(
            'UPDATE %s pv
            LEFT JOIN %s pm ON pv.media_id = pm.id
            LEFT JOIN akeneo_file_storage_file_info fi ON fi.old_file_key = pm.filename
            SET pv.new_media_id=fi.id
            WHERE pv.media_id IS NOT NULL',
            $productValueTable,
            $productMediaTable
        ));

        $this->writeConsole(sprintf(
            'Cleaning temporary fields of table <comment>%s</comment>...',
            $productValueTable
        ));
        $this->ormConnection->exec(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $productValueTable, $fkMedia));
        $this->ormConnection->exec(sprintf('ALTER TABLE %s DROP media_id', $productValueTable));
        $this->ormConnection->exec(sprintf(
            'ALTER TABLE %s CHANGE new_media_id media_id INT(11) NULL DEFAULT NULL, ADD INDEX (media_id)',
            $productValueTable
        ));
        $this->ormConnection->exec(sprintf(
            'ALTER TABLE %s ADD FOREIGN KEY (media_id)
              REFERENCES akeneo_file_storage_file_info(id) ON DELETE SET NULL ON UPDATE RESTRICT',
            $productValueTable
        ));
        $this->ormConnection->exec(sprintf('DROP INDEX new_media_id ON %s', $productValueTable));
    }

    /**
     * Link files to the ORM product templates.
     *
     * @param string $productTemplateTable
     */
    public function migrateMediasOnProductTemplate($productTemplateTable)
    {
        $this->writeConsole('Start migration of product template media');
        // fetch all product templates with filepath
        $selectTemplates = $this->ormConnection->prepare(
            sprintf('SELECT id, valuesData from %s WHERE valuesData REGEXP \'"filepath":\'', $productTemplateTable)
        );
        $selectTemplates->execute();

        $findFileInfo     = $this->prepareFindFileInfo();
        $updateFileInfo   = $this->prepareUpdateFileInfo();

        $updateSpool = [];

        $this->writeConsole('Update product template data');
        while ($productTemplate = $selectTemplates->fetch(\PDO::FETCH_ASSOC)) {
            $valuesData    = json_decode($productTemplate['valuesData']);
            $newValuesData = $this->updateTemplateData($valuesData, $findFileInfo, $updateFileInfo);
            if (null !== $newValuesData) {
                $updateSpool[$productTemplate['id']] = $newValuesData;
            }
        }
        $selectTemplates->closeCursor();

        $updateTemplate = $this->ormConnection->prepare(
            sprintf('UPDATE %s SET valuesData = :valuesData WHERE id =  :id', $productTemplateTable)
        );
        $this->writeConsole('Update product templates');
        foreach ($updateSpool as $id => $valuesData) {
            $updateTemplate->execute(
                [
                    ':id'         => $id,
                    ':valuesData' => json_encode($valuesData),
                ]
            );
        }
        $this->writeConsole('Product template media migration <info>Done</info>.');
    }

    /**
     * Modify a media attribute to change filepath
     *
     * Structure example :
     * {
     *      "picture": [
     *          {
     *              "scope": null,
     *              "locale": null,
     *              "data": {
     *                  "originalFilename": "foo.jpeg",
     *                  "filePath": "\/var\/www\/\/pim\/app\/uploads\/product\/560ce96fc2b8c-picture---foo.jpeg"
     *              }
     *          }
     *      ]
     * }
     *
     * @param \stdClass $valuesData
     * @param Statement $findProductMedia
     * @param Statement $findFileInfo
     * @param Statement $updateFileInfo
     *
     * @return array|null
     */
    protected function updateTemplateData(
        \stdClass $valuesData,
        Statement $findFileInfo,
        Statement $updateFileInfo
    ) {
        $updateNeeded = false;

        foreach ((array) $valuesData as $attributeCode => $attributeData) {
            // a media attribute will have only one data item
            $currentData = $attributeData[0];
            if (isset($currentData->data->filePath)) {
                // find old media with old file key = basename(filePath)
                $filename = basename($currentData->data->filePath);
                // original filename is stored in the product template
                $productMedia = [
                    'original_filename' => $currentData->data->originalFilename
                ];

                $fileInfo     = $this->findFileInfo($findFileInfo, $filename);

                $this->updateFileInfo($updateFileInfo, $productMedia, $filename);

                $currentData->data->filePath = $fileInfo['file_key'];
                $currentData->data->hash     = $fileInfo['hash'];

                $updateNeeded = true;
            }
        }

        return $updateNeeded ? $valuesData : null;
    }

    /**
     * @param string $productMediaTable
     *
     * @return Statement
     */
    protected function prepareFindProductMedia($productMediaTable)
    {
        return $findProductMedia = $this->ormConnection->prepare(
            sprintf(
                'SELECT id, filename, original_filename FROM %s WHERE filename =  :oldFileKey',
                $productMediaTable
            )
        );
    }

    /**
     * @param Statement $preparedStatement
     * @param string    $filename
     *
     * @return array
     */
    protected function findProductMedia(Statement $preparedStatement, $filename)
    {
        $preparedStatement->execute([':oldFileKey' => $filename]);
        $productMedia = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
        if (false === $productMedia) {
            throw new \RuntimeException(sprintf('Unknown product media %s', $filename));
        }

        return $productMedia;
    }

    /**
     * @return Statement
     */
    protected function prepareFindFileInfo()
    {
        return $this->ormConnection->prepare(
            'SELECT id, file_key, original_filename, hash FROM akeneo_file_storage_file_info
              WHERE old_file_key =  :oldFileKey'
        );
    }

    /**
     * @param Statement $preparedStatement
     * @param           $filename
     *
     * @return array
     */
    protected function findFileInfo(Statement $preparedStatement, $filename)
    {
        $preparedStatement->execute([':oldFileKey' => $filename]);
        $fileInfo = $preparedStatement->fetch(\PDO::FETCH_ASSOC);
        if (false === $fileInfo) {
            throw new \RuntimeException(sprintf('Unknown file %s', $filename));
        }

        return $fileInfo;
    }

    /**
     * @return Statement
     */
    protected function prepareUpdateFileInfo()
    {
        return $this->ormConnection->prepare(
            'UPDATE akeneo_file_storage_file_info
              SET original_filename = :originalFilename
              WHERE old_file_key =  :oldFileKey'
        );
    }

    /**
     * @param Statement $preparedStatement
     * @param array     $productMedia
     * @param string    $filename
     *
     * @return null
     */
    protected function updateFileInfo(Statement $preparedStatement, array $productMedia, $filename)
    {
        $preparedStatement->execute(
            [
                ':originalFilename' => $productMedia['original_filename'],
                ':oldFileKey'       => $filename,
            ]
        );
    }
}
