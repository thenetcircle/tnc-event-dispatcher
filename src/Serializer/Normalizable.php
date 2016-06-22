<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Normalizer;

/**
 * Normalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Normalizable
{
    /**
     * Normalize instance to array representation.
     *
     * @param Normalizer $normalizer
     *
     * @return array
     */
    public function normalize(Normalizer $normalizer);

    /**
     * Denormalize array representation back to this instance.
     *
     * @param array      $data
     * @param Normalizer $normalizer
     */
    public function denormalize(array $data, Normalizer $normalizer);
}
