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
     * @param EventWrapper $eventWrapper
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push(EventWrapper $eventWrapper);

    /**
     * @param string $channel
     * @param int    $timeout milliseconds
     *
     * @return EventWrapper $event
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel, $timeout = 5000);

    /**
     * @param EventWrapper $event
     */
    public function ack(EventWrapper $event);
}