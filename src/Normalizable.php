<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

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
     */
    public function normalize(Serializer $serializer);

    /**
     * Denormalize array representation back to this instance.
     *
     * @param array      $data
     * @param Serializer $serializer
     *
     * @return Normalizable
     *
     * @throws InvalidArgumentException
     */
    public function denormalize(array $data, Serializer $serializer);
}
