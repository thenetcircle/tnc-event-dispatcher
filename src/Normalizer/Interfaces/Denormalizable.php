<?php

namespace TNC\Service\EventDispatcher\Normalizer\Interfaces;

use TNC\Service\EventDispatcher\Interfaces\Serializer;

/**
 * Denormalizable
 *
 * @package    TNC\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface Denormalizable
{
    /**
     * Denormalize array representation back to this instance.
     *
     * @param \TNC\Service\EventDispatcher\Interfaces\Serializer $serializer
     * @param array                                              $data
     *
     * @throws \TNC\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalize(\TNC\Service\EventDispatcher\Interfaces\Serializer $serializer, array $data);
}
