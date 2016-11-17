<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Interfaces;

interface ExternalDispatcher
{
    /**
     * Dispatches an event to all listeners
     *
     * @param string     $eventName The name of the event
     * @param Event|null $event
     */
    public function syncDispatch($eventName, Event $event = null);
}