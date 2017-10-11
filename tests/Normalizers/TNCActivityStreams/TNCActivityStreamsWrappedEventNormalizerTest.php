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

namespace TNC\EventDispatcher\Tests\Normalizers\TNCActivityStreams;

use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject;
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

    /**
     * @var array
     */
    public $testData;

    /**
     * @var array
     */
    public $expectedData;

    public function setUp()
    {
        $this->normalizer = new TNCActivityStreamsWrappedEventNormalizer();
        $this->testData = [
          'version'   => '1.0',
          'id'        => 'id',
          'verb'      => 'verb',
          'published' => 'published',
          'content'   => 'content',
          'actor'     => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ],
          'object'    => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ],
          'target'    => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ],
          'provider'  => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ]
        ];

        $this->expectedData = $this->testData;
        $this->expectedData['verb'] = 'message.send';
        $this->expectedData['generator'] = [
          'attachments' => [[
            'id' => 'event-dispatcher-metadata',
            'content' => [
              'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
              'class' => TransportableEvent::class
            ]
          ]]
        ];
    }

    public function tearDown()
    {
        $this->normalizer = null;
        $this->testData = [];
        $this->expectedData = [];
    }

    public function testNormalizeAndDenormalize()
    {
        $verb = 'message.send';

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $verb,
          $this->testData,
          TransportableEvent::class
        );

        self::assertEquals(true, $this->normalizer->supportsNormalization($wrappedEvent));
        $data = $this->normalizer->normalize($wrappedEvent);
        self::assertEquals($this->expectedData, $data);

        $expectedWrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $verb,
          $data,
          TransportableEvent::class
        );
        self::assertEquals(true, $this->normalizer->supportsDenormalization($data, WrappedEvent::class));
        self::assertEquals($expectedWrappedEvent, $this->normalizer->denormalize($data, WrappedEvent::class));
    }

    public function testWithSerializer()
    {
        $serializer = new Serializer(
          [$this->normalizer, new TNCActivityStreamsNormalizer()],
          new JsonFormatter()
        );
        $verb = 'message.send';

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($this->testData);
        $testEvent = new TestEvent($builder->getActivity());

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $verb,
          $serializer->normalize($testEvent),
          TransportableEvent::class
        );

        $expectedData = json_encode($this->expectedData);
        $data = $serializer->serialize($wrappedEvent);
        self::assertJson($expectedData, $data);

        /** @var WrappedEvent $unserializedWrappedEvent */
        $unserializedWrappedEvent = $serializer->unserialize($expectedData, WrappedEvent::class);

        $expectedTestEvent = $testEvent;
        $expectedTestEvent->activity->setVerb('message.send');
        $expectedTestEvent->activity->getGenerator()->addAttachment(
          (new ActivityObject())
            ->setId('event-dispatcher-metadata')
            ->setContent([
              'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
              'class' => TransportableEvent::class
            ])
        );

        self::assertEquals($expectedTestEvent, $serializer->denormalize($unserializedWrappedEvent->getNormalizedEvent(),
          TestEvent::class));
    }
}
