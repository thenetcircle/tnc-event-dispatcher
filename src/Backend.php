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

interface Backend
{
    /**
     * Push a message to the specific channel of a pipeline
     *
     * @param string      $channel
     * @param string      $message
     * @param string|null $key
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push($channel, $message, $key = null);

    /**
     * Pop a message to the specific channel of a pipeline
     *
     * @param string $channel
     * @param int    $timeout milliseconds
     *
     * @return array [$message, $receipt]
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel, $timeout);

    /**
     * Acknowledge a message after it's consumed successfully.
     *
     * @param mixed $receipt
     */
    public function ack($receipt);

    /**
     * Sets the dispatcher which can do dispatch internal events
     *
     * @param ExternalDispatcher $externalDispatcher
     */
    public function setEventDispatcher(ExternalDispatcher $externalDispatcher);
}
