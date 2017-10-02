<?php

namespace TNC\EventDispatcher\EndPoints;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use TNC\EventDispatcher\Event\Internal\TransportFailureEvent;
use TNC\EventDispatcher\Event\Internal\TransportSuccessEvent;
use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\Exception\TimeoutException;
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