<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;

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
     * Normalize instance to activity streams structure.
     *
     * @return \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function normalizeActivity();
}
