<?php

namespace Tnc\Service\EventDispatcher\Interfaces;

use Tnc\Service\EventDispatcher\Event\EventWrapper;

interface ChannelDetective
{
    /**
     * @param \Tnc\Service\EventDispatcher\Event\EventWrapper $eventWrapper
     *
     * @return array
     */
    public function getPushingChannels(EventWrapper $eventWrapper);

    /**
     * @return array
     */
    public function getListeningChannels();
}