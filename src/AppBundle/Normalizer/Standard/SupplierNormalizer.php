<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Normalizer\Standard;

use AppBundle\Entity\Supplier;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Damien Carcel <damien.carcel@akeneo.com>
 */
class SupplierNormalizer implements NormalizerInterface
{
    /** @var string[] */
    private $supportedFormats = ['standard'];

    /**
     * {@inheritdoc}
     */
    public function normalize($supplier, $format = null, array $context = []): array
    {
        return [
            'id' => $supplier->getId(),
            'code' => $supplier->getCode(),
            'name' => $supplier->getName(),
            'country' => $supplier->getCountry(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Supplier && in_array($format, $this->supportedFormats);
    }
}
