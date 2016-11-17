<?php

namespace TNC\Service\EventDispatcher\ExternalDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Interfaces\ExternalDispatcher;

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