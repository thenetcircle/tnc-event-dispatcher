<?php

namespace TNC\EventDispatcher\Normalizer\Interfaces;

use TNC\EventDispatcher\Normalizer\ActivityStreams\Activity;

/**
 * ActivityDenormalizable
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityDenormalizable
{
    /**
     * Denormalize ActivityStreams representation back to this instance.
     *
     * @param \TNC\EventDispatcher\Normalizer\ActivityStreams\Activity $activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\TNC\EventDispatcher\Normalizer\ActivityStreams\Activity $activity);
}
