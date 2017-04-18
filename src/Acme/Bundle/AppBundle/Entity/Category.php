<?php

namespace Acme\Bundle\AppBundle\Entity;

use Pim\Bundle\CatalogBundle\Entity\Category as BaseCategory;

class Category extends BaseCategory
{
    protected $description;

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
