<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Event\Activity;

use Tnc\Service\EventDispatcher\Normalizable;
use Tnc\Service\EventDispatcher\Serializer;

class Activity implements Normalizable
{
    /**
     * @var Obj
     */
    private $actor;
    /**
     * @var string
     */
    private $verb;
    /**
     * @var Obj
     */
    private $object;
    /**
     * @var Obj
     */
    private $target;
    /**
     * @var Obj
     */
    private $provider;
    /**
     * @var Obj
     */
    private $generator;

    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $published;
    /**
     * @var string
     */
    private $updated;


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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Obj
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * @param Obj $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
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

            if (is_object($_value) && $_value instanceof Normalizable) {
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
            if (in_array($_key, array('actor', 'verb', 'object', 'target', 'provider', 'generator'), true)) {
                $this->{$_key} = $serializer->denormalize('\Tnc\Service\EventDispatcher\Event\Activity\Obj', $_value);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }
}