<?php

namespace TNC\EventDispatcher\Event\Internal;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;

abstract class InternalEventProducer
{
    /**
     * @var Dispatcher
     */
    protected $internalEventDispatcher;

    /**
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setInternalEventDispatcher(Dispatcher $dispatcher)
    {
        $this->internalEventDispatcher = $dispatcher;
    }

    /**
     * @param string                                                       $name
     * @param \TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent|null $event
     */
    protected function dispatchInternalEvent($name, TNCActivityStreamsEvent $event = null)
    {
        if ($this->internalEventDispatcher) {
            try {
                $this->internalEventDispatcher->dispatch($name, $event, Dispatcher::MODE_SYNC);
            }
            catch(\Exception $e) {}
        }
    }
}
