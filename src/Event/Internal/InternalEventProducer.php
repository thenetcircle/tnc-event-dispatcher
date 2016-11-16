<?php

namespace Tnc\Service\EventDispatcher\Event\Internal;

use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Interfaces\Event;

abstract class InternalEventProducer
{
    /**
     * @var Dispatcher
     */
    protected $internalEventDispatcher;

    /**
     * @param \Tnc\Service\EventDispatcher\Dispatcher $dispatcher
     */
    public function setInternalEventDispatcher(Dispatcher $dispatcher)
    {
        $this->internalEventDispatcher = $dispatcher;
    }

    /**
     * @param string                                             $name
     * @param \Tnc\Service\EventDispatcher\Interfaces\Event|null $event
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
