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

namespace TNC\EventDispatcher\EndPoints;

use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\WrappedEvent;

class EventBusEndPoint extends AbstractEndPoint
{
    /**
     * @var \GuzzleHttp\Client|null
     */
    protected $client = null;

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * @var int
     */
    protected $concurrency = 5;

    /**
     * @var array
     */
    protected $waitingMessages = [];

    /**
     * @var int
     */
    protected $maxWaiting = 1000;

    /**
     * EventBusEndPoint constructor.
     *
     * @param string $uri
     * @param float  $timeout          Float describing the timeout of the request in seconds. Use 0 to wait
     *                                 indefinitely.
     * @param array  $requestOptions   @see http://docs.guzzlephp.org/en/stable/request-options.html
     * @param int    $concurrency
     *
     * @throws \TNC\EventDispatcher\Exception\InitializeException
     */
    public function __construct(
        $uri,
        $timeout = 5,
        array $requestOptions = [],
        $concurrency = 5
    ) {
        if (!class_exists('\GuzzleHttp\Client')) {
            throw new InitializeException(
                'Dependency not found, EventBusEndPoint depends on GuzzleHttp(http://docs.guzzlephp.org/en/stable/).'
            );
        }

        $this->uri         = $uri;
        $this->concurrency = $concurrency;

        $config       = array_merge(
            $requestOptions,
            [
                'timeout' => $timeout,
            ]
        );
        $this->client = new \GuzzleHttp\Client($config);

        register_shutdown_function([$this, 'sendWaitingMessages']);
    }

    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function send($message, WrappedEvent $wrappedEvent)
    {
        array_push($this->waitingMessages, [$message, $wrappedEvent]);

        if (count($this->waitingMessages) >= $this->maxWaiting) {
            $this->sendWaitingMessages();
        }
    }

    /**
     * Send all waiting messages
     */
    public function sendWaitingMessages()
    {
        if (!is_array($this->waitingMessages) || count($this->waitingMessages) === 0) {
            return;
        }

        try {
            $uri             = $this->uri;
            $waitingMessages = $this->waitingMessages;

            $requests = array_map(
                function ($item) use ($uri) {
                    return new Request('POST', $uri, [], $item[0]);
                },
                $waitingMessages
            );

            $pool = new Pool(
                $this->client, $requests, [
                    'concurrency' => $this->concurrency,
                    'fulfilled'   => function ($response, $index) use ($waitingMessages) {
                        list($message, $wrappedEvent) = $waitingMessages[$index];
                        $this->dispatchSuccessEvent($message, $wrappedEvent);
                    },
                    'rejected'    => function ($reason, $index) use ($waitingMessages) {
                        list($message, $wrappedEvent) = $waitingMessages[$index];
                        $this->dispatchFailureEvent($message, $wrappedEvent, $reason);
                    },
                ]
            );

            $promise = $pool->promise();
            $promise->wait();
        }
        finally {
            $this->waitingMessages = [];
        }
    }
}