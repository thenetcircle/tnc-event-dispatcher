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

namespace TNC\EventDispatcher\Dispatchers\SymfonyImpl;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Exception\NoClassException;
use TNC\EventDispatcher\Interfaces\Dispatcher;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\InternalEvents\AbstractInternalEvent;
use TNC\EventDispatcher\InternalEvents\DispatchedEvent;
use TNC\EventDispatcher\InternalEvents\DispatchingEvent;
use TNC\EventDispatcher\InternalEvents\InternalEvents;
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\WrappedEvent;

class EventDispatcher extends SymfonyEventDispatcher implements Dispatcher
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var EndPoint
     */
    protected $endPoint;

    /**
     * @param \TNC\EventDispatcher\Serializer          $serializer
     * @param \TNC\EventDispatcher\Interfaces\EndPoint $endPoint
     */
    public function __construct(Serializer $serializer, EndPoint $endPoint)
    {
        parent::__construct();

        $this->serializer = $serializer;
        $this->endPoint   = $endPoint->withDispatcher($this);
    }

    /**
     * {@inheritdoc}
     *
     * @param string|null $eventName
     */
    public function dispatch($event/*, string $eventName = null*/)
    {
        // ---
        // this part is for compatible with Symfony 4.3+ EventDispatcher implementation,
        // may be removed in future version of Symfony
        // --
        $eventName = 1 < \func_num_args() ? \func_get_arg(1) : null;

        if (\is_object($event)) {
            $eventName = $eventName ?? \get_class($event);
        } else {
            @trigger_error(sprintf('Calling the "%s::dispatch()" method with the event name as first argument is deprecated since Symfony 4.3, pass it second and provide the event object first instead.', EventDispatcherInterface::class), E_USER_DEPRECATED);
            $swap = $event;
            $event = $eventName ?? new Event();
            $eventName = $swap;

            if (!$event instanceof Event) {
                throw new \TypeError(sprintf('Argument 1 passed to "%s::dispatch()" must be an instance of %s, %s given.', EventDispatcherInterface::class, Event::class, \is_object($event) ? \get_class($event) : \gettype($event)));
            }
        }
        // --- end --

        $transportMode = ($event instanceof TransportableEvent) ?
            $event->getTransportMode() : TransportableEvent::TRANSPORT_MODE_SYNC;

        if ($transportMode !== TransportableEvent::TRANSPORT_MODE_SYNC) {
            switch ($transportMode) {
                case TransportableEvent::TRANSPORT_MODE_ASYNC:
                    $this->sendToEndPoint($eventName, $event);

                    return $event;

                case TransportableEvent::TRANSPORT_MODE_SYNC_PLUS:
                case TransportableEvent::TRANSPORT_MODE_BOTH:
                    $this->preDispatch($eventName, $event, $transportMode);
                    $event = parent::dispatch($event, $eventName);
                    $this->postDispatch($eventName, $event, $transportMode);

                    $this->sendToEndPoint($eventName, $event);

                    return $event;

                default:
                    throw new InvalidArgumentException('Unsupported transport mode.');
            }
        }
        elseif ($event instanceof AbstractInternalEvent) {
            return parent::dispatch($event, $eventName);
        }
        else {
            $this->preDispatch($eventName, $event, $transportMode);
            $event = parent::dispatch($event, $eventName);
            $this->postDispatch($eventName, $event, $transportMode);

            return $event;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSerializedEvent($serializedEvent)
    {
        /** @var WrappedEvent $wrappedEvent */
        $wrappedEvent  = $this->serializer->unserialize($serializedEvent, WrappedEvent::class);
        $transportMode = $wrappedEvent->getTransportMode();

        if (in_array(
            $transportMode,
            [TransportableEvent::TRANSPORT_MODE_ASYNC, TransportableEvent::TRANSPORT_MODE_BOTH]
        )) {
            $eventName = $wrappedEvent->getEventName();
            if ($transportMode == TransportableEvent::TRANSPORT_MODE_BOTH) { // append suffix for both mode
                $eventName .= '.async';
            }
            $className = $wrappedEvent->getClassName();
            $listeners = $this->getListeners($eventName);

            if ($listeners) {
                if (empty($className)) {
                    $className = $this->extractClassNameFromListeners($listeners);
                    if (empty($className)) {
                        throw new NoClassException(
                            sprintf(
                                "Can not figure out classname of Event %s ",
                                $eventName
                            )
                        );
                    }
                }

                $event = $this->serializer->denormalize($wrappedEvent->getNormalizedEvent(), $className);

                $this->preDispatch($eventName, $event, $transportMode);
                $this->callListeners($listeners, $eventName, $event);
                $this->postDispatch($eventName, $event, $transportMode);

                return $event;
            }
        }

        return null;
    }

    /**
     * @return \TNC\EventDispatcher\Serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\EndPoint
     */
    public function getEndPoint()
    {
        return $this->endPoint;
    }

    /**
     * Transports a TransportableEvent to the EndPoint
     *
     * @param string                                                   $eventName
     * @param \TNC\EventDispatcher\Interfaces\Event\TransportableEvent $event
     */
    protected function sendToEndPoint($eventName, TransportableEvent $event)
    {
        $normalizedEvent = $this->serializer->normalize($event);
        $wrappedEvent    = new WrappedEvent(
            $event->getTransportMode(), $eventName, $normalizedEvent, get_class($event)
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
    protected function extractClassNameFromListeners(array $listeners)
    {
        $className         = '';
        $reservedClassName = '';

        // Going to extract Event classname from type hint of Listeners
        foreach ($listeners as $listener) {
            try {
                $rfParameter  = new \ReflectionParameter($listener, 0);
                $rfEventClass = $rfParameter->getClass();
                if ($rfEventClass->implementsInterface(TransportableEvent::class)) {
                    $className = $rfEventClass->getName();
                    break;
                } elseif ($rfEventClass->isSubclassOf(Event::class)) {
                    $reservedClassName = $rfEventClass->getName();
                }
            } catch (\ReflectionException $e) {
            }
        }
        if (empty($className) && !empty($reservedClassName)) {
            $className = $reservedClassName;
        }

        return $className;
    }

    /**
     * execs actions before dispatch events
     */
    protected function preDispatch($eventName, $event, $transportMode)
    {
        $this->dispatch(
            new DispatchingEvent($eventName, $event, $transportMode),
            InternalEvents::DISPATCHING
        );
    }

    /**
     * execs actions after dispatch events
     */
    protected function postDispatch($eventName, $event, $transportMode)
    {
        $this->dispatch(
            new DispatchedEvent($eventName, $event, $transportMode),
            InternalEvents::DISPATCHED
        );
    }
}