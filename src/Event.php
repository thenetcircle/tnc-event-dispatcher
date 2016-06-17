<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

class Event extends BaseEvent implements Normalizable
{
    /**
     * @var string
     */
    private $source;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $context = array();
    /**
     * @var int
     */
    private $time;

    /**
     * @var string
     */
    private $group;
    /**
     * @var string
     */
    private $mode;

    /**
     * Event constructor.
     *
     * @param array $context
     */
    public function __construct(array $context = array())
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }


    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * This will be called by Dispatcher as event is dispatching.
     *
     * @param $source
     * @param $name
     * @param $mode
     */
    public function setDispatchingInfo($source, $name, $mode, $time)
    {
        $this->source = $source;
        $this->name   = $name;
        $this->mode   = $mode;
        $this->time   = $time;
    }


    /**
     * {@inheritdoc}
     */
    public function normalize(Serializer $serializer)
    {
        return get_object_vars($this);
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