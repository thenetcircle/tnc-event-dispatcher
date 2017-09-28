<?php

namespace TNC\EventDispatcher\Tests\Mock;

use TNC\EventDispatcher\Interfaces\SerializableEvent;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class MockExternalDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function syncDispatch($eventName, SerializableEvent $event = null)
    {
        return $event;
    }
}