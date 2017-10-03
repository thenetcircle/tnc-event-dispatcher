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

use TNC\EventDispatcher\Interfaces\TransportableEvent;

/**
 * Class EventWrapper
 *
 * @package TNC\EventDispatcher
 */
class WrappedEvent
{
    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var \TNC\EventDispatcher\Interfaces\TransportableEvent
     */
    protected $event;

    /**
     * @var string
     */
    protected $transportMode;

    /**
     * @var string
     */
    protected $className;


    /**
     * @param string                                             $eventName
     * @param \TNC\EventDispatcher\Interfaces\TransportableEvent $event
     * @param string                                             $transportMode
     */
    public function __construct($eventName, TransportableEvent $event, $transportMode)
    {
        $this->eventName     = $eventName;
        $this->event         = $event;
        $this->transportMode = $transportMode;
        $this->className     = get_class($event);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\TransportableEvent
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

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
