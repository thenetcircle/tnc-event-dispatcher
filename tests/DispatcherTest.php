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

namespace TNC\EventDispatcher\Tests;

use Symfony\Component\EventDispatcher\Tests\AbstractEventDispatcherTest;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;
use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\WrappedEvent;

class DispatcherTest extends AbstractEventDispatcherTest
{
    protected function createEventDispatcher()
    {
        $serializer = new Serializer(
          [new TNCActivityStreamsNormalizer()],
          new JsonFormatter()
        );

        return new Dispatcher($serializer, new EndPointMock());
    }
}

class EndPointMock implements EndPoint {
    /**
     * {@inheritdoc}
     */
    public function send($message, WrappedEvent $wrappedEvent)
    {
        // TODO: Implement send() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        // TODO: Implement setDispatcher() method.
    }
}