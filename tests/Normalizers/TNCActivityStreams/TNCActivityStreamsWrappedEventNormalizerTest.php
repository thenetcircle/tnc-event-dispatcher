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
          'published' => 'published',
          'content'   => 'content',
          'actor'     => [
            'id' => 'actorId',
            'objectType' => 'actorType'
          ],
          'object'    => [
            'id' => 'objectId',
            'objectType' => 'objectType'
          ],
          'target'    => [
            'id' => 'targetId',
            'objectType' => 'targetType'
          ],
          'provider'  => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ]
        ];

        $this->expectedData = $this->testData;
        $this->expectedData['title'] = 'message.send';
        $this->expectedData['verb'] = 'send';
        $this->expectedData['content'] = \json_encode($this->expectedData['content']);
        $this->expectedData['generator'] = [
          'id' => 'tnc-event-dispatcher',
          'objectType' => 'library',
          'content' => \json_encode([
            'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
            'class' => TransportableEvent::class
          ])
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
        $eventName = 'message.send';

        $normalizedEvent = $this->testData;
        $normalizedEvent['content'] = \json_encode($normalizedEvent['content']);

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $eventName,
          $normalizedEvent,
          TransportableEvent::class
        );

        self::assertEquals(true, $this->normalizer->supportsNormalization($wrappedEvent));
        $data = $this->normalizer->normalize($wrappedEvent);
        self::assertEquals($this->expectedData, $data);

        $expectedWrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $eventName,
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
        $eventName = 'message.send';

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($this->testData);
        $testEvent = new TestEvent($builder->getActivity());

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          $eventName,
          $serializer->normalize($testEvent),
          TransportableEvent::class
        );

        $expectedData = json_encode($this->expectedData);
        $data = $serializer->serialize($wrappedEvent);
        self::assertJson($expectedData, $data);

        /** @var WrappedEvent $unserializedWrappedEvent */
        $unserializedWrappedEvent = $serializer->unserialize($expectedData, WrappedEvent::class);

        $expectedTestEvent = $testEvent;
        $expectedTestEvent->activity->setTitle('message.send');
        $expectedTestEvent->activity->setVerb('send');
        $expectedTestEvent->activity->setGenerator(
          (new ActivityObject())
            ->setId('tnc-event-dispatcher')
            ->setObjectType('library')
            ->setContent([
              'mode'  => TransportableEvent::TRANSPORT_MODE_ASYNC,
              'class' => TransportableEvent::class
            ])
        );

        self::assertEquals($expectedTestEvent, $serializer->denormalize($unserializedWrappedEvent->getNormalizedEvent(),
          TestEvent::class));
    }

    public function testSerializedEvent()
    {
        $serializer = new Serializer(
          [$this->normalizer, new TNCActivityStreamsNormalizer()],
          new JsonFormatter()
        );

        /*$data = [
          'actor'     => [
            'id'   => 'actorId',
            'objectType' => 'actorType',
            'content' => ['a' => 1, 'b' => 2],
            'attachments' => [
              [
                'id'   => 'attachmentId1',
                'objectType' => 'attachmentType1',
                'content' => 'abc',
                'attachments' => [
                  [
                    'id'   => 'subAttachmentId1',
                    'objectType' => 'subAttachmentType1',
                    'content' => 'subcontent',
                  ]
                ]
              ],
              [
                'id'   => 'attachmentId2',
                'objectType' => 'attachmentType2',
                'content' => 'def',
              ]
            ]
          ],
          'object'    => [
            'id' => 'objectId',
            'objectType' => 'objectType'
          ],
          'target'    => [
            'id' => 'targetId',
            'objectType' => 'targetType'
          ],
          'provider'  => [
            'id' => 'providerId',
            'objectType' => 'providerType'
          ],
          'content'   => ['a' => 'testa', 'b' => 'testb']
        ];*/

        $data = [
          'actor'     => [
            'id'   => '3',
            'objectType' => 'user',
            'content' => ['age' => 18, 'name' => 'Want Er Gou']
          ],
          'object'    => [
            'id' => '0101',
            'objectType' => 'quiz.subcategory',
            'content' => [
              'a' => 1,
              'b' => 2,
              'c' => 3,
              'd' => 4
            ]
          ],
          "published" => "2017-12-21T05:17:40+00:00",
  "title" => "user.quiz.update",
  "verb" => "update",
  "id" => "ED-user.quiz.update-3-5a3b43f4e6297"
        ];

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($data);
        $testEvent = new TestEvent($builder->getActivity());

        $wrappedEvent = new WrappedEvent(
          TransportableEvent::TRANSPORT_MODE_ASYNC,
          'message.send',
          $serializer->normalize($testEvent),
          TransportableEvent::class
        );

        echo $serializer->serialize($wrappedEvent);
    }
}
