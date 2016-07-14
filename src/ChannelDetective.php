<?php

namespace Tnc\Service\EventDispatcher;

interface ChannelDetective
{
    /**
     * @param \Tnc\Service\EventDispatcher\EventWrapper $eventWrapper
     *
     * @return mixed
     */
    public function getPushingChannel(EventWrapper $eventWrapper);

    /**
     * @return mixed
     */
    public function getDefaultPoppingChannel();
}