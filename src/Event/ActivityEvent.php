<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Event\Activity\Entity;
use Tnc\Service\EventDispatcher\Normalizable;
use Tnc\Service\EventDispatcher\Serializer;

class ActivityEvent extends AbstractEvent
{
    /**
     * @var Object
     */
    protected $actor;
    /**
     * @var string
     */
    protected $verb;
    /**
     * @var Object
     */
    protected $object;
    /**
     * @var Object
     */
    protected $target;

    /**
     * @var string
     */
    protected $id;
    /**
     * @var Object
     */
    protected $provider;


    /**
     * @return Object
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @param Object $actor
     */
    public function setActor(Entity $actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @param string $verb
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    /**
     * @return Object
     */
    public function getActivityObject()
    {
        return $this->object;
    }

    /**
     * @param Object $object
     */
    public function setActivityObject(Entity $object)
    {
        $this->object = $object;
    }

    /**
     * @return Object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param Object $target
     */
    public function setTarget(Entity $target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Object
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Object $provider
     */
    public function setProvider(Entity $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $data = get_object_vars($this);
        unset($data['name']);

        foreach($data as $_key => $_value) {
            if(is_object($_value) && $_value instanceof Normalizable) {
                $data[$_key] = $serializer->normalize($_value);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Serializer $serializer)
    {
        foreach ($data as $_key => $_value) {
            $this->{$_key} = $_value;
        }
    }
}