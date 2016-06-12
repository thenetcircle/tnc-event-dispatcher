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
     * @param WrappedEvent $event
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push(WrappedEvent $event);

    /**
     * @return WrappedEvent
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     * @throws Exception\NoDataException
     */
    public function pop();

    /**
     * @param WrappedEvent $message
     */
    public function ack(WrappedEvent $message);
}