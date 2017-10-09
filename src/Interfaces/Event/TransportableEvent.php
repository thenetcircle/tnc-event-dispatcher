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

namespace TNC\EventDispatcher\Interfaces\Event;

interface TransportableEvent
{
    CONST TRANSPORT_MODE_SYNC      = 'sync';
    CONST TRANSPORT_MODE_SYNC_PLUS = 'sync_plus';
    CONST TRANSPORT_MODE_ASYNC     = 'async';

    /**
     * Returns transport mode of this event
     *
     * It supports one of these:
     *  - "sync"      works as same as origin event, the event will be dispatched to listeners directly.
     *  - "sync_plus" after the event dispatched to local listeners, it will be sent to the EndPoint for other remote
     *                listeners as well, but it will not be dispatch again if the receiver got it.
     *  - "async"     the event will be sent to EndPoint only, and after receiver got it, will be dispatched to
     *                listeners.
     *
     * @see \TNC\EventDispatcher\Interfaces\Event\TransportableEvent
     *
     * @return string
     */
    public function getTransportMode();
}