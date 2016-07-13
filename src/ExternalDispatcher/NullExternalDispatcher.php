<?php

namespace Tnc\Service\EventDispatcher\ExternalDispatcher;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\ExternalDispatcher;

class NullExternalDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($name = null)
    {
        return [];
    }
}