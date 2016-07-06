<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer;

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
     * @param Serializer $serializer
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function normalize(Serializer $serializer);

    /**
     * Denormalize array representation back to this instance.
     *
     * @param Serializer $serializer
     * @param array      $data
     *
     * @throws InvalidArgumentException
     */
    public function denormalize(Serializer $serializer, array $data);
}
