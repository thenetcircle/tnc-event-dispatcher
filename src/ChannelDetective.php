<?php

namespace Tnc\Service\EventDispatcher;

interface ChannelDetective
{
    /**
     * @param \Tnc\Service\EventDispatcher\EventWrapper $eventWrapper
     *
     * @return array
     */
    public function getPushingChannels(EventWrapper $eventWrapper);

    /**
     * @return array
     */
    public function getListeningChannels();
}