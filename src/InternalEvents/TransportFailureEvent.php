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

namespace TNC\EventDispatcher\Event\InternalEvents;

use Symfony\Component\EventDispatcher\Event;
use TNC\EventDispatcher\WrappedEvent;

class TransportFailureEvent extends Event
{
    const NAME = 'event-dispatcher.transport.failure';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var WrappedEvent
     */
    protected $wrappedEvent;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * TransportSuccessEvent constructor.
     *
     * @param string                            $message
     * @param \TNC\EventDispatcher\WrappedEvent $wrappedEvent
     * @param \Exception                        $exception
     */
    public function __construct($message, WrappedEvent $wrappedEvent, \Exception $exception)
    {
        $this->message = $message;
        $this->wrappedEvent = $wrappedEvent;
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \TNC\EventDispatcher\WrappedEvent
     */
    public function getWrappedEvent()
    {
        return $this->wrappedEvent;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}