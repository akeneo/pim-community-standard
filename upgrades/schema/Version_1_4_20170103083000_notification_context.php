<?php

namespace Pim\Upgrade\Schema;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version_1_4_20170103083000_notification_context
 *
 * @author    Patrik Karisch <p.karisch@pixelart.at>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Version_1_4_20170103083000_notification_context extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $contextMapping = [
            'pim_import_export.notification.export' => ['actionType' => 'export'],
            'pim_import_export.notification.import' => ['actionType' => 'import'],
            'pim_mass_edit.notification.mass_edit' => ['actionType' => 'mass_edit'],
            'pim_mass_edit.notification.quick_export' => ['actionType' => 'quick_export'],
        ];

        foreach ($contextMapping as $messagePart => $context) {
            $context = serialize($context);

            $this->addSql(<<<SQL
                UPDATE pim_notification_notification
                SET context = '{$context}'
                WHERE message LIKE '{$messagePart}%'
SQL
            );
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException();
    }
}
