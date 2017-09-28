<?php

namespace TNC\EventDispatcher\Serialization\Normalizer\Interfaces;


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
     * @return \TNC\EventDispatcher\Utils\ActivityStreams\Activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
