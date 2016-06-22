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
use Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject;
use Tnc\Service\EventDispatcher\Normalizer;

abstract class ActivityEvent extends AbstractEvent
{
    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @param string|null $id
     * @param string|null $objectType
     * @param string|null $content
     *
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject
     */
    public static function newActivityObject($id = null, $objectType = null, $content = null)
    {
        return (new ActivityObject())->setObjectType($objectType)
                                     ->setId($id)
                                     ->setContent($content);
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
        $this->activity->setProvider(self::newActivityObject($name));

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject
     */
    public function getActor()
    {
        return $this->activity->getActor();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject $type
     *
     * @return $this
     */
    public function setActor(ActivityObject $object)
    {
        $this->activity->setActor($object);

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject
     */
    public function getObject()
    {
        return $this->activity->getObject();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject $type
     *
     * @return $this
     */
    public function setObject(ActivityObject $object)
    {
        $this->activity->setObject($object);

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject
     */
    public function getTarget()
    {
        return $this->activity->getTarget();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\ActivityObject $type
     *
     * @return $this
     */
    public function setTarget(ActivityObject $object)
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