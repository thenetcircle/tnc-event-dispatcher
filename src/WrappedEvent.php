<?php

namespace TNC\EventDispatcher;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\TransportableEvent;

/**
 * Class EventWrapper
 *
 * @package TNC\EventDispatcher
 */
class WrappedEvent
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var \TNC\EventDispatcher\Interfaces\TransportableEvent
     */
    protected $event;

    /**
     * @var string
     */
    protected $transportMode;


    /**
     * @param string                                             $eventName
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     * @param string                                             $transportMode
     */
    public function __construct($eventName, TransportableEvent $event, $transportMode)
    {
        $this->eventName     = $eventName;
        $this->event         = $event;
        $this->transportMode = $transportMode;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
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
    public function getTransportMode()
    {
        return $this->transportMode;
    }
}
