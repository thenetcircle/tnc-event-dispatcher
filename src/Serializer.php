<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Event\DefaultEvent;

interface Serializer
{
    /**
     * @param Event $event
     *
     * @return string
     */
    public function serialize(Event $event);

    /**
     * @param mixed  $data
     * @param string $type The class to which the data should be denormalized
     *
     * @return Event
     */
    public function deserialize($data, $type = DefaultEvent::class);
}
