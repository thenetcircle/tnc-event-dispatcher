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
    const ERROR_TYPE_NO_DATA = 1;
    const ERROR_TYPE_TIMEOUT = 2;
    const ERROR_TYPE_ERROR   = 3;

    /**
     * Produce a new message to backend
     *
     * @param array       $channels
     * @param string      $message
     * @param string|null $key
     * @param int         $timeout millisecond
     *
     * @throws Exception
     */
    public function produce(array $channels, $message, $key = null, $timeout = 0);

    /**
     * Consume messages from backend
     *
     * @param array    $channels
     * @param callable $callback with parameters ($channel, $message, $key)
     * @param int      $timeout  millisecond
     *
     * @throws Exception
     */
    public function consume(array $channels, $callback, $timeout = 0);

    /**
     * @param callable $errorCallback with parameters ($type, $errStr, $errNum)
     *
     * @return $this
     */
    public function setErrorCallback($errorCallback);
}