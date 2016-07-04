<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

interface Normalizer
{
    /**
     * @param Event $event
     *
     * @return array
     */
    public function normalize(Event $event);

    /**
     * @param array  $data
     *
     * @return Event
     *
     * @throws InvalidArgumentException
    */
    public function denormalize($data);
}
