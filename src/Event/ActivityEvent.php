<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\ActivityStreams\Activity;
use Tnc\Service\EventDispatcher\ActivityStreams\Object;
use Tnc\Service\EventDispatcher\Normalizer;

abstract class ActivityEvent extends AbstractEvent
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Object
     */
    public static function newActivityObject($objectType = null, $id = null)
    {
        return (new Object())->setObjectType($objectType)->setId($id);
    }

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
    public function getName()
    {
        return $this->getVerb();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setVerb($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        $actor = $this->activity->getActor();
        if ($actor) {
            return $actor->getObjectType() . '_' . $actor->getId();
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->activity->getVerb();
    }

    /**
     * @param string $verb
     *
     * @return $this
     */
    public function setVerb($verb)
    {
        $this->activity->setVerb($verb);

        return $this;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->activity->getProvider()->getId();
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setProvider($name)
    {
        $object = (new Object())->setId($name);

        $this->activity->setProvider($object);

        return $this;
    }

    /**
     * @param Object $type
     *
     * @return $this
     */
    public function setActor(Object $object)
    {
        $this->activity->setActor($object);

        return $this;
    }

    /**
     * @param Object $type
     *
     * @return $this
     */
    public function setObject(Object $object)
    {
        $this->activity->setObject($object);

        return $this;
    }

    /**
     * @param Object $type
     *
     * @return $this
     */
    public function setTarget(Object $object)
    {
        $this->activity->setTarget($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        $uuid = uniqid($this->activity->getVerb() . '.', true);
        $this->activity->setId($uuid);
        return $normalizer->normalize($this->activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        $this->activity = $normalizer->denormalize(Activity::class, $data);
    }
}