<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Interfaces\Serializer;

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
     * @param \Tnc\Service\EventDispatcher\Interfaces\Serializer $serializer
     *
     * @return array
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalize(\Tnc\Service\EventDispatcher\Interfaces\Serializer $serializer);
}
