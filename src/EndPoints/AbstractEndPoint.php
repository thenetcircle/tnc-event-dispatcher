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

use TNC\EventDispatcher\Event\InternalEvents\TransportFailureEvent;
use TNC\EventDispatcher\Event\InternalEvents\TransportSuccessEvent;
use TNC\EventDispatcher\Interfaces\DispatcherInterface;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\WrappedEvent;

abstract class AbstractEndPoint implements EndPoint
{
    /**
     * @var \TNC\EventDispatcher\Interfaces\DispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * {@inheritdoc}
     */
    public function withDispatcher(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    protected function dispatchSuccessEvent($message, WrappedEvent $wrappedEvent) {
        $this->dispatcher->dispatch(
          TransportSuccessEvent::NAME,
          new TransportSuccessEvent($message, $wrappedEvent)
        );
    }

    protected function dispatchFailureEvent($message, WrappedEvent $wrappedEvent, \Exception $e) {
        $this->dispatcher->dispatch(
          TransportFailureEvent::NAME,
          new TransportFailureEvent($message, $wrappedEvent, $e)
        );
    }
}
