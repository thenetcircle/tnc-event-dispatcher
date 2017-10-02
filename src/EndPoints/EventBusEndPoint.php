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

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\WrappedEvent;

class EventBusEndPoint extends AbstractEndPoint
{
    /**
     * @var \GuzzleHttp\Client|null
     */
    protected $client = null;

    /**
     * EventBusEndPoint constructor.
     *
     * @param string $uri
     * @param float  $timeout        Float describing the timeout of the request in seconds. Use 0 to wait indefinitely (the
     *                               default behavior).
     * @param array  $requestOptions @see http://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @throws \TNC\EventDispatcher\Exception\InitializeException
     */
    public function __construct($uri, $timeout = 3, array $requestOptions = [])
    {
        if (!class_exists('\GuzzleHttp\Client')) {
            throw new InitializeException(
              'Dependency not found, EventBusEndPoint depends on GuzzleHttp(http://docs.guzzlephp.org/en/stable/).'
            );
        }

        $config = array_merge($requestOptions, [
          'base_uri' => $uri,
          'timeout' => $timeout
        ]);

        $this->client = new \GuzzleHttp\Client($config);
    }

    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     */
    public function send($message, WrappedEvent $wrappedEvent)
    {
        $request = new Request('POST', '/', [], $message);
        $promise = $this->client->sendAsync($request);
        $promise->then(
          function (ResponseInterface $res) use ($message, $wrappedEvent) {
              $this->dispatchSuccessEvent($message, $wrappedEvent);
          },
          function (RequestException $e) use ($message, $wrappedEvent) {
              $this->dispatchFailureEvent($message, $wrappedEvent, $e);
          }
        );
    }
}