<?php

namespace TNC\EventDispatcher\Serialization\Normalizer\Interfaces;

use TNC\EventDispatcher\Utils\ActivityStreams\Activity;

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
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Activity $activity
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\TNC\EventDispatcher\Utils\ActivityStreams\Activity $activity);
}
