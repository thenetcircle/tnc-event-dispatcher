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

namespace TNC\EventDispatcher\Tests\Serializer;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityStreamsNormalizer;

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

    public function testNormalizeAndDenormalize()
    {
        $builder = new TNCActivityBuilder();
        $builder->setId("id");
        $builder->setVerb("testVerb");
        $builder->setActor('actorId', 'actorType');
        $builder->setObject('testObject', ['key1' => 'value1', 'key2' => 'value2']);
        $builder->setPublished('testPublished');
        $builder->setProvider('testProvider');
        $builder->setContext(['ckey1' => 'cvalue1', 'ckey2' => 'cvalue2', 'ckey3' => 'cvalue3']);
        $builder->addContext('ckey4', 'cvalue4');
        $builder->delContext('ckey3');
        $activity = $builder->getActivity();
        $testEvent = new TestEvent($activity);

        $expectedData = [
          'version'   => '1.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
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

        self::assertEquals(true, $this->normalizer->supportsNormalization($testEvent));
        $data = $this->normalizer->normalize($testEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, TestEvent::class));
        self::assertEquals($testEvent, $this->normalizer->denormalize($expectedData, TestEvent::class));
    }

    public function testIncompleteData()
    {
        $builder = new TNCActivityBuilder();
        $builder->setId("id");
        $builder->setVerb("testVerb");
        $builder->setActor('actorId');
        $builder->setObject('testObject');
        $builder->setPublished('testPublished');
        $activity = $builder->getActivity();
        $testEvent = new TestEvent($activity);

        $expectedData = [
          'version'   => '1.0',
          'id'        => 'id',
          'verb'      => 'testVerb',
          'published' => 'testPublished',
          'actor'     => [
            'id'   => 'actorId'
          ],
          'object'    => [
            'type'    => 'testObject'
          ]
        ];

        self::assertEquals(true, $this->normalizer->supportsNormalization($testEvent));
        $data = $this->normalizer->normalize($testEvent);
        self::assertEquals($expectedData, $data);

        self::assertEquals(true, $this->normalizer->supportsDenormalization($expectedData, TestEvent::class));
        self::assertEquals($testEvent, $this->normalizer->denormalize($expectedData, TestEvent::class));
    }

}

class TestEvent implements TNCActivityStreamsEvent
{
    /**
     * @var Activity
     */
    public $activity = null;

    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(TNCActivityBuilder $builder)
    {
        return $this->activity;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getTransportMode()
    {
        return TransportableEvent::TRANSPORT_MODE_ASYNC;
    }
}