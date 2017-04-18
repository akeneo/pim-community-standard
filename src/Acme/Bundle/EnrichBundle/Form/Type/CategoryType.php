<?php

namespace Acme\Bundle\EnrichBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Pim\Bundle\EnrichBundle\Form\Type\CategoryType as BaseCategoryType;

/**
 * Type for category properties
 */
class CategoryType extends BaseCategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', 'text',
            [
                'required' => true
            ]
        );
        parent::buildForm($builder, $options);
    }
}
