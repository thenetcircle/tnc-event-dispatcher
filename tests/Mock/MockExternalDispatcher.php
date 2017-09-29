<?php

namespace TNC\EventDispatcher\Tests\Mock;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\ExternalDispatcher;

class MockExternalDispatcher implements ExternalDispatcher
{
    /**
     * {@inheritdoc}
     */
    public function syncDispatch($eventName, TNCActivityStreamsEvent $event = null)
    {
        return $event;
    }
}