<?php

namespace TNC\Service\EventDispatcher\Event\Internal;

use TNC\Service\EventDispatcher\Dispatcher;
use TNC\Service\EventDispatcher\Interfaces\Event;

abstract class InternalEventProducer
{
    /**
     * @var Dispatcher
     */
    protected $internalEventDispatcher;

    /**
     * @param \TNC\Service\EventDispatcher\Dispatcher $dispatcher
     */
    public function setInternalEventDispatcher(Dispatcher $dispatcher)
    {
        $this->internalEventDispatcher = $dispatcher;
    }

    /**
     * @param string                                             $name
     * @param \TNC\Service\EventDispatcher\Interfaces\Event|null $event
     */
    protected function dispatchInternalEvent($name, Event $event = null)
    {
        if ($this->internalEventDispatcher) {
            try {
                $this->internalEventDispatcher->dispatch($name, $event, Dispatcher::MODE_SYNC);
            }
            catch(\Exception $e) {}
        }
    }
}
