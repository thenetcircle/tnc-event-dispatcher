<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Normalizer\ActivityStreams;

use Tnc\Service\EventDispatcher\Normalizer\Interfaces\Denormalizable;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Normalizer\Interfaces\Normalizable;

class Activity implements Normalizable, Denormalizable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $verb;

    /**
     * @var Obj
     */
    private $provider;

    /**
     * @var Obj
     */
    private $actor;

    /**
     * @var Obj
     */
    private $object;

    /**
     * @var Obj
     */
    private $target;

    /**
     * @var string
     */
    private $published;

    /**
     * @var string
     */
    private $updated;


    // Custom Fileds

    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $version = '1.0';



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
    public function getProvider()
    {
        return $this->getEmptyObjectIfNull($this->provider);
    }

    /**
     * @param Obj $provider
     */
    public function setProvider(Obj $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return Obj
     */
    public function getActor()
    {
        return $this->getEmptyObjectIfNull($this->actor);
    }

    /**
     * @param Obj $actor
     */
    public function setActor(Obj $actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return Obj
     */
    public function getObject()
    {
        return $this->getEmptyObjectIfNull($this->object);
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
        return $this->getEmptyObjectIfNull($this->target);
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
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param string $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param string $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setContext(array $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        $data = get_object_vars($this);

        foreach ($data as $_key => $_value) {
            if ($_value === null) {
                unset($data[$_key]);
                continue;
            }

            if (is_object($_value) && ($_value instanceof Normalizable)) {
                $data[$_key] = $serializer->normalize($_value);
            }
            elseif (is_array($_value)) {
                $data[$_key] = $_value;
            }
            else {
                $data[$_key] = (string)$_value;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Serializer $serializer, array $data)
    {
        foreach ($data as $_key => $_value) {
            if (in_array($_key, array('actor', 'object', 'target', 'provider', 'generator'), true)) {
                $this->{$_key} = $serializer->denormalize($_value, Obj::class);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }

    /**
     * @param Obj $object
     *
     * @return Obj
     */
    protected function getEmptyObjectIfNull($object)
    {
        return $object === null ? new Obj() : $object;
    }
}
