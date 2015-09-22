<?php

namespace Pim\Upgrade\Schema;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version_1_4_20150922153314_product_value
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Version_1_4_20150922153314_product_value extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pim_catalog_metric CHANGE data data DOUBLE PRECISION DEFAULT NULL, CHANGE base_data base_data DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE pim_catalog_product_value_price CHANGE data data DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE pim_catalog_product_value CHANGE value_decimal value_decimal DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        throw new \RuntimeException('No revert is provided for the migrations.');
    }
}
