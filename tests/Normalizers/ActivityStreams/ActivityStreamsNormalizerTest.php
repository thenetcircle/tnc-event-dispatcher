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

namespace TNC\EventDispatcher\Tests\Normalizers\ActivityStreams;

use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\ActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\ActivityStreamsNormalizer;
use TNC\EventDispatcher\Serializer;

class ActivityStreamsNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActivityStreamsNormalizer
     */
    public $normalizer;

    public function setUp()
    {
        $this->normalizer = new ActivityStreamsNormalizer();
    }

    public function tearDown()
    {
        $this->normalizer = null;
    }

    public function testNormalizeAndDenormalize()
    {
        $originalData = [
          'version'   => '2.0',
          'id'        => 'id',
          'verb'      => 'verb',
          'published' => 'published',
          'content'   => [1, 2, 3, 'a', 'b', 'c'],
          'actor'     => [
            'id' => 'providerId',
            'objectType' => 'providerType',
            'content' => 1
          ],
          'object'    => [
            'id' => 'providerId',
            'objectType' => 'providerType',
            'content' => 'abc'
          ],
          'target'    => [
            'id' => 'providerId',
            'objectType' => 'providerType',
            'content' => ['a'=>1, 'b'=>2, 'c'=>['a', 'b', 'c']]
          ],
          'provider'  => [
            'id' => 'providerId',
            'objectType' => 'providerType',
            'content' => ['a', 'd', 3]
          ]
        ];

        $expectedData = $originalData;
        $expectedData['content'] = \json_encode($expectedData['content']);
        $expectedData['actor']['content'] = \json_encode($expectedData['actor']['content']);
        $expectedData['object']['content'] = \json_encode($expectedData['object']['content']);
        $expectedData['target']['content'] = \json_encode($expectedData['target']['content']);
        $expectedData['provider']['content'] = \json_encode($expectedData['provider']['content']);

        $builder = new ActivityBuilder();
        $builder->setFromArray($originalData);
        $testEvent = new TestEvent($builder->getActivity());

        self::assertEquals(true, $this->normalizer->supportsNormalization($testEvent));
        $data = $this->normalizer->normalize($testEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, TestEvent::class));
        self::assertEquals($testEvent, $this->normalizer->denormalize($expectedData, TestEvent::class));
    }

    public function testIncompleteData()
    {
        $expectedData = [
          'version'   => '2.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
          'published' => 'testPublished',
          'actor'     => [
            'id'   => 'actorId'
          ],
          'object'    => [
            'objectType'    => 'testObject'
          ]
        ];

        $builder = new ActivityBuilder();
        $builder->setFromArray($expectedData);
        $testEvent = new TestEvent($builder->getActivity());

        self::assertEquals(true, $this->normalizer->supportsNormalization($testEvent));
        $data = $this->normalizer->normalize($testEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, TestEvent::class));
        self::assertEquals($testEvent, $this->normalizer->denormalize($expectedData, TestEvent::class));
    }

    public function testNormalizeEmptyActivityObject()
    {
        $testEvent = new TestEvent((new Activity())->setId('id'));
        self::assertEquals(['version' => '2.0', 'id' => 'id'], $this->normalizer->normalize($testEvent));

        $testEvent->activity->getProvider(); // which will create a empty ActivityObject implicitly
        self::assertEquals(['version' => '2.0', 'id' => 'id'], $this->normalizer->normalize($testEvent));
    }

    public function testAttachments()
    {
        $originalData = [
          'version'   => '2.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
          'published' => 'testPublished',
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
          ]
        ];

        $expectedData = [
          'version'   => '2.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
          'published' => 'testPublished',
          'actor'     => [
            'id'   => 'actorId',
            'objectType' => 'actorType',
            'content' => '{"a":1,"b":2}',
            'attachments' => [
              [
                'id'   => 'attachmentId1',
                'objectType' => 'attachmentType1',
                'content' => '"abc"',
                'attachments' => [
                  [
                    'id'   => 'subAttachmentId1',
                    'objectType' => 'subAttachmentType1',
                    'content' => '"subcontent"',
                  ]
                ]
              ],
              [
                'id'   => 'attachmentId2',
                'objectType' => 'attachmentType2',
                'content' => '"def"',
              ]
            ]
          ]
        ];

        $builder = new ActivityBuilder();
        $builder->setFromArray($originalData);
        $testEvent = new TestEvent($builder->getActivity());

        self::assertEquals(true, $this->normalizer->supportsNormalization($testEvent));
        $data = $this->normalizer->normalize($testEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, TestEvent::class));
        self::assertEquals($testEvent, $this->normalizer->denormalize($expectedData, TestEvent::class));
    }

    public function testWithSerializer()
    {
        $serializer = new Serializer(
          [$this->normalizer],
          new JsonFormatter()
        );

        $data = [
          'version'   => '2.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
          'published' => 'testPublished',
          'actor'     => [
            'id'   => 'actorId'
          ],
          'object'    => [
            'objectType'    => 'testObject'
          ]
        ];

        $builder = new ActivityBuilder();
        $builder->setFromArray($data);
        $testEvent = new TestEvent($builder->getActivity());

        $expectedData = json_encode($data);
        $serializedData = $serializer->serialize($testEvent);

        self::assertJson($expectedData, $serializedData);
        self::assertEquals($testEvent, $serializer->unserialize($serializedData, TestEvent::class));
    }

    public function testUserCase_IntIdMissed()
    {
        $builder = new ActivityBuilder();
        $builder->setActor([123, 'user_id']);
        $builder->setObject([321, 'object_id']);
        $builder->setTarget([456, 'target_user']);
        $builder->setPublished('2018-01-01T00:00:00+08:00');
        $testEvent = new TestEvent($builder->getActivity());

        self::assertEquals([
          'version' => '2.0',
          'actor' => [
            'id' => 123,
            'objectType' => 'user_id'
          ],
          'object' => [
            'id' => 321,
            'objectType' => 'object_id'
          ],
          'target' => [
            'id' => 456,
            'objectType' => 'target_user'
          ],
          'published' => '2018-01-01T00:00:00+08:00',
        ], $this->normalizer->normalize($testEvent));
    }
}