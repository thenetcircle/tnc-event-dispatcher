<?php

namespace TNC\Service\EventDispatcher\Tests\Mock;

use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Interfaces\ExternalDispatcher;

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