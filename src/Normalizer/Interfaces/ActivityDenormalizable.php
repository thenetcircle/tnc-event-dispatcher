<?php

namespace TNC\Service\EventDispatcher\Normalizer\Interfaces;

use TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;

/**
 * ActivityDenormalizable
 *
 * @package    TNC\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
interface ActivityDenormalizable
{
    /**
     * Denormalize ActivityStreams representation back to this instance.
     *
     * @param \TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity
     *
     * @throws \TNC\Service\EventDispatcher\Exception\InvalidArgumentException
     */
    public function denormalizeActivity(\TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity);
}
