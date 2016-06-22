<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\ActivityStreams;

use Tnc\Service\EventDispatcher\Normalizer;
use Tnc\Service\EventDispatcher\Serializer\Normalizable;

class Activity implements Normalizable
{
    /**
     * @var Object
     */
    private $actor;
    /**
     * @var string
     */
    private $verb;
    /**
     * @var Object
     */
    private $object;
    /**
     * @var Object
     */
    private $target;
    /**
     * @var Object
     */
    private $provider;
    /**
     * @var Object
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
     * @return Object
     */
    public function getActor()
    {
        return $this->getEmptyObjectIfNull($this->actor);
    }

    /**
     * @param Object $actor
     */
    public function setActor(Object $actor)
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
    public function getObject()
    {
        return $this->getEmptyObjectIfNull($this->object);
    }

    /**
     * @param Object $object
     */
    public function setObject(Object $object)
    {
        $this->object = $object;
    }

    /**
     * @return Object
     */
    public function getTarget()
    {
        return $this->getEmptyObjectIfNull($this->target);
    }

    /**
     * @param Object $target
     */
    public function setTarget(Object $target)
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
        return $this->getEmptyObjectIfNull($this->provider);
    }

    /**
     * @param Object $provider
     */
    public function setProvider(Object $provider)
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
     * @return Object
     */
    public function getGenerator()
    {
        return $this->getEmptyObjectIfNull($this->generator);
    }

    /**
     * @param Object $generator
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
     * @param Object $object
     *
     * @return Object
     */
    public function getEmptyObjectIfNull($object)
    {
        return $object === null ? new Object() : $object;
    }


    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer)
    {
        $data = get_object_vars($this);

        foreach ($data as $_key => $_value) {
            if ($_value === null) {
                unset($data[$_key]);
                continue;
            }

            if (is_object($_value) && $_value instanceof Normalizable) {
                $data[$_key] = $normalizer->normalize($_value);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(array $data, Normalizer $normalizer)
    {
        foreach ($data as $_key => $_value) {
            if (in_array($_key, array('actor', 'verb', 'object', 'target', 'provider', 'generator'), true)) {
                $this->{$_key} = $normalizer->denormalize(Object::class, $_value);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }
}