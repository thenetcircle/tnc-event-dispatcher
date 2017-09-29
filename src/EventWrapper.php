<?php

namespace TNC\EventDispatcher\Event;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\TransportableEvent;

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
     * @var \TNC\EventDispatcher\Interfaces\TransportableEvent
     */
    protected $event;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $className;


    /**
     * @param string                                             $name
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     * @param string                                             $mode
     */
    public function __construct($name, TransportableEvent $event, $mode)
    {
        $this->name  = $name;
        $this->event = $event;
        $this->mode  = $mode;
        $this->className = get_class($event);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\TransportableEvent
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
    public function getClassName()
    {
        return $this->className;
    }
}
