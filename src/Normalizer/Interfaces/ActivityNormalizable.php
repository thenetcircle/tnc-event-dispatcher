<?php

namespace TNC\Service\EventDispatcher\Normalizer\Interfaces;


/**
 * ActivityNormalizable
 *
 * @package    TNC\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityNormalizable
{
    /**
     * Normalize instance to ActivityStreams representation.
     *
     * @return \TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity
     *
     * @throws \TNC\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
