<?php

namespace TNC\Service\EventDispatcher\Interfaces;

use TNC\Service\EventDispatcher\Event\EventWrapper;

interface ChannelDetective
{
    /**
     * @param \TNC\Service\EventDispatcher\Event\EventWrapper $eventWrapper
     *
     * @return array
     */
    public function getPushingChannels(EventWrapper $eventWrapper);

    /**
     * @return array
     */
    public function getListeningChannels();
}