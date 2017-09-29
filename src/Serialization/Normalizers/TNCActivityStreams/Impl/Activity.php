<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl;


/**
 * ActivityStreams
 *
 * an activity consists of an actor, a verb, an an object, and a target.
 * It tells the story of a person performing an action on or with an object --
 * "Geraldine posted a photo to her album" or "John shared a video".
 * In most cases these components will be explicit, but they may also be implied.
 *
 * @see     http://activitystrea.ms/specs/json/1.0/
 *
 * @author  Service Team
 */
class Activity
{
    /**
     * @var Actor
     */
    private $actor = null;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Generator
     */
    private $generator = null;

    /**
     * @var string
     */
    private $id;

    /**
     * @var Obj
     */
    private $object = null;

    /**
     * @var string
     */
    private $published;

    /**
     * @var Provider
     */
    private $provider = null;

    /**
     * @var Target
     */
    private $target = null;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $updated;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $verb;


    // non-standard fields
    /**
     * @var array
     */
    private $context = [];

    /**
     * @var string
     */
    private $version = '1.0';
    //---


    /**
     * Activity constructor.
     */
    public function __construct()
    {
        $this->setPublished((new \DateTime())->format(\DateTime::RFC3339));
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor
     */
    public function getActor()
    {
        return $this->actor ?: new Actor();
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor $actor
     *
     * @return $this
     */
    public function setActor(Actor $actor)
    {
        $this->actor = $actor;

        return $this;
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
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Generator
     */
    public function getGenerator()
    {
        return $this->generator ?: new Generator();
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Generator $generator
     *
     * @return $this
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;

        return $this;
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj
     */
    public function getObject()
    {
        return $this->object ?: new Obj();
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj $object
     *
     * @return $this
     */
    public function setObject(Obj $object)
    {
        $this->object = $object;

        return $this;
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
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Provider
     */
    public function getProvider()
    {
        return $this->provider ?: new Provider();
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Provider $provider
     *
     * @return $this
     */
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Target
     */
    public function getTarget()
    {
        return $this->target ?: new Target();
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Target $target
     *
     * @return $this
     */
    public function setTarget(Target $target)
    {
        $this->target = $target;

        return $this;
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
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
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
     *
     * @return $this
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
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
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function addContext($key, $value)
    {
        $this->context[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function delContext($key)
    {
        if(isset($this->context[$key])) {
            unset($this->context[$key]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
