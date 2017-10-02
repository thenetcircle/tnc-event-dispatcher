<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Event\Internal;

use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\WrappedEvent;

class TransportFailureEvent extends Event
{
    const NAME = 'event-dispatcher.transport.failure';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var WrappedEvent
     */
    protected $wrappedEvent;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * TransportSuccessEvent constructor.
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     * @param \Exception                        $exception
     */
    public function __construct($message, WrappedEvent $wrappedEvent, \Exception $exception)
    {
        $this->message = $message;
        $this->wrappedEvent = $wrappedEvent;
        $this->exception = $exception;
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

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}