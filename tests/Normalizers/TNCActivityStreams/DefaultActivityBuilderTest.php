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

use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\DefaultActivityBuilder;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Actor;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Obj;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Provider;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Target;

class DefaultActivityBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultActivityBuilder
     */
    public $builder;

    public function setUp()
    {
        $this->builder = new DefaultActivityBuilder();
    }

    public function tearDown()
    {
        $this->builder = null;
    }

    public function testSetFromArray()
    {
        # set data from array
        $this->builder->setFromArray([
          'id' => '123',
          'verb' => 'message.send',
          'published' => 'now'
        ]);

        self::assertEquals('123', $this->builder->getActivity()->getId());
        self::assertEquals('message.send', $this->builder->getActivity()->getVerb());
        self::assertEquals('now', $this->builder->getActivity()->getPublished());

        # append data and override data
        $this->builder->setFromArray([
          'content' => [ 'a' => 1, 'b' => 2 ]
        ]);
        self::assertEquals([ 'a' => 1, 'b' => 2 ], $this->builder->getActivity()->getContent());

        $this->builder->setFromArray([
          'content' => 'new content'
        ]);
        self::assertEquals('new content', $this->builder->getActivity()->getContent());

        # set ActivityObject
        $testActor = (new Actor())->setId('test actor');
        $testObject = (new Obj())->setId('test object');
        $testTarget = (new Target())->setId('test target');
        $testProvider = (new Provider())->setId('test provider');

        $this->builder->setFromArray([
            'actor' => $testActor,
            'object' => $testObject,
            'target' => $testTarget,
            'provider' => $testProvider
        ]);
        self::assertEquals($testActor, $this->builder->getActivity()->getActor());
        self::assertEquals($testObject, $this->builder->getActivity()->getObject());
        self::assertEquals($testTarget, $this->builder->getActivity()->getTarget());
        self::assertEquals($testProvider, $this->builder->getActivity()->getProvider());
    }

    /**
     * @expectedException \TNC\EventDispatcher\Exception\InvalidArgumentException
     * @expectedExceptionMessage Key illegalkey is not supported.
     */
    public function testSetFromArrayException()
    {
        # set data from array
        $this->builder->setFromArray([
          'id' => '123',
          'verb' => 'message.send',
          'illegalkey' => 'abc'
        ]);
    }

    /**
     * @expectedException \TNC\EventDispatcher\Exception\InvalidArgumentException
     * @expectedExceptionMessage ActivityObject key illegalkey is not supported.
     */
    public function testSetFromArrayException2()
    {
        # set data from array
        $this->builder->setFromArray([
          'id' => '123',
          'verb' => 'message.send',
          'actor' => [
            'illegalkey' => 'abc'
          ]
        ]);
    }

    public function testGetterAndSetter()
    {
        # set data from array
        $this->builder->setId('123');
        $this->builder->setVerb('message.send');
        $this->builder->setPublished('now');

        self::assertEquals('123', $this->builder->getActivity()->getId());
        self::assertEquals('message.send', $this->builder->getActivity()->getVerb());
        self::assertEquals('now', $this->builder->getActivity()->getPublished());

        $testActor = (new Actor())->setId('test actor');
        $this->builder->setActor($testActor);
        self::assertEquals($testActor, $this->builder->getActivity()->getActor());
    }

    public function testSetActivityObject()
    {
        # actor, object, target, provider are same, just test actor here

        # use string
        $this->builder->setFromArray([
          'actor' => 'id1'
        ]);
        self::assertEquals('id1', $this->builder->getActivity()->getActor()->getId());

        # use array with one element
        $this->builder->setFromArray([
          'actor' => ['id2']
        ]);
        self::assertEquals('id2', $this->builder->getActivity()->getActor()->getId());

        # use array with two element
        $this->builder->setFromArray([
          'actor' => ['type', 'id3']
        ]);
        self::assertEquals('type', $this->builder->getActivity()->getActor()->getObjectType());
        self::assertEquals('id3', $this->builder->getActivity()->getActor()->getId());

        # use full array
        $this->builder->setFromArray([
          'actor' => [
            'objectType' => 'type2',
            'id' => 'id4',
            'content' => 'content',
            'attachments' => [ # the rule of attachments is as same as ActivityObject
              ['subtype1', 'subid1'],
              'subid2',
              [
                'objectType' => 'subtype3',
                'id' => 'subid3'
              ]
            ]
          ]
        ]);
        self::assertEquals('type2', $this->builder->getActivity()->getActor()->getObjectType());
        self::assertEquals('id4', $this->builder->getActivity()->getActor()->getId());
        self::assertEquals('content', $this->builder->getActivity()->getActor()->getContent());
        self::assertEquals(
          [
            (new ActivityObject())->setObjectType('subtype1')->setId('subid1'),
            (new ActivityObject())->setId('subid2'),
            (new ActivityObject())->setObjectType('subtype3')->setId('subid3')
          ],
          $this->builder->getActivity()->getActor()->getAttachments()
        );

        # use object directly
        $testActor = (new Actor())->setId('test actor');
        $this->builder->setFromArray([
          'actor' => $testActor
        ]);
        self::assertEquals($testActor, $this->builder->getActivity()->getActor());
    }
}