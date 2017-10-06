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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TNC\EventDispatcher\Exception\NoClassException;
use TNC\EventDispatcher\Interfaces\DispatcherInterface;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\Exception\InvalidArgumentException;

class Dispatcher extends EventDispatcher implements DispatcherInterface
{
    /**
     * @param \TNC\EventDispatcher\Serializer          $serializer
     * @param \TNC\EventDispatcher\Interfaces\EndPoint $endPoint
     * @param \Psr\Log\LoggerInterface|null            $logger
     */
    public function __construct(Serializer $serializer, EndPoint $endPoint, LoggerInterface $logger = null)
    {
        $this->serializer = $serializer;
        $this->endPoint   = $endPoint->withDispatcher($this);
        $this->logger     = $logger;
    }

    use DispatcherTrait;
}