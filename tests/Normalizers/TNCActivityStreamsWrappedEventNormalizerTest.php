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

namespace TNC\EventDispatcher\Tests\Normalizers;

use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsWrappedEventNormalizer;
use TNC\EventDispatcher\Serializer;
use TNC\EventDispatcher\WrappedEvent;

class TNCActivityStreamsWrappedEventNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TNCActivityStreamsWrappedEventNormalizer
     */
    public $normalizer;

    public function setUp()
    {
        $this->normalizer = new TNCActivityStreamsWrappedEventNormalizer();
    }

    public function tearDown()
    {
        $this->normalizer = null;
    }

    public function testNormalizeAndDenormalize()
    {
        $normalizedEvent = [
          'id'        => 'id',
          'provider'  => 'testProvider',
          'published' => 'testPublished',
          'context'   => [
            'ckey1' => 'cvalue1',
            'ckey2' => 'cvalue2',
            'ckey4' => 'cvalue4',
          ],
          'actor'     => [
            'id'   => 'actorId',
            'type' => 'actorType'
          ],
          'object'    => [
            'type'    => 'testObject',
            'context' => [
              'key1' => 'value1',
              'key2' => 'value2'
            ]
          ]
        ];

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          'message.send',
          $normalizedEvent,
          TransportableEvent::class
        );

        $expectedData = $normalizedEvent;
        $expectedData['verb'] = 'message.send';
        $expectedData['context']['metadata'] = [
          'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
          'class' => TransportableEvent::class
        ];

        self::assertEquals(true, $this->normalizer->supportsNormalization($wrappedEvent));
        $data = $this->normalizer->normalize($wrappedEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, WrappedEvent::class));

        $expectedWrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          'message.send',
          array_merge($normalizedEvent, ['verb' => 'message.send']),
          TransportableEvent::class
        );
        self::assertEquals($expectedWrappedEvent, $this->normalizer->denormalize($expectedData, WrappedEvent::class));
    }

    public function testWithSerializer()
    {
        $serializer = new Serializer(
          [$this->normalizer, new TNCActivityStreamsNormalizer()],
          new JsonFormatter()
        );

        $builder = new TNCActivityBuilder();
        $builder->setId("id");
        $builder->setActor('actorId');
        $builder->setObject('testObject');
        $builder->setPublished('testPublished');
        $activity = $builder->getActivity();
        $testEvent = new TestEvent($activity);

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          'message.send',
          $serializer->normalize($testEvent),
          TransportableEvent::class
        );

        $expectedData = json_encode([
          'version'   => '1.0',
          'id'        => 'id',
          'verb'      => 'message.send',
          'published' => 'testPublished',
          'actor'     => [
            'id'   => 'actorId'
          ],
          'object'    => [
            'type'    => 'testObject'
          ],
          'context' => [
            'metadata' => [
              'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
              'class' => TransportableEvent::class
            ]
          ]
        ]);

        $data = $serializer->serialize($wrappedEvent);
        self::assertJson($expectedData, $data);

        /** @var WrappedEvent $unserializedWrappedEvent */
        $unserializedWrappedEvent = $serializer->unserialize($expectedData, WrappedEvent::class);
        $expectedTestEvent = $testEvent;
        $expectedTestEvent->activity->verb = 'message.send';

        self::assertEquals($expectedTestEvent, $serializer->denormalize($unserializedWrappedEvent->getNormalizedEvent(),
          TestEvent::class));
    }
}
