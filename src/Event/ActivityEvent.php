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
    public function getName()
    {
        return $this->activity->getVerb();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->activity->setVerb($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        $actor = $this->activity->getActor();
        if($actor) {
            return $actor->getObjectType() . '_' . $actor->getId();
        }
        else {
            return '';
        }
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setProvider($name) {
        $obj = (new \Tnc\Service\EventDispatcher\Event\Activity\Obj())
            ->setId($name);

        $this->activity->setProvider($obj);

        return $this;
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return $this
     */
    public function setActor($type, $id)
    {
        $obj = (new \Tnc\Service\EventDispatcher\Event\Activity\Obj())
            ->setObjectType($type)
            ->setId($id);

        $this->activity->setActor($obj);

        return $this;
    }

    /**
     * @param string $type
     * @param string $id
     * @param string $content
     *
     * @return $this
     */
    public function setObject($type, $id, $content)
    {
        $obj = (new \Tnc\Service\EventDispatcher\Event\Activity\Obj())
            ->setObjectType($type)
            ->setId($id)
            ->setContent($content);

        $this->activity->setObject($obj);

        return $this;
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return $this
     */
    public function setTarget($type, $id)
    {
        $obj = (new \Tnc\Service\EventDispatcher\Event\Activity\Obj())
            ->setObjectType($type)
            ->setId($id);

        $this->activity->setTarget($obj);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $uuid =  uniqid($this->activity->getVerb() . '.', true);
        $this->activity->setId($uuid);
        return $serializer->normalize($this->activity);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        $this->activity = $serializer->denormalize('\Tnc\Service\EventDispatcher\Event\Activity\Activity', $data);
    }
}