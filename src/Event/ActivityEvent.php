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
        if ($actor) {
            return $actor->getObjectType() . '_' . $actor->getId();
        } else {
            return '';
        }
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
     * @param string $type
     * @param string $id
     *
     * @return $this
     */
    public function setActor($type, $id)
    {
        $object = (new Object())->setObjectType($type)
                                ->setId($id);

        $this->activity->setActor($object);

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
        $object = (new Object())->setObjectType($type)
                                ->setId($id)
                                ->setContent($content);

        $this->activity->setObject($object);

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
        $object = (new Object())->setObjectType($type)
                                ->setId($id);

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