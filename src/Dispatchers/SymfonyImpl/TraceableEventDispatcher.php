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

use Psr\Log\LoggerInterface;
use \Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher as BaseTraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use TNC\EventDispatcher\Interfaces\Dispatcher;

class TraceableEventDispatcher extends BaseTraceableEventDispatcher implements Dispatcher
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function __construct(EventDispatcherInterface $dispatcher, Stopwatch $stopwatch, LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $stopwatch, $logger);
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchSerializedEvent($serializedEvent)
    {
        $this->dispatcher->dispatchSerializedEvent($serializedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchInternalEvent($eventName, $event = null)
    {
        return $this->dispatcher->dispatchInternalEvent($eventName, $event);
    }
}