<?php

namespace Tnc\Service\EventDispatcher;

interface ChannelDetective
{
    /**
     * @param \Tnc\Service\EventDispatcher\Event $event
     *
     * @return array
     */
    public function getPushingChannels(Event $event);

    /**
     * @return array
     */
    public function getListeningChannels();
}