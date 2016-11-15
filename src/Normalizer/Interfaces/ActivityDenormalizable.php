<?php

namespace Tnc\Service\EventDispatcher\Normalizer\Interfaces;

use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;

/**
 * ActivityDenormalizable
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityDenormalizable
{
    /**
     * Normalize instance to activity streams structure.
     *
     * @param \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity);
}
