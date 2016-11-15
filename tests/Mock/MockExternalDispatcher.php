<?php

namespace Tnc\Service\EventDispatcher\Tests\Mock;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\ExternalDispatcher;

class MockExternalDispatcher implements ExternalDispatcher
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