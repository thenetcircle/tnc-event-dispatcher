<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Util;
use Tnc\Service\EventDispatcher\WrappedEvent;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = new Serializer\JsonSerializer();
    }

    public function testSerializeEvent()
    {
        $event = new Event();
        $this->assertJsonStringEqualsJsonString('[]', $this->serializer->serialize($event));
    }

    public function testUnserializeEvent()
    {
        $event =  new Event();
        $newEvent = $this->serializer->unserialize('\Tnc\Service\EventDispatcher\Event', '[]');
        $this->assertEquals($event, $newEvent);
    }

    public function testSerializeRichEvent()
    {
        $event =  new RichEvent('rich', ['key1' => 'value1', 'key2' => 'value2'], ['extra1' => 'value1']);
        $this->assertJsonStringEqualsJsonString(
            '{
              "name":"rich",
              "context":{"key1":"value1","key2":"value2"}
            }',
            $this->serializer->serialize($event)
        );
    }

    public function testUnserializeRichEvent()
    {
        $event =  new RichEvent('rich', ['key1' => 'value1', 'key2' => 'value2'], []);
        $newEvent = $this->serializer->unserialize(
            '\Tnc\Service\EventDispatcher\Test\RichEvent',
            '{
              "name":"rich",
              "context":{"key1":"value1", "key2":"value2"}
            }'
        );
        $this->assertEquals($event, $newEvent);
    }

    public function testSerializeWrappedEvent()
    {
        $event = new Event();
        $wrappedEvent = new WrappedEvent('DD', 'testName', $event, 'testGroup', 'async');
        $time = time();
        Util::setInvisiblePropertyValue($wrappedEvent, 'time', $time);

        $this->assertJsonStringEqualsJsonString(
            '{
              "domainId":"DD",
              "name":"testName",
              "time":'.$time.',
              "data":[],
              "extra":{"group":"testGroup","mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Event"}
            }',
            $this->serializer->serialize($wrappedEvent)
        );
    }

    public function testSerializeWrappedRichEvent()
    {
        $time = time();
        $event = new RichEvent('rich', ['key1' => 'value1', 'key2' => 'value2'], ['extra1' => 'value1']);
        $wrappedEvent = new WrappedEvent('DD', 'testName', $event, 'testGroup', 'async');
        Util::setInvisiblePropertyValue($wrappedEvent, 'time', $time);

        $this->assertJsonStringEqualsJsonString(
            '{
              "domainId":"DD",
              "name":"testName",
              "time":'.$time.',
              "data":{"name":"rich","context":{"key1":"value1","key2":"value2"}},
              "extra":{"group":"testGroup","mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Test\\\RichEvent"}
            }',
            $this->serializer->serialize($wrappedEvent)
        );
    }

    public function testUnserializeWrappedRichEvent()
    {
        $event =  new RichEvent('rich', ['key1' => 'value1', 'key2' => 'value2'], []);
        $wrappedEvent = new WrappedEvent('DD', 'testName', $event, 'testGroup', 'async');
        $time = time();
        Util::setInvisiblePropertyValue($wrappedEvent, 'time', $time);

        $newWrappedEvent = $this->serializer->unserialize(
            '\Tnc\Service\EventDispatcher\WrappedEvent',
            '{
              "domainId":"DD",
              "name":"testName",
              "time":'.$time.',
              "data":{"name":"rich","context":{"key1":"value1","key2":"value2"}},
              "extra":{"group":"testGroup","mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Test\\\RichEvent"}
            }'
        );

        $this->assertEquals($wrappedEvent, $newWrappedEvent);
        $this->assertEquals($wrappedEvent->getEvent(), $newWrappedEvent->getEvent());
        $this->assertEquals($event, $newWrappedEvent->getEvent());
    }
}


class RichEvent extends Event
{
    public $name;
    protected $context = array();
    private $extra = array(); // for private property, you need implement your own serialize method

    public function __construct($name, array $context, array $extra)
    {
        $this->name = $name;
        $this->context = $context;
        $this->extra = $extra;
    }
}