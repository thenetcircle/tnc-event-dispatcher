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
use Tnc\Service\EventDispatcher\Internal\InternalEventProducer;

interface Pipeline extends InternalEventProducer
{
    /**
     * @param EventWrapper $eventWrapper
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push(EventWrapper $eventWrapper);

    /**
     * @param string   $channel
     *
     * @return EventWrapper $event
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop($channel);

    /**
     * @param EventWrapper $event
     */
    public function ack(EventWrapper $event);
}