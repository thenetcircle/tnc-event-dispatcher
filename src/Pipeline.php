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

interface Pipeline
{
    /**
     * @param string       $channel
     * @param WrappedEvent $wrappedEvent
     * @param int          $timeout
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push($channel, WrappedEvent $wrappedEvent, $timeout);

    /**
     * @param string $channel
     * @param int    $timeout
     *
     * @return array [WrappedEvent $event, mixed $receipt]
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel, $timeout);

    /**
     * @param mixed $receipt
     */
    public function ack($receipt);
}