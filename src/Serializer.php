<?php

namespace Tnc\Service\EventDispatcher;

interface Serializer
{
    /**
     * @param Event $event
     *
     * @return string
     */
    public function serialize(Event $event);

    /**
     * @param string $content
     *
     * @return Event
     */
    public function unserialize($content);
}
