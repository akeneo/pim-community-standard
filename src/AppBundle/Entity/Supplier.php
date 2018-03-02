<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class Supplier extends AbstractCustomEntity
{
    /** @var string */
    private $name;

    /** @var string */
    private $country;

    /**
     * {@inheritdoc}
     */
    public function getCustomEntityName(): string
    {
        return 'supplier';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSortOrderColumn(): string
    {
        return 'name';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabelProperty(): string
    {
        return 'name';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
}
