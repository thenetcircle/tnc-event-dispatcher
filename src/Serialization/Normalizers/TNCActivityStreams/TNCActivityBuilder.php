<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams;


class TNCActivityBuilder
{
    /**
     * @var Activity
     */
    protected $activity = null;
    //---


    /**
     * @return static
     */
    public static function createActivity()
    {
        return new static(new Activity());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity
     *
     * @return static
     */
    public static function fromActivity(Activity $activity)
    {
        return new static($activity);
    }

    /**
     * ActivityBuilder constructor.
     */
    protected function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return \TNC\EventDispatcher\Utils\ActivityStreams\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Actor $actor
     *
     * @return $this
     */
    public function setActor(Actor $actor)
    {
        $this->activity->setActor($actor);

        return $this;
    }

    /**
     * @return $this
     */
    public function setActorByParams($objectType = '', $id = '', $content = '')
    {
        $this->activity->setActor(new Actor($objectType, $id, $content));

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->activity->setContent($content);

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Generator $generator
     *
     * @return $this
     */
    public function setGenerator(Generator $generator)
    {
        $this->activity->setGenerator($generator);

        return $this;
    }

    /**
     * @return $this
     */
    public function setGeneratorByParams($objectType = '', $id = '', $content = '')
    {
        $this->activity->setGenerator(new Generator($objectType, $id, $content));

        return $this;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->activity->setId($id);

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Obj $object
     *
     * @return $this
     */
    public function setObject(Obj $object)
    {
        $this->activity->setObject($object);

        return $this;
    }

    /**
     * @return $this
     */
    public function setObjectByParams($objectType = '', $id = '', $content = '')
    {
        $this->activity->setObject(new Obj($objectType, $id, $content));

        return $this;
    }

    /**
     * @param string $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->activity->setPublished($published);

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Provider $provider
     *
     * @return $this
     */
    public function setProvider(Provider $provider)
    {
        $this->activity->setProvider($provider);

        return $this;
    }

    /**
     * @return $this
     */
    public function setProviderByParams($objectType = '', $id = '', $content = '')
    {
        $this->activity->setProvider(new Provider($objectType, $id, $content));

        return $this;
    }

    /**
     * @param \TNC\EventDispatcher\Utils\ActivityStreams\Target $target
     *
     * @return $this
     */
    public function setTarget(Target $target)
    {
        $this->activity->setTarget($target);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTargetByParams($objectType = '', $id = '', $content = '')
    {
        $this->activity->setTarget(new Target($objectType, $id, $content));

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->activity->setTitle($title);

        return $this;
    }

    /**
     * @param string $updated
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->activity->setUpdated($updated);

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->activity->setUrl($url);

        return $this;
    }

    /**
     * @param string $verb
     *
     * @return $this
     */
    public function setVerb($verb)
    {
        $this->activity->setVerb($verb);

        return $this;
    }

    /**
     * @param array $context
     *
     * @return $this
     */
    public function setContext(array $context)
    {
        $this->activity->setContext($context);

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
        $this->activity->addContext($key, $value);

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
        $this->activity->delContext($key);

        return $this;
    }
}
