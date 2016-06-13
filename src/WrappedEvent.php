<?php

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;

/**
 * Class WrappedEvent
 *
 * @package Tnc\Service\EventDispatcher
 */
class WrappedEvent
{
    /**
     * @var BaseEvent
     */
    protected $event;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $group;
    /**
     * @var string
     */
    protected $mode;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var int
     */
    protected $time;


    /**
     * @param string    $name
     * @param BaseEvent $event
     */
    public function __construct($name, BaseEvent $event, $group, $mode)
    {
        $this->event = $event;
        $this->name  = $name;
        $this->group = $group;
        $this->mode  = $mode;
        $this->class = get_class($event);
        $this->time  = time();
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
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
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }
}
