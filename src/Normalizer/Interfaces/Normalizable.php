<?php

namespace TNC\Service\EventDispatcher\Normalizer\Interfaces;

use TNC\Service\EventDispatcher\Interfaces\Serializer;

/**
 * Normalizable
 *
 * @package    TNC\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Normalizable
{
    /**
     * Normalize instance to array representation.
     *
     * @param \TNC\Service\EventDispatcher\Interfaces\Serializer $serializer
     *
     * @return array
     *
     * @throws \TNC\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalize(\TNC\Service\EventDispatcher\Interfaces\Serializer $serializer);
}
