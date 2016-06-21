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
     * @param int|null     $timeout null will use default timeout
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push(EventWrapper $eventWrapper, $timeout = null);

    /**
     * @param string   $channel
     * @param int|null $timeout null will use default timeout
     *
     * @return EventWrapper $event
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel, $timeout = null);

    /**
     * @param EventWrapper $event
     */
    public function ack(EventWrapper $event);
}