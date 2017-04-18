<?php

namespace Acme\Bundle\MetricBundle;

use Oro\Bundle\EntityBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeMetricBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $productMappings = [
            realpath(__DIR__ . '/Resources/config/model/doctrine') => 'Acme\Bundle\MetricBundle\Model'
        ];

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createYamlMappingDriver(
                $productMappings,
                ['doctrine.orm.entity_manager'],
                'akeneo_storage_utils.storage_driver.doctrine/orm'
            )
        );
    }
}
