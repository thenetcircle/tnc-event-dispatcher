<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams;

use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;

class TNCActivityBuilder
{
    /**
     * @var Activity
     */
    protected $activity = null;

    /**
     * ActivityBuilder constructor.
     */
    public function __construct()
    {
        $this->activity = new Activity();
        $this->setPublished((new \DateTime())->format(\DateTime::RFC3339));
    }

    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->activity->id = $id;
        return $this;
    }

    /**
     * @param string $verb
     *
     * @return $this
     */
    public function setVerb($verb)
    {
        $this->activity->verb = $verb;
        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor $actor
     *
     * @return $this
     */
    public function setActor($actor)
    {
        $this->activity->actor = $actor;
        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj $object
     *
     * @return $this
     */
    public function setObject($object)
    {
        $this->activity->object = $object;
        return $this;
    }

    /**
     * @param string $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->activity->published = $published;
        return $this;
    }

    /**
     * @param string $provider
     *
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->activity->provider = $provider;
        return $this;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setContext(array $context)
    {
        $this->activity->context = $context;
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
        $this->activity->context[$key] = $value;
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
        if(isset($this->activity->context[$key])) {
            unset($this->activity->context[$key]);
        }
        return $this;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->activity->version = $version;
        return $this;
    }
}
