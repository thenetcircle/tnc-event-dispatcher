<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Beineng Ma <baineng.ma@gmail.com>
 */

namespace TNC\EventDispatcher\EndPoints\Redis;

use TNC\EventDispatcher\EndPoints\AbstractEndPoint;
use TNC\EventDispatcher\WrappedEvent;
use TNC\EventDispatcher\Exceptions\InitializeException;

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
     * @throws \TNC\EventDispatcher\Exceptions\InitializeException
     */
    public function __construct(
        ChannelResolver $channelResolver,
        $host, $port = 6379,
        $db = null, $timeout = 0, $persistent = false
    ) {
        if (!class_exists('\Redis')) {
            throw new InitializeException(
                'Dependency not found, PHPRedisEndPoint depends on php-redis(https://github.com/phpredis/phpredis).'
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
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     */
    public function send($message, WrappedEvent $wrappedEvent)
    {
        $channel = $this->channelResolver->getChannel($wrappedEvent);
        $received = $this->redisManager->publish($channel, $message);
        if ($received <= 0) {
            $this->dispatchFailureEvent(
              $message,
              $wrappedEvent,
              new \Exception("No one received the message.")
            );
        }
        else {
            $this->dispatchSuccessEvent($message, $wrappedEvent);
        }
    }
}
