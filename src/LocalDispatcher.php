<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

interface LocalDispatcher
{
    /**
     * Dispatches an event to all listeners
     *
     * @param string     $name The name of the event
     * @param Event|null $event
     */
    public function dispatch($name, Event $event = null);

    /**
     * Gets the listeners of a specific event or all listeners sorted by descending priority.
     *
     * @param string $name The name of the event
     *
     * @return array The event listeners for the specified event, or all event listeners by event name
     */
    public function getListeners($name = null);

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $name The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($name = null);
}