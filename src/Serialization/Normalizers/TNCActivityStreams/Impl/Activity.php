<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Beineng Ma <baineng.ma@gmail.com>
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
 */
class Activity
{
    /**
     * @var string
     */
    private $version = '1.0';

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $verb = '';

    /**
     * @var mixed
     */
    private $content = '';

    /**
     * @var ActivityObject
     */
    private $actor = null;

    /**
     * @var ActivityObject
     */
    private $object = null;

    /**
     * @var ActivityObject
     */
    private $target = null;

    /**
     * @var ActivityObject
     */
    private $provider = null;

    /**
     * @var string
     */
    private $published = '';

    /**
     * @var ActivityObject
     */
    private $generator = null;

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return Activity
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return get_object_vars($this);
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
     * @return Activity
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Activity
     */
    public function setVerb($verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return Activity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getActor()
    {
        return $this->actor ?: ($this->actor = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $actor
     *
     * @return Activity
     */
    public function setActor($actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getObject()
    {
        return $this->object ?: ($this->object = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $object
     *
     * @return Activity
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getTarget()
    {
        return $this->target ?: ($this->target = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $target
     *
     * @return Activity
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getProvider()
    {
        return $this->provider ?: ($this->provider = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $provider
     *
     * @return Activity
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

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
     * @return Activity
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject
     */
    public function getGenerator()
    {
        return $this->generator ?: ($this->generator = new ActivityObject());
    }

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject $generator
     *
     * @return Activity
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;

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
     * @return Activity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}