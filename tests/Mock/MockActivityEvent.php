<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\Service\EventDispatcher\Tests\Mock;

use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\ActivityDenormalizable;
use TNC\Service\EventDispatcher\Normalizer\Interfaces\ActivityNormalizable;

class MockActivityEvent implements Event, ActivityNormalizable, ActivityDenormalizable
{
    /**
     * @var Activity
     */
    private $activity = null;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getActivity()
    {
        return $this->activity;
    }

    public function getTransportToken()
    {
        return '';
    }

    public function normalizeActivity()
    {
        return $this->activity;
    }

    public function denormalizeActivity(\TNC\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity)
    {
        $this->activity = $activity;
    }
}