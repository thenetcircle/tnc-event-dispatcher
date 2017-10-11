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

use TNC\EventDispatcher\Serialization\Formatters\JsonFormatter;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;
use TNC\EventDispatcher\Serializer;

class TNCActivityStreamsNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TNCActivityStreamsNormalizer
     */
    public $normalizer;

    public function setUp()
    {
        $this->normalizer = new TNCActivityStreamsNormalizer();
    }

    public function tearDown()
    {
        $this->normalizer = null;
    }

    public function testNormalizeAndDenormalize()
    {
        $expectedData = [
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

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($expectedData);
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
          'version'   => '1.0',
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

        $builder = new DefaultActivityBuilder();
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
        $testEvent = new TestEvent(new Activity());
        self::assertEquals(['version' => '1.0'], $this->normalizer->normalize($testEvent));

        $testEvent->activity->getProvider(); // which will create a empty ActivityObject implicitly
        self::assertEquals(['version' => '1.0'], $this->normalizer->normalize($testEvent));
    }

    public function testAttachments()
    {
        $expectedData = [
          'version'   => '1.0',
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

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($expectedData);
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
          'version'   => '1.0',
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

        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($data);
        $testEvent = new TestEvent($builder->getActivity());

        $expectedData = json_encode($data);
        $serializedData = $serializer->serialize($testEvent);

        self::assertJson($expectedData, $serializedData);
        self::assertEquals($testEvent, $serializer->unserialize($serializedData, TestEvent::class));
    }
}