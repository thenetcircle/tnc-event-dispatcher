<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\EndPoints\Redis;

use TNC\EventDispatcher\EndPoints\AbstractEndPoint;
use TNC\EventDispatcher\Event\Internal\DeliverySerializableEvent;
use TNC\EventDispatcher\WrappedEvent;
use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\Exception\TimeoutException;

class PHPRedisEndPoint extends AbstractEndPoint
{
    /**
     * @var \Redis
     */
    protected $redisManager = null;

    /**
     * @var \TNC\EventDispatcher\EndPoints\Redis\ChannelResolver
     */
    protected $channelResolver = null;

    /**
     * PHPRedisBackend constructor.
     *
     * @param \TNC\EventDispatcher\EndPoints\Redis\ChannelResolver $channelResolver
     * @param string $host can be a host, or the path to a unix domain
     * @param int $port
     * @param int $db
     * @param float $timeout value in seconds (optional, default is 0 meaning unlimited)
     * @param bool $persistent
     *
     * @throws \TNC\EventDispatcher\Exception\InitializeException
     */
    public function __construct(
        ChannelResolver $channelResolver,
        $host, $port = 6379,
        $db = null, $timeout = 0, $persistent = false
    ) {
        if (!class_exists('\Redis')) {
            throw new InitializeException(
                'Dependency missed, php-redis(https://github.com/phpredis/phpredis).'
            );
        }

        $redisManager = new \Redis();
        if ($persistent) {
            $connected = $redisManager->pconnect($host, $port, $timeout);
        }
        else {
            $connected = $redisManager->connect($host, $port, $timeout);
        }

        if (!$connected) {
            throw new InitializeException(
                'The redis server can not be reached.'
            );
        }

        if ($db !== null) {
            $redisManager->select($db);
        }

        $this->redisManager = $redisManager;
        $this->channelResolver = $channelResolver;
    }

    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $event
     *
     * @throws TimeoutException
     */
    public function send($message, WrappedEvent $event)
    {
        $channel = $this->channelResolver->getChannel($event);
        $received = $this->redisManager->publish($channel, $message);
        if ($received <= 0) {
            $this->dispatchInternalEvent(
                DeliverySerializableEvent::FAILED,
                new DeliverySerializableEvent($channel, $message, $key, 0)
            );
        }
    }
}
