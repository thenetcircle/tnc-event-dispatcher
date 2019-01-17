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

namespace TNC\EventDispatcher\Tests\Dispatchers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Tests\AbstractEventDispatcherTest;
use TNC\EventDispatcher\Dispatchers\SymfonyImpl\EventDispatcher;
use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\ActivityStreamsWrappedEventNormalizer;
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\ActivityStreamsNormalizer;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;

class SymfonyEventDispatcherTest extends AbstractEventDispatcherTest
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->dispatcher = $this->createEventDispatcher();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->dispatcher = null;
    }

    protected function createEventDispatcher()
    {
        $serializer = new Serializer(
          [new ActivityStreamsWrappedEventNormalizer(), new ActivityStreamsNormalizer()],
          new JsonFormatter()
        );

        $endPointMock = $this->getMockBuilder(EndPoint::class)->getMock();

        return new EventDispatcher($serializer, $endPointMock);
    }

    // TODO: add more tests and SerializedEvent dispatch
}

abstract class AbstractTestEvent implements TransportableEvent {
    public function getTransportMode() { return TransportableEvent::TRANSPORT_MODE_ASYNC; }
}
class TestEvent1 extends AbstractTestEvent {}
class TestEvent2 extends AbstractTestEvent {}
class TestEvent3 extends AbstractTestEvent {}
class TestEvent4 extends AbstractTestEvent {}

class CallableClass
{
    public function __invoke(TestEvent1 $event) {}
}

class TestEventListener
{
    public function listeningMuthodA(TestEvent2 $event) {}
}
class TestEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'testEvent3' => 'listeningMethod3',
            'testEvent4' => 'listeningMethod4'
        ];
    }

    public function listeningMethod3(TestEvent3 $event) {}
    public function listeningMethod4(TestEvent4 $event) {}
}