<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal;

use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Exception;

class AbstractInternalEventProducer implements InternalEventProducer
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @see Dispatcher::dispatch
     */
    public function dispatch($name, Event $event = null)
    {
        if ($this->dispatcher) {
            return $this->dispatcher->dispatch($name, $event, Dispatcher::MODE_SYNC);
        }
        else {
            return $event;
        }
    }
}