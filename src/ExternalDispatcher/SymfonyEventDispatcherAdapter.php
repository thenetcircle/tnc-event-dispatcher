<?php

namespace TNC\EventDispatcher\ExternalDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use TNC\EventDispatcher\Interfaces\Event;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class SymfonyEventDispatcherAdapter extends SymfonyEventDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function syncDispatch($eventName, Event $event = null)
    {
        return $this->dispatch($eventName, $event);
    }
}