<?php

namespace TNC\EventDispatcher\EndPoints\Redis;

use TNC\EventDispatcher\WrappedEvent;

interface ChannelResolver
{
    /**
     * @param \TNC\EventDispatcher\WrappedEvent $event
     *
     * @return string
     */
    public function getChannel(WrappedEvent $event);
}