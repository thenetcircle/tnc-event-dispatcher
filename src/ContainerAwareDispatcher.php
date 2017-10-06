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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use TNC\EventDispatcher\Interfaces\DispatcherInterface;
use TNC\EventDispatcher\Interfaces\EndPoint;

class ContainerAwareDispatcher extends ContainerAwareEventDispatcher implements DispatcherInterface
{
    public function __construct(
      ContainerInterface $container,
      Serializer $serializer,
      EndPoint $endPoint,
      LoggerInterface $logger = null
    ) {
        parent::__construct($container);
        $this->serializer = $serializer;
        $this->endPoint   = $endPoint->withDispatcher($this);
        $this->logger     = $logger;
    }

    use DispatcherTrait;
}