<?php

namespace Tnc\Service\EventDispatcher;

/**
 * Class EventWrapper
 *
 * @package Tnc\Service\EventDispatcher
 */
class EventWrapper
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var string
     */
    protected $class;


    /**
     * @param Event  $event
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->class = get_class($event);
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
    public function getClass()
    {
        return $this->class;
    }
}
