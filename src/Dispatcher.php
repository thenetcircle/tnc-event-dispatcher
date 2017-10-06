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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TNC\EventDispatcher\Exception\NoClassException;
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
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger = null;

    /**
     * @param \TNC\EventDispatcher\Serializer          $serializer
     * @param \TNC\EventDispatcher\Interfaces\EndPoint $endPoint
     * @param \Psr\Log\LoggerInterface|null            $logger
     */
    public function __construct(Serializer $serializer, EndPoint $endPoint, LoggerInterface $logger = null)
    {
        $this->serializer = $serializer;
        $this->endPoint   = $endPoint->withDispatcher($this);
        $this->logger     = $logger;
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
                    $this->sendToEndPoint($eventName, $event);
                    return $event;

                case TransportableEvent::TRANSPORT_MODE_SYNC_PLUS:
                    $this->sendToEndPoint($eventName, $event);
                    return parent::dispatch($eventName, $event);

                default:
                    throw new InvalidArgumentException('Unsupported transport mode.');

            }

        } else {
            return parent::dispatch($eventName, $event);
        }
    }

    /**
     * Dispatches a async event
     *
     * @param string $message
     */
    public function dispatchMessage($message)
    {
        /** @var WrappedEvent $metadata */
        $metadata = $this->serializer->unserialize($message, WrappedEvent::class);

        if ($metadata->getTransportMode() == TransportableEvent::TRANSPORT_MODE_ASYNC) {

            $eventName = $metadata->getEventName();
            $className = $metadata->getClassName();

            if ($listeners = $this->getListeners($eventName)) {

                if (empty($className)) {
                    $className = $this->extractClassNameFromListeners($listeners);
                    if (empty($className)) {
                        throw new NoClassException(sprintf(
                          "Can not figure out classname of Event %s ",
                          $eventName
                        ));
                    }
                }

                $event = $this->serializer->denormalize($metadata->getNormalizedEvent(), $className);

                $this->doDispatch($listeners, $eventName, $event);
            }

        }
    }

    /**
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array()) {
        if (!is_null($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Transports a TransportableEvent to the EndPoint
     *
     * @param string                                             $eventName
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     */
    protected function sendToEndPoint($eventName, TransportableEvent $event)
    {
        $normalizedEvent = $this->serializer->normalize($event);
        $wrappedEvent = new WrappedEvent(
          $event->getTransportMode(),
          $eventName,
          $normalizedEvent,
          get_class($event)
        );

        $message = $this->serializer->serialize($wrappedEvent);
        $this->endPoint->send($message, $wrappedEvent);
    }

    /**
     * Extracts classname from Event Listeners
     *
     * @param array $listeners
     *
     * @return string
     */
    protected function extractClassNameFromListeners(array $listeners) {
        $className = '';
        $reservedClassName = '';

        // Going to extract Event classname from type hint of Listeners
        foreach ($listeners as $listener) {
            try {
                $rfParameter  = new \ReflectionParameter($listener, 0);
                $rfEventClass = $rfParameter->getClass();
                if ($rfEventClass->implementsInterface(TransportableEvent::class)) {
                    $className = $rfEventClass->getName();
                    break;
                }
                elseif ($rfEventClass->isSubclassOf(Event::class)) {
                    $reservedClassName = $rfEventClass->getName();
                }
            } catch (\ReflectionException $e) {}
        }
        if (empty($className) && !empty($reservedClassName)) {
            $className = $reservedClassName;
        }

        return $className;
    }
}