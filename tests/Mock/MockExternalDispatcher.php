<?php

namespace Tnc\Service\EventDispatcher\Tests\Mock;

use Tnc\Service\EventDispatcher\Interfaces\Event;
use Tnc\Service\EventDispatcher\Interfaces\ExternalDispatcher;

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