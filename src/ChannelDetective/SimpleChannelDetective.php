<?php

namespace TNC\EventDispatcher\ChannelDetective;

use TNC\EventDispatcher\Interfaces\ChannelDetective;
use TNC\EventDispatcher\Interfaces\Event;
use TNC\EventDispatcher\Event\EventWrapper;

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
    public function getPushingChannels(EventWrapper $eventWrapper)
    {
        $eventName = $eventWrapper->getName();

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