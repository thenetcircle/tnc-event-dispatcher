<?php

namespace TNC\EventDispatcher\ExternalDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class SymfonyEventDispatcherAdapter extends SymfonyEventDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function syncDispatch($eventName, TNCActivityStreamsEvent $event = null)
    {
        return $this->dispatch($eventName, $event);
    }
}