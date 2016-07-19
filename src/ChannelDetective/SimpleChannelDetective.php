<?php

namespace Tnc\Service\EventDispatcher\ChannelDetective;

use Tnc\Service\EventDispatcher\ChannelDetective;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\EventWrapper;

class SimpleChannelDetective implements ChannelDetective
{
    protected $channels = ['event-default', 'event-message'];
    protected $channelsMapping = [
            '^message\\.' => ['event-message'],
            '.*'          => ['event-default'],
        ];

    /**
     * {@inheritdoc}
     */
    public function getPushingChannels(EventWrapper $eventWrapper)
    {
        $eventName = $eventWrapper->getEvent()->getName();

        foreach ($this->channelsMapping as $_key => $_value) {
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
        return $this->channels;
    }
}