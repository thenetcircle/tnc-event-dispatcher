<?php

namespace TNC\EventDispatcher\EndPoints;

use TNC\EventDispatcher\Exception\TimeoutException;
use TNC\EventDispatcher\WrappedEvent;

class EventBusEndPoint extends AbstractEndPoint
{
    public function __construct($uri)
    {
    }

    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $event
     *
     * @throws TimeoutException
     */
    public function send($message, WrappedEvent $event)
    {
        // TODO: Implement send() method.
    }
}