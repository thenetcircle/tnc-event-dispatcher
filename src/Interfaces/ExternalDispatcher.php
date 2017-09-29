<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

interface ExternalDispatcher
{
    /**
     * Dispatches an event to all listeners
     *
     * @param string                       $eventName The name of the event
     * @param TNCActivityStreamsEvent|null $event
     */
    public function syncDispatch($eventName, TNCActivityStreamsEvent $event = null);
}