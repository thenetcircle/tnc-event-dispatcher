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

use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TNC\EventDispatcher\Interfaces\Dispatcher as TNCEventDispatcher;

class TraceableEventDispatcher implements TraceableEventDispatcherInterface, TNCEventDispatcher
{
    /**
     * @var TraceableEventDispatcherInterface
     */
    private $traceableEventDispatcher;

    /**
     * @var TNCEventDispatcher
     */
    private $tncEventDispatcher;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        TraceableEventDispatcherInterface $traceableEventDispatcher,
        TNCEventDispatcher $tncEventDispatcher
    ) {
        $this->traceableEventDispatcher = $traceableEventDispatcher;
        $this->tncEventDispatcher       = $tncEventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSerializedEvent($serializedEvent)
    {
        return $this->tncEventDispatcher->dispatchSerializedEvent($serializedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchInternalEvent($eventName, $event = null)
    {
        return $this->tncEventDispatcher->dispatchInternalEvent($eventName, $event);
    }

    public function dispatch($eventName, Event $event = null)
    {
        return $this->traceableEventDispatcher->dispatch($eventName, $event);
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        return $this->traceableEventDispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        return $this->traceableEventDispatcher->addSubscriber($subscriber);
    }

    public function removeListener($eventName, $listener)
    {
        return $this->traceableEventDispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        return $this->traceableEventDispatcher->removeSubscriber($subscriber);
    }

    public function getListeners($eventName = null)
    {
        return $this->traceableEventDispatcher->getListeners($eventName);
    }

    public function getListenerPriority($eventName, $listener)
    {
        return $this->traceableEventDispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners($eventName = null)
    {
        return $this->traceableEventDispatcher->hasListeners($eventName);
    }

    public function getCalledListeners()
    {
        return $this->traceableEventDispatcher->getCalledListeners();
    }

    public function getNotCalledListeners()
    {
        return $this->traceableEventDispatcher->getNotCalledListeners();
    }

    public function reset()
    {
        return $this->traceableEventDispatcher->reset();
    }
}