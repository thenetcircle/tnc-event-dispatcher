<?php

namespace TNC\Service\EventDispatcher\ChannelDetective;

use TNC\Service\EventDispatcher\Interfaces\ChannelDetective;
use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Event\EventWrapper;

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