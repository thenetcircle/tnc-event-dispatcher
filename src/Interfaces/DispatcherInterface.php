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

namespace TNC\EventDispatcher\Interfaces;

use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\Exception\InvalidArgumentException;

interface DispatcherInterface
{
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
    public function dispatch($eventName, Event $event = null);

    /**
     * Dispatches a async event
     *
     * @param string $message
     */
    public function dispatchMessage($message);

    /**
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array());
}