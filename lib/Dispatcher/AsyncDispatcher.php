<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team <benn@thenetcircle.com>
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Componment\EventDispatcher\Dispatcher;

use Tnc\Componment\EventDispatcher\Backend\BackendInterface;
use Tnc\Componment\EventDispatcher\Event;
use Tnc\Componment\EventDispatcher\Manager;

class AsyncDispatcher
{
    /**
     * @var BackendInterface
     */
    protected $backend;

    /**
     * AsyncDispatcher constructor.
     *
     * @param BackendInterface $backend
     */
    public function __construct($backend)
    {
        $this->backend = $backend;
    }

    /**
     * Dispatches an event to all listeners asynchronously
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
        // TODO: Implement dispatch() method.
    }
}