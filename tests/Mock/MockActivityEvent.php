<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Tests\Mock;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Normalizer\ActivityStreams\Activity;
use TNC\EventDispatcher\Normalizer\Interfaces\ActivityDenormalizable;
use TNC\EventDispatcher\Normalizer\Interfaces\ActivityNormalizable;

class MockActivityTNCActivityStreamsEvent implements TNCActivityStreamsEvent, ActivityNormalizable, ActivityDenormalizable
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

    public function denormalizeActivity(\TNC\EventDispatcher\Normalizer\ActivityStreams\Activity $activity)
    {
        $this->activity = $activity;
    }
}