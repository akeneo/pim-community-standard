<?php

declare(strict_types=1);

/*
 * This file is part of PimCommunityStandard.
 *
 * Copyright (c) 2018 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type;

use Pim\Bundle\EnrichBundle\Form\Type\TranslatableFieldType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class CategoryType extends \Pim\Bundle\EnrichBundle\Form\Type\CategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'description',
            TranslatableFieldType::class,
            [
                'field'             => 'description',
                'translation_class' => $this->translationDataClass,
                'entity_class'      => $this->dataClass,
                'property_path'     => 'translations',
                'widget'            => 'textarea',
            ]
        );
    }
}
