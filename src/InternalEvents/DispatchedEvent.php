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

class DispatchedEvent extends AbstractInternalEvent
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var object
     */
    protected $event;

    /**
     * @var string
     */
    protected $transportMode;

    /**
     * @param string $eventName
     * @param object $event
     * @param string $transportMode
     */
    public function __construct($eventName, $event, $transportMode)
    {
        $this->eventName = $eventName;
        $this->event = $event;
        $this->transportMode = $transportMode;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return object
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getTransportMode()
    {
        return $this->transportMode;
    }
}