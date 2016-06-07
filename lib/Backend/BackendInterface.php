<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team <benn@thenetcircle.com>
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Componment\EventDispatcher\Backend;

use Tnc\Componment\EventDispatcher\Exception;

interface BackendInterface
{
    /**
     * Produce a new message to backend
     *
     * @param string $channel
     * @param string $message
     *
     * @throws Exception
     */
    public function produce($channel, $message);

    /**
     * Consume messages from backend, And will call $callback with parameters $message
     *
     * @param string   $channel
     * @param callable $callback
     *
     * @throws Exception
     */
    public function consume($channel, $callback);
}