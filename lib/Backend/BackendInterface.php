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
     * @param array  $channels
     * @param string $message
     *
     * @throws Exception
     */
    public function produce(array $channels, $message);

    /**
     * Consume messages from backend, And will call $callback with parameters $message
     *
     * @param array    $channels
     * @param callable $callback
     *
     * @throws Exception
     */
    public function consume(array $channels, $callback);
}