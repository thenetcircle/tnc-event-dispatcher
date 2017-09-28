<?php

namespace TNC\EventDispatcher\Serializer\Normalizer\Interfaces;

use TNC\EventDispatcher\Serializer\Normalizer\ActivityStreams\Activity;

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
     * @param \TNC\EventDispatcher\Serializer\Normalizer\ActivityStreams\Activity $activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\TNC\EventDispatcher\Serializer\Normalizer\ActivityStreams\Activity $activity);
}
