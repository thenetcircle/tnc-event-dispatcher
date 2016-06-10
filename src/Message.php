<?php

namespace Tnc\Service\EventDispatcher;

/**
 * Class Message
 *
 * @package Tnc\Service\EventDispatcher
 */
class Message
{
    /**
     * @var string
     */
    protected $eventName;
    /**
     * @var \Tnc\Service\EventDispatcher\Event
     */
    protected $event;
    /**
     * @var int Decides it's a sync event or async event
     */
    protected $mode;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var int
     */
    protected $timestamp;


    /**
     * @param string $eventName
     * @param Event  $event
     * @param int    $mode
     */
    public function __construct($eventName, Event $event, $mode)
    {
        $this->eventName = $eventName;
        $this->event     = $event;
        $this->mode      = $mode;
        $this->class     = get_class($event);
        $this->timestamp = time();
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return int
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
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
