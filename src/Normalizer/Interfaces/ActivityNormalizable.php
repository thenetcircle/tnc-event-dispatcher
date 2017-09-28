<?php

namespace TNC\EventDispatcher\Normalizer\Interfaces;


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
     * @return \TNC\EventDispatcher\Normalizer\ActivityStreams\Activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
