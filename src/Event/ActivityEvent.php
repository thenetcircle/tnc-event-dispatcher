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
use Tnc\Service\EventDispatcher\ActivityStreams\Obj;
use Tnc\Service\EventDispatcher\Normalizer;

abstract class ActivityEvent extends AbstractEvent
{
    /**
     * @var Activity
     */
    protected $activity;


    /**
     * @return $this
     */
    public static function createInstance()
    {
        return new static();
    }

    /**
     * Create a new activity object
     *
     * @param string|null $id
     * @param string|null $objectType
     * @param string|null $content
     *
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public static function obj($id = null, $objectType = null, $content = null)
    {
        return (new Obj())->setObjectType($objectType)
                          ->setId($id)
                          ->setContent($content);
    }

    /**
     * ActivityEvent constructor.
     */
    public function __construct()
    {
        $this->activity = new Activity();
        $this->setPublished((new \DateTime())->format(\DateTime::RFC3339));
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
    public function getKey()
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
    public function getId()
    {
        return $this->activity->getId();
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->activity->setId($id);

        return $this;
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
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public function getProvider()
    {
        return $this->activity->getProvider();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\Obj $object
     *
     * @return $this
     */
    public function setProvider(Obj $object)
    {
        $this->activity->setProvider($object);

        return $this;
    }

    /**
     * @return string
     */
    public function getPublished()
    {
        return $this->activity->getPublished();
    }

    /**
     * @param string $datetime
     *
     * @return $this
     */
    public function setPublished($datetime)
    {
        $this->activity->setPublished($datetime);

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public function getActor()
    {
        return $this->activity->getActor();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\Obj $object
     *
     * @return $this
     */
    public function setActor(Obj $object)
    {
        $this->activity->setActor($object);

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public function getObject()
    {
        return $this->activity->getObject();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\Obj $object
     *
     * @return $this
     */
    public function setObject(Obj $object)
    {
        $this->activity->setObject($object);

        return $this;
    }

    /**
     * @return \Tnc\Service\EventDispatcher\ActivityStreams\Obj
     */
    public function getTarget()
    {
        return $this->activity->getTarget();
    }

    /**
     * @param \Tnc\Service\EventDispatcher\ActivityStreams\Obj $object
     *
     * @return $this
     */
    public function setTarget(Obj $object)
    {
        $this->activity->setTarget($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        $this->setId($this->getUuid());
        return $normalizer->normalize($this->activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        $this->activity = $normalizer->denormalize($data, Activity::class);
    }

    /**
     * Gets the uuid for the activity
     *
     * @return string
     */
    protected function getUuid()
    {
        $uuidArr = [$this->getProvider()->getId()];
        if ($this->getActor()->getId() !== null) {
            $uuidArr[] = $this->getActor()->getObjectType();
            $uuidArr[] = $this->getActor()->getId();
        }
        $uuidArr[] = time();
        $uuidArr[] = sprintf('%03d', mt_rand(0, 999));
        return implode('-', $uuidArr);
    }
}