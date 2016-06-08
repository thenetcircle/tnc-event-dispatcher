<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team <benn@thenetcircle.com>
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Componment\EventDispatcher\Dispatcher;

use Tnc\Componment\EventDispatcher\Event;
use Tnc\Componment\EventDispatcher\Manager;

class SyncDispatcher
{
    /**
     * Dispatches an event to all listeners synchronously
     *
     * @param string  $eventName
     * @param Event   $event
     * @param array   $listeners
     * @param Manager $dispatcher
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event, array $listeners, $dispatcher)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $dispatcher);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }
}