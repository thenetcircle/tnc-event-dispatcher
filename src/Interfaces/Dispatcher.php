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

interface Dispatcher
{
    /**
     * Dispatches an event to all registered local or remote listeners.
     *
     * A [\TNC\EventDispatcher\Interfaces\Event\TransportableEvent] with non SYNC transport mode will be send to
     * predefined remote endpoint.
     *
     * @param object      $event     The event to pass to the event handlers/listeners.
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
         *                           the class of $event should be used instead.
     *
     * @return object The passed $event MUST be returned
     *
     * @throws \TNC\EventDispatcher\Exception\InvalidArgumentException
     */
    public function dispatch($event/*, string $eventName = null*/);

    /**
     * Dispatches a event which has been serialized already, Usually it comes from a Receiver
     *
     * @param string $serializedEvent
     *
     * @return \TNC\EventDispatcher\Interfaces\Event\TransportableEvent|null
     */
    public function dispatchSerializedEvent($serializedEvent);
}