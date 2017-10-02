<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\WrappedEvent;

interface EndPoint
{
    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     */
    public function send($message, WrappedEvent $wrappedEvent);

    /**
     * Sets current Dispatcher instance
     *
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher);
}
