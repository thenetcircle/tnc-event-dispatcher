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
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\Exception\SendingException;
use TNC\EventDispatcher\WrappedEvent;

class EventBusEndPoint extends AbstractEndPoint
{
    const EXPECTED_RESPONSE = 'ok';

    /**
     * @var \GuzzleHttp\Client|null
     */
    protected $client = null;

    /**
     * @var CurlMultiHandler|null
     */
    protected $asyncHandler = null;

    /**
     * @var \GuzzleHttp\Promise\Promise[]
     */
    protected $flyingRequests = [];

    /**
     * @var callable|null
     */
    protected $fallback = null;

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * EventBusEndPoint constructor.
     *
     * @param string   $uri
     * @param callable $fallback       a fallback function which takes
     *                                 (string $message, \TNC\EventDispatcher\WrappedEvent $wrappedEvent, \Exception $e)
     *                                 as parameters, and returns boolean as result
     * @param float    $timeout        Float describing the timeout of the request in seconds. Use 0 to wait
     *                                 indefinitely (the default behavior).
     * @param array    $requestOptions @see http://docs.guzzlephp.org/en/stable/request-options.html
     *
     * @throws \TNC\EventDispatcher\Exception\InitializeException
     */
    public function __construct($uri, callable $fallback = null, $timeout = 3, array $requestOptions = [])
    {
        if (!class_exists('\GuzzleHttp\Client')) {
            throw new InitializeException('Dependency not found, EventBusEndPoint depends on GuzzleHttp(http://docs.guzzlephp.org/en/stable/).');
        }

        $this->uri = $uri;

        $config = array_merge($requestOptions, [
          'timeout'  => $timeout
        ]);

        # if curl_multi_exec function existed, just use it for async requests
        if (function_exists('curl_multi_exec')) {
            $handler = $this->asyncHandler = new CurlMultiHandler();

            if (function_exists('curl_exec')) {
                $handler = Proxy::wrapSync($this->asyncHandler, new CurlHandler());
            }

            $config['handler'] = HandlerStack::create($handler);
        }

        $this->client = new \GuzzleHttp\Client($config);

        register_shutdown_function([$this, 'waitingFlyingRequests']);

        $this->fallback = $fallback;
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
        $request = new Request('POST', $this->uri, [], $message);

        $promise = $this->client->sendAsync($request);

        if (null !== $this->asyncHandler) {
            $this->asyncHandler->tick();
        }

        array_push($this->flyingRequests, [$promise, $message, $wrappedEvent]);

        return $promise;
    }

    /**
     * Waiting all flying requests complete
     */
    public function waitingFlyingRequests()
    {
        foreach ($this->flyingRequests as $requestData) {
            list($promise, $message, $wrappedEvent) = $requestData;
            try {
                $response = $promise->wait();
                if ($response->getBody()->getContents() != self::EXPECTED_RESPONSE) {
                    throw new SendingException('The response %s is not expected', $response->getBody()->getContents());
                }
                $this->dispatchSuccessEvent($message, $wrappedEvent);
            } catch (\Exception $exception) {
                if (null !== $this->fallback) {
                    try {
                        $result = call_user_func($this->fallback, $message, $wrappedEvent, $exception);
                        if ($result) {
                            $this->dispatchSuccessEvent($message, $wrappedEvent);
                        } else {
                            throw new \Exception(sprintf('Get failed result %s from fallback.', $result), 0, $exception);
                        }
                    } catch (\Exception $fallbackException) {
                        $this->dispatchFailureEvent($message, $wrappedEvent, $fallbackException);
                    }
                } else {
                    $this->dispatchFailureEvent($message, $wrappedEvent, $exception);
                }
            }
        }
    }
}