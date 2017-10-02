<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Event\InternalEvents;

use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\WrappedEvent;

class TransportSuccessEvent extends Event
{
    const NAME = 'event-dispatcher.transport.success';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var WrappedEvent
     */
    protected $wrappedEvent;

    /**
     * TransportSuccessEvent constructor.
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     */
    public function __construct($message, WrappedEvent $wrappedEvent)
    {
        $this->message = $message;
        $this->wrappedEvent = $wrappedEvent;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \TNC\EventDispatcher\WrappedEvent
     */
    public function getWrappedEvent()
    {
        return $this->wrappedEvent;
    }
}