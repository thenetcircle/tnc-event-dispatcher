<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Exception\TimeoutException;
use TNC\EventDispatcher\Exception\NoDataException;

interface Receiver
{
    /**
     * Reads next message
     *
     * @param int   $timeout milliseconds
     *
     * @return array [$message, $receipt]
     *
     * @throws TimeoutException
     * @throws NoDataException
     */
    public function next($timeout);

    /**
     * Acknowledges a message after processed.
     *
     * @param mixed $receipt
     */
    public function ack($receipt);
}
