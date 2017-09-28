<?php

namespace TNC\EventDispatcher\Serialization\Normalizer\Interfaces;

use TNC\EventDispatcher\Interfaces\Serializer;

/**
 * Normalizable
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Normalizable
{
    /**
     * Normalize instance to array representation.
     *
     * @param \TNC\EventDispatcher\Interfaces\Serializer $serializer
     *
     * @return array
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalize(\TNC\EventDispatcher\Interfaces\Serializer $serializer);
}
