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

namespace TNC\EventDispatcher\Receivers;

final class EventBusSignal
{
    /**
     * @var string tells EventBus this event was properly processed.
     */
    const OK = 'ok';

    /**
     * @var string tells EventBus to use a exponential backoff strategy to retry the event.
     *             https://en.wikipedia.org/wiki/Exponential_backoff
     */
    const EXPONENTIAL_BACKOFF = 'exponential_backoff';

    /**
     * @var string tells EventBus to save this event in to fallback storage
     */
    const TO_FALLBACK = 'to_fallback';


    // all other response will be consider failure
}