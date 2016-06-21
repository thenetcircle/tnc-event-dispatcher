<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event;

use Tnc\Service\EventDispatcher\Event\Activity\Obj;
use Tnc\Service\EventDispatcher\Normalizable;
use Tnc\Service\EventDispatcher\Serializer;

class ActivityEvent extends AbstractEvent
{
    /**
     * @var Obj
     */
    protected $actor;
    /**
     * @var string
     */
    protected $verb;
    /**
     * @var Obj
     */
    protected $object;
    /**
     * @var Obj
     */
    protected $target;

    /**
     * @var string
     */
    protected $id;
    /**
     * @var Obj
     */
    protected $provider;


    /**
     * @return Obj
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @param Obj $actor
     */
    public function setActor(Obj $actor)
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
     * @return Obj
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param Obj $object
     */
    public function setObject(Obj $object)
    {
        $this->object = $object;
    }

    /**
     * @return Obj
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param Obj $target
     */
    public function setTarget(Obj $target)
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
     * @return Obj
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Obj $provider
     */
    public function setProvider(Obj $provider)
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