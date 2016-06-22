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
     * @var ActivityObject
     */
    private $actor;
    /**
     * @var string
     */
    private $verb;
    /**
     * @var ActivityObject
     */
    private $object;
    /**
     * @var ActivityObject
     */
    private $target;
    /**
     * @var ActivityObject
     */
    private $provider;
    /**
     * @var ActivityObject
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
     * @return ActivityObject
     */
    public function getActor()
    {
        return $this->getEmptyObjectIfNull($this->actor);
    }

    /**
     * @param ActivityObject $actor
     */
    public function setActor(ActivityObject $actor)
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
     * @return ActivityObject
     */
    public function getObject()
    {
        return $this->getEmptyObjectIfNull($this->object);
    }

    /**
     * @param ActivityObject $object
     */
    public function setObject(ActivityObject $object)
    {
        $this->object = $object;
    }

    /**
     * @return ActivityObject
     */
    public function getTarget()
    {
        return $this->getEmptyObjectIfNull($this->target);
    }

    /**
     * @param ActivityObject $target
     */
    public function setTarget(ActivityObject $target)
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
     * @return ActivityObject
     */
    public function getProvider()
    {
        return $this->getEmptyObjectIfNull($this->provider);
    }

    /**
     * @param ActivityObject $provider
     */
    public function setProvider(ActivityObject $provider)
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
     * @return ActivityObject
     */
    public function getGenerator()
    {
        return $this->getEmptyObjectIfNull($this->generator);
    }

    /**
     * @param ActivityObject $generator
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
     * @param ActivityObject $object
     *
     * @return ActivityObject
     */
    public function getEmptyObjectIfNull($object)
    {
        return $object === null ? new ActivityObject() : $object;
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
                $this->{$_key} = $normalizer->denormalize(ActivityObject::class, $_value);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }
}