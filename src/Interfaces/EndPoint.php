<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\EventWrapper;
use TNC\EventDispatcher\Exception\TimeoutException;

interface EndPoint
{
    /**
     * Sends a new message
     *
     * @param \TNC\EventDispatcher\EventWrapper $event
     * @param string                            $message
     *
     * @throws TimeoutException
     */
    public function send(EventWrapper $event, $message);
}
