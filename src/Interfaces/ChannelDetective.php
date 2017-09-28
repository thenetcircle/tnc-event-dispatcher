<?php

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Event\EventWrapper;

interface ChannelDetective
{
    /**
     * @param \TNC\EventDispatcher\Event\EventWrapper $eventWrapper
     *
     * @return array
     */
    public function getPushingChannels(EventWrapper $eventWrapper);

    /**
     * @return array
     */
    public function getListeningChannels();
}