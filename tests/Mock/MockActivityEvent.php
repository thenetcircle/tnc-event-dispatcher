<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Tests\Mock;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityDenormalizable;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\ActivityNormalizable;

class MockActivityEvent extends AbstractEvent implements ActivityNormalizable, ActivityDenormalizable
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

    public function normalizeActivity()
    {
        return $this->activity;
    }

    public function denormalizeActivity(\Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\Activity $activity)
    {
        $extra = $activity->getExtra();

        if (isset($extra['name'])) {
            $this->name = $extra['name'];
        }
        if (isset($extra['group'])) {
            $this->group = $extra['group'];
        }
        if (isset($extra['mode'])) {
            $this->mode = $extra['mode'];
        }
        if (isset($extra['propagationStopped'])) {
            $this->propagationStopped = $extra['propagationStopped'];
        }

        $this->activity = $activity;
    }
}