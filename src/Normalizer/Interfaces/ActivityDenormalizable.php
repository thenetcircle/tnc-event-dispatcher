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
     * Denormalize ActivityStreams representation back to this instance.
     *
     * @param \Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity
     *
     * @throws \Tnc\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity);
}
