<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;


/**
 * ActivityNormalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityNormalizable
{
    /**
     * Normalize instance to ActivityStreams representation.
     *
     * @return \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
