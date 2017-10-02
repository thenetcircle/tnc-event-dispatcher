<?php

namespace TNC\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Exception\TimeoutException;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\Exception\InvalidArgumentException;

class Dispatcher extends EventDispatcher
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var EndPoint
     */
    private $endPoint;

    /**
     * @param \TNC\EventDispatcher\Serializer          $serializer
     * @param \TNC\EventDispatcher\Interfaces\EndPoint $endPoint
     */
    public function __construct(Serializer $serializer, EndPoint $endPoint)
    {
        $this->serializer = $serializer;
        $this->endPoint = $endPoint;
        $this->endPoint->setDispatcher($this);
    }

    /**
     * Dispatches an event to all listeners.
     * If it's a TransportableEvent and with non SYNC transport mode,
     * It will be send to predefined EndPoint.
     *
     * @param string $eventName The name of the event to dispatch. The name of
     *                          the event is the name of the method that is
     *                          invoked on listeners.
     * @param Event  $event     The event to pass to the event handlers/listeners
     *                          If not supplied, an empty Event instance is created.
     *
     *
     * @return Event
     *
     * @throws InvalidArgumentException
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (
            $event !== null &&
            ($event instanceof TransportableEvent) &&
            $event->getTransportMode() !== TransportableEvent::TRANSPORT_MODE_SYNC
        ) {

            switch ($event->getTransportMode()) {

                case TransportableEvent::TRANSPORT_MODE_ASYNC:
                    $this->doAsyncDispatch($eventName, $event);
                    return $event;

                case TransportableEvent::TRANSPORT_MODE_SYNC_PLUS:
                    $this->doAsyncDispatch($eventName, $event);
                    return parent::dispatch($eventName, $event);

                default:
                    throw new InvalidArgumentException('Unsupported transport mode.');

            }

        }
        else {
            return parent::dispatch($eventName, $event);
        }
    }

    /**
     * Transports a TransportableEvent to the EndPoint
     *
     * @param string                                             $eventName
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     */
    public function doAsyncDispatch($eventName, TransportableEvent $event)
    {
        $wrappedEvent = new WrappedEvent($eventName, $event, $event->getTransportMode());
        try {
            $message = $this->serializer->serialize($wrappedEvent);
            $this->endPoint->send($message, $wrappedEvent);
        }
        // TODO: spearate serializer exception and endpoint exception
        catch (NormalizeException $e) {

        }
        catch (TimeoutException $e) {

        }
    }
}