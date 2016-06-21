<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Event\Activity\Activity;
use Tnc\Service\EventDispatcher\Serializer;

class ActivityEvent extends AbstractEvent
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * ActivityEvent constructor.
     */
    public function __construct()
    {
        $this->activity = new Activity();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        parent::setName($name);
        $this->activity->setVerb($name);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        return $serializer->normalize($this->activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        $this->activity = $serializer->denormalize('\Tnc\Service\EventDispatcher\Event\Activity\Activity', $data);
        $this->name = $this->activity->getVerb();
    }
}