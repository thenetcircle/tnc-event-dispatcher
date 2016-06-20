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
     * @param string     $name
     * @param Event|null $event
     */
    public function dispatch($name, Event $event = null);
}