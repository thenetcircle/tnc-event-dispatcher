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

namespace TNC\EventDispatcher\InternalEvents;

final class InternalEvents
{
    /**
     * occurs when sending the Event to the EndPoint succeeded.
     *
     * @Event("TNC\EventDispatcher\InternalEvents\SendingSucceededEvent")
     *
     * @var string
     */
    const SENDING_SUCCEEDED = 'tnc-event-dispatcher.sending_succeeded';

    /**
     * occurs when sending the Event to the EndPoint failed.
     *
     * @Event("TNC\EventDispatcher\InternalEvents\SendingFailedEvent")
     *
     * @var string
     */
    const SENDING_FAILED = 'tnc-event-dispatcher.sending_failed';

    /**
     * occurs when receives a new Event from EndPoint
     *
     * @Event("TNC\EventDispatcher\InternalEvents\ReceivedEvent")
     *
     * @var string
     */
    const RECEIVED = 'tnc-event-dispatcher.received';

    /**
     * occurs when receiver dispatching a Event failed
     *
     * @Event("TNC\EventDispatcher\InternalEvents\ReceiverDispatchingFailedEvent")
     *
     * @var string
     */
    const RECEIVER_DISPATCHING_FAILED = 'tnc-event-dispatcher.receiver_dispatching_failed';

    /**
     * occurs before dispatching to listeners
     *
     * @Event("TNC\EventDispatcher\InternalEvents\DispatchingEvent")
     *
     * @var string
     */
    const DISPATCHING = 'tnc-event-dispatcher.dispatching';

    /**
     * occurs after dispatched to listeners
     *
     * @Event("TNC\EventDispatcher\InternalEvents\DispatchedEvent")
     *
     * @var string
     */
    const DISPATCHED = 'tnc-event-dispatcher.dispatched';
}