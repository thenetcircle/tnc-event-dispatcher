<?php

namespace TNC\EventDispatcher\EndPoints\Redis;

use TNC\EventDispatcher\WrappedEvent;

interface ChannelResolver
{
    /**
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     *
     * @return string
     */
    public function getChannel(WrappedEvent $wrappedEvent);
}