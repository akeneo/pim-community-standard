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

namespace AppBundle\Entity;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class Category extends \Pim\Bundle\CatalogBundle\Entity\Category
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        $translated = $this->getTranslation() ? $this->getTranslation()->getDescription() : null;

        return null !== $translated && '' !== $translated ? $translated : '['.$this->getCode().']';
    }

    /**
     * @param string $description
     *
     * @return Category
     */
    public function setDescription(?string $description): Category
    {
        $this->getTranslation()->setDescription($description);

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationFQCN(): string
    {
        return CategoryTranslation::class;
    }
}
