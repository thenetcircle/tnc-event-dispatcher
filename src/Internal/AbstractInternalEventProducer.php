<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Exception;
use Tnc\Service\EventDispatcher\LocalDispatcher;

class AbstractInternalEventProducer implements InternalEventProducer
{
    /**
     * @var LocalDispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function setInternalEventDispatcher(LocalDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @see LocalDispatcher::dispatch
     */
    public function dispatch($name, Event $event = null)
    {
        if ($this->dispatcher) {
            return $this->dispatcher->dispatch($name, $event);
        }
        else {
            return $event;
        }
    }
}