<?php

namespace Tnc\Service\EventDispatcher\ChannelDetective;

use Tnc\Service\EventDispatcher\ChannelDetective;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\EventWrapper;

class SimpleChannelDetective implements ChannelDetective
{
    CONST CHANNEL_PREFIX = 'event-';

    /**
     * {@inheritdoc}
     */
    public function getPushingChannel(EventWrapper $eventWrapper)
    {
        $eventName = $eventWrapper->getEvent()->getName();

        if (($pos = strpos($eventName, '.')) !== false) {
            $channel = substr($eventName, 0, $pos);
        } else {
            $channel = $eventName;
        }

        return self::CHANNEL_PREFIX . $channel;
    }

    /**
     * @return mixed
     */
    public function getDefaultPoppingChannel()
    {
        return '^' . self::CHANNEL_PREFIX . '*';
    }
}