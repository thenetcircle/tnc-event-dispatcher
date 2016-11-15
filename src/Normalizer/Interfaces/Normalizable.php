<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

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
     * @param \Tnc\Service\EventDispatcher\Serializer $serializer
     *
     * @return array
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalize(\Tnc\Service\EventDispatcher\Serializer $serializer);
}
