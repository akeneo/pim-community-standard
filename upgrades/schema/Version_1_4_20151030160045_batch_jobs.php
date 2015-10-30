<?php

namespace Pim\Upgrade\Schema;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version_1_4_20151030160045_batch_jobs
 *
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Version_1_4_20151030160045_batch_jobs extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(<<<SQL
            INSERT INTO akeneo_batch_job_instance
                (`code`, `label`, `alias`, `status`, `connector`, `type`, `rawConfiguration`)
            VALUES
                ('update_product_value',       'Mass update products',                'update_product_value',       0, 'Akeneo Mass Edit Connector', 'mass_edit',    'a:0:{}'),
                ('add_product_value',          'Mass add products values',            'add_product_value',          0, 'Akeneo Mass Edit Connector', 'mass_edit',    'a:0:{}'),
                ('edit_common_attributes',     'Mass edit common product attributes', 'edit_common_attributes',     0, 'Akeneo Mass Edit Connector', 'mass_edit',    'a:0:{}'),
                ('set_attribute_requirements', 'Set family attribute requirements',   'set_attribute_requirements', 0, 'Akeneo Mass Edit Connector', 'mass_edit',    'a:0:{}'),
                ('add_to_variant_group',       'Mass add products to variant group',  'add_to_variant_group',       0, 'Akeneo Mass Edit Connector', 'mass_edit',    'a:0:{}'),
                ('csv_product_quick_export',   'CSV product quick export',            'csv_product_quick_export',   0, 'Akeneo Mass Edit Connector', 'quick_export', 'a:4:{s:9:"delimiter";s:1:";";s:9:"enclosure";s:1:""";s:10:"withHeader";b:1;s:8:"filePath";s:52:"/tmp/products_export_%locale%_%scope%_%datetime%.csv";}')
            ;
SQL
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException();
    }
}
