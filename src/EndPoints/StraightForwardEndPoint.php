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

namespace TNC\EventDispatcher\EndPoints;

use TNC\EventDispatcher\Exception\InitializeException;
use TNC\EventDispatcher\WrappedEvent;

class StraightForwardEndPoint extends AbstractEndPoint
{
    /**
     * Sends a new message
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     */
    public function send($message, WrappedEvent $wrappedEvent)
    {
        if (null === $this->dispatcher) {
            throw new InitializeException('StraightForwardEndPoint needs Dispatcher.');
        }

        try {
            $this->dispatcher->dispatchSerializedEvent($message);
            $this->dispatchSuccessEvent($message, $wrappedEvent);
        }
        catch (\Exception $e) {
            $this->dispatchFailureEvent($message, $wrappedEvent, $e);
        }
    }
}