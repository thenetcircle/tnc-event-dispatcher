<?php

namespace TNC\EventDispatcher\Serializer\Normalizer\Interfaces;


/**
 * ActivityNormalizable
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityNormalizable
{
    /**
     * Normalize instance to ActivityStreams representation.
     *
     * @return \TNC\EventDispatcher\Serializer\Normalizer\ActivityStreams\Activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
