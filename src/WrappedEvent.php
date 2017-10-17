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

final class WrappedEvent
{
    /**
     * @var string
     */
    protected $transportMode;

    /**
     * @var string
     */
    protected $eventName;

    /**
     * @var array
     */
    protected $normalizedEvent;

    /**
     * @var string
     */
    protected $className;


    /**
     * @param string $transportMode
     * @param string $eventName
     * @param array  $normalizedEvent
     * @param string $className
     */
    public function __construct($transportMode, $eventName, $normalizedEvent, $className)
    {
        $this->transportMode   = $transportMode;
        $this->eventName       = $eventName;
        $this->normalizedEvent = $normalizedEvent;
        $this->className       = $className;
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
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return array
     */
    public function getNormalizedEvent()
    {
        return $this->normalizedEvent;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
