<?php

namespace TNC\EventDispatcher\Tests\Mock;

use TNC\EventDispatcher\Interfaces\Event;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class MockExternalDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function syncDispatch($eventName, Event $event = null)
    {
        return $event;
    }
}