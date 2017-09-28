<?php

namespace TNC\EventDispatcher\Event;

use TNC\EventDispatcher\Interfaces\SerializableEvent;

/**
 * Class EventWrapper
 *
 * @package TNC\EventDispatcher
 */
class EventWrapper
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \TNC\EventDispatcher\Interfaces\SerializableEvent
     */
    protected $event;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $transportToken;


    /**
     * @param string            $name
     * @param SerializableEvent $event
     * @param string            $mode
     */
    public function __construct($name, SerializableEvent $event, $mode)
    {
        $this->name  = $name;
        $this->event = $event;
        $this->mode  = $mode;
        $this->class = get_class($event);
        $this->transportToken = $event->getTransportToken();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SerializableEvent
     */
    public function getEvent()
    {
        return $this->event;
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
     * @return string
     */
    public function getTransportToken()
    {
        return !empty($this->transportToken) ? $this->transportToken : mt_rand(1, 999999);
    }
}
