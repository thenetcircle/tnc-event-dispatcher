<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception;

interface Driver
{
    /**
     * Push a message to the specific channel of a pipeline
     *
     * @param string      $channel
     * @param string      $message
     * @param int         $timeout millisecond
     * @param string|null $group   the messages in same group will be ordered
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push($channel, $message, $timeout = 200, $group = null);

    /**
     * Pop a message to the specific channel of a pipeline
     *
     * @param string $channel
     * @param int    $timeout millisecond
     *
     * @return array [$message, $receipt]
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel, $timeout = (120 * 1000));

    /**
     * Acknowledge a message after it's consumed successfully.
     *
     * @param mixed $receipt
     */
    public function ack($receipt);
}