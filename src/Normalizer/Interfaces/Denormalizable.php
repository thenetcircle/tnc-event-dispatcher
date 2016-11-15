<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Serializer;

/**
 * Denormalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Denormalizable
{
    /**
     * Denormalize array representation back to this instance.
     *
     * @param \Tnc\Service\EventDispatcher\Serializer $serializer
     * @param array      $data
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalize(\Tnc\Service\EventDispatcher\Serializer $serializer, array $data);
}
