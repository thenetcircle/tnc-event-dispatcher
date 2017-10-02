<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\EndPoints;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Event\InternalEvents\TransportFailureEvent;
use TNC\EventDispatcher\Event\InternalEvents\TransportSuccessEvent;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\WrappedEvent;

abstract class AbstractEndPoint implements EndPoint
{
    /**
     * @var \TNC\EventDispatcher\Dispatcher
     */
    protected $dispatcher = null;

    /**
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    protected function dispatchSuccessEvent($message, WrappedEvent $wrappedEvent) {
        $this->dispatcher->dispatch(
          TransportSuccessEvent::NAME,
          new TransportSuccessEvent($message, $wrappedEvent)
        );
    }

    protected function dispatchFailureEvent($message, WrappedEvent $wrappedEvent, \Exception $e) {
        $this->dispatcher->dispatch(
          TransportFailureEvent::NAME,
          new TransportFailureEvent($message, $wrappedEvent, $e)
        );
    }
}
