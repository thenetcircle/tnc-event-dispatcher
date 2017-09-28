<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Backend\RedisPubsub;

use TNC\EventDispatcher\Backend\AbstractBackend;
use TNC\EventDispatcher\Event\Internal\DeliveryEvent;
use TNC\EventDispatcher\Exception;

class PHPRedisBackend extends AbstractBackend
{
    /**
     * @var \Redis
     */
    protected $redisManager = null;

    /**
     * PHPRedisBackend constructor.
     *
     * @param string $host can be a host, or the path to a unix domain
     * @param int $port
     * @param int $db
     * @param bool $persistent
     * @param float $timeout value in seconds (optional, default is 0 meaning unlimited)
     *
     * @throws \TNC\EventDispatcher\Exception\FatalException
     */
    public function __construct(
        $host,
        $port = 6379,
        $db = null,
        $timeout = 0,
        $persistent = false
    ) {
        if (!class_exists('\Redis')) {
            throw new Exception\FatalException(
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
            throw new Exception\FatalException(
                'The redis server can not be reached.'
            );
        }

        if ($db !== null) {
            $redisManager->select($db);
        }

        $this->redisManager = $redisManager;
    }

    /**
     * Push a message to the specific channel of a pipeline
     *
     * @param array       $channels
     * @param string      $message
     * @param string|null $key
     *
     * @throws Exception\FatalException
     * @throws Exception\TimeoutException
     */
    public function push($channels, $message, $key = null)
    {
        foreach($channels as $_channel) {
            $received = $this->redisManager->publish($_channel, $message);
            if ($received <= 0) {
                $this->dispatchInternalEvent(
                    DeliveryEvent::FAILED,
                    new DeliveryEvent($_channel, $message, $key, 0)
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pop($channels, $timeout)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function ack($receipt)
    {
    }
}
