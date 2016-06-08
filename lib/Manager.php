<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team <benn@thenetcircle.com>
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Componment\EventDispatcher;

use Tnc\Componment\EventDispatcher\Dispatcher\AsyncDispatcher;
use Tnc\Componment\EventDispatcher\Dispatcher\SyncDispatcher;

class Manager
{
    /**
     * @var array
     */
    private $listeners = array();
    /**
     * @var array
     */
    private $sorted = array();
    /**
     * @var SyncDispatcher
     */
    private $syncDispatcher;
    /**
     * @var AsyncDispatcher
     */
    private $asyncDispatcher;


    /**
     * Manager constructor.
     *
     * @param SyncDispatcher  $syncDispatcher
     * @param AsyncDispatcher $asyncDispatcher
     */
    public function __construct($syncDispatcher, $asyncDispatcher)
    {
        $this->syncDispatcher  = $syncDispatcher;
        $this->asyncDispatcher = $asyncDispatcher;
    }

    /**
     * Dispatches an event to all listeners by synchronous or asynchronous way
     *
     * @param string     $eventName
     * @param Event|null $event
     * @param bool       $sync
     *
     * @return Event
     */
    public function dispatch($eventName, Event $event = null, $sync = true)
    {
        if ($event === null) {
            $event = new Event();
        }

        $dispatcher = $sync ? $this->syncDispatcher : $this->asyncDispatcher;
        $dispatcher->dispatch($eventName, $event, $this->getListeners($eventName), $this);

        return $event;
    }

    /**
     * Adds an event listener
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority
     *
     * @return $this
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);

        return $this;
    }

    /**
     * Removes an event listener from the specific events
     *
     * @param string   $eventName
     * @param callable $listener
     *
     * @return $this
     */
    public function removeListener($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {

            foreach ($this->listeners[$eventName] as $priority => $listeners) {
                if (($key = array_search($listener, $listeners, true)) !== false) {
                    unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
                }
            }

        }

        return $this;
    }

    /**
     * Gets the listeners of a specific event, Or all listeners if $eventName is null
     *
     * @param string|null $eventName
     *
     * @return array
     */
    public function getListeners($eventName = null)
    {
        if ($eventName === null) {

            if (!isset($this->listeners[$eventName])) {
                return array();
            }

            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];

        } else {

            foreach ($this->listeners as $eventName => $eventListeners) {
                if (!isset($this->sorted[$eventName])) {
                    $this->sortListeners($eventName);
                }
            }

            return array_filter($this->sorted);

        }
    }

    /**
     * Checks whether an event has any listeners
     *
     * @param string|null $eventName
     *
     * @return boolean
     */
    public function hasListeners($eventName = null)
    {
        return (bool)count($this->getListeners($eventName));
    }

    /**
     * Gets the priority of an event
     *
     * @param string   $eventName
     * @param callable $listener
     *
     * @return int
     */
    public function getListenerPriority($eventName, $listener)
    {
        if (isset($this->listeners[$eventName])) {

            foreach ($this->listeners[$eventName] as $priority => $listeners) {
                if (false !== ($key = array_search($listener, $listeners, true))) {
                    return $priority;
                }
            }

        }

        return 0;
    }

    /**
     * Sorts the listeners by priority.
     *
     * @param string $eventName
     */
    private function sortListeners($eventName)
    {
        krsort($this->listeners[$eventName]);
        $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
    }
}