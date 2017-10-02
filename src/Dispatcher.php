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

namespace TNC\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TNC\EventDispatcher\Exception\ConflictedEventTypeException;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Serialization\Normalizers\EventDispatcherNormalizer;

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
     * TransportableEvents are being listened
     *
     * @var array
     */
    private $listeningTransportableEvents = [];

    /**
     * @param \TNC\EventDispatcher\Serializer          $serializer
     * @param \TNC\EventDispatcher\Interfaces\EndPoint $endPoint
     */
    public function __construct(Serializer $serializer, EndPoint $endPoint)
    {
        $this->serializer = $serializer;
        $this->serializer->prependNormalizer(new EventDispatcherNormalizer($this));
        $this->endPoint = $endPoint;
        $this->endPoint->setDispatcher($this);
    }

    /**
     * Dispatches an event to all listeners.
     * A TransportableEvent with non SYNC transport mode will be send to predefined EndPoint.
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
        if ($event !== null && ($event instanceof TransportableEvent) && $event->getTransportMode() !== TransportableEvent::TRANSPORT_MODE_SYNC) {

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

        } else {
            return parent::dispatch($eventName, $event);
        }
    }

    /**
     * Dispatches a async received message
     *
     * @param string $message
     */
    public function dispatchMessage($message)
    {
        /** @var WrappedEvent $wrappedEvent */
        $wrappedEvent = $this->serializer->denormalize($message, WrappedEvent::class);
        if ($wrappedEvent->getTransportMode() == TransportableEvent::TRANSPORT_MODE_ASYNC) {
            $this->dispatch($wrappedEvent->getEventName(), $wrappedEvent->getEvent());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        try {
            // Only supports that one event name matching one kind of event
            $rfParameter  = new \ReflectionParameter($listener, 0);
            $rfEventClass = $rfParameter->getClass();
            if ($rfEventClass->implementsInterface(TransportableEvent::class)) {
                if (isset($this->listeningTransportableEvents[$eventName])) {
                    if ($rfEventClass->getName() != $this->listeningTransportableEvents[$eventName]) {
                        throw new ConflictedEventTypeException(sprintf('Event %s has been listened by other listeners with type %s, Can not be defined with type %s again.',
                          $eventName, $this->listeningTransportableEvents[$eventName], $rfEventClass->getName()));
                    }
                } else {
                    $this->listeningTransportableEvents[$eventName] = $rfEventClass->getName();
                }
            }
        } catch (\ReflectionException $e) {
            // TODO: record log
        }

        parent::addListener($eventName, $listener, $priority);
    }

    /**
     * Gets class name of the event $eventName
     *
     * @param $eventName
     *
     * @return string|null event class name
     */
    public function getTransportableEventClassName($eventName)
    {
        return isset($this->listeningTransportableEvents[$eventName]) ? $this->listeningTransportableEvents[$eventName] : null;
    }

    /**
     * Transports a TransportableEvent to the EndPoint
     *
     * @param string                                             $eventName
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     */
    protected function doAsyncDispatch($eventName, TransportableEvent $event)
    {
        $wrappedEvent = new WrappedEvent($eventName, $event, $event->getTransportMode());
        $message      = $this->serializer->serialize($wrappedEvent);
        $this->endPoint->send($message, $wrappedEvent);
    }
}