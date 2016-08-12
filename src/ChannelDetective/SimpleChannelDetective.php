<?php

namespace Tnc\Service\EventDispatcher\ChannelDetective;

use Tnc\Service\EventDispatcher\ChannelDetective;
use Tnc\Service\EventDispatcher\Event;

class SimpleChannelDetective implements ChannelDetective
{
    protected $listeningChannels = ['event-default', 'event-message'];
    protected $pushingChannelsMapping = [
            '^message\\.' => ['event-message'],
            '.*'          => ['event-default'],
        ];

    /**
     * {@inheritdoc}
     */
    public function getPushingChannels(Event $event)
    {
        $eventName = $event->getName();

        foreach ($this->pushingChannelsMapping as $_key => $_value) {
            if (preg_match('/'.$_key.'/i', $eventName)) {
                return $_value;
            }
        }

        return ['event-default'];
    }

    /**
     * {@inheritdoc}
     */
    public function getListeningChannels()
    {
        return $this->listeningChannels;
    }
}