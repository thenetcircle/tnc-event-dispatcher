<?php

namespace TNC\EventDispatcher\Normalizer\Interfaces;

use TNC\EventDispatcher\Interfaces\Serializer;

/**
 * Denormalizable
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Denormalizable
{
    /**
     * Denormalize array representation back to this instance.
     *
     * @param \TNC\EventDispatcher\Interfaces\Serializer $serializer
     * @param array                                              $data
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalize(\TNC\EventDispatcher\Interfaces\Serializer $serializer, array $data);
}
