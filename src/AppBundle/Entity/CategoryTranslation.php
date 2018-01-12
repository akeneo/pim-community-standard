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
class CategoryTranslation extends \Pim\Bundle\CatalogBundle\Entity\CategoryTranslation
{
    /** @var string */
    protected $description;

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return CategoryTranslation
     */
    public function setDescription(?string $description): CategoryTranslation
    {
        $this->description = $description;

        return $this;
    }
}
