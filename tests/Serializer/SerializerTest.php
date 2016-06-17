<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Util;
use Tnc\Service\EventDispatcher\EventWrapper;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Event
     */
    private $event;
    private $serializedEvent;

    /**
     * @var RichEvent
     */
    private $richEvent;
    private $serializedRichEvent;
    private $serializedWrappedRichEvent;

    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var int
     */
    private $time;


    public function setUp()
    {
        $this->time = time();

        $this->event = new Event();
        $this->event->setContext(['sender' => 1, 'receiver' => 2]);
        $this->serializedEvent = '{
          "source": null,
          "name": null,
          "time": null,
          "context": {"sender":1, "receiver":2},
          "group":null,
          "mode":null
        }';

        $this->richEvent = new RichEvent(
            'default',
            'message.send',
            'async',
            'group1',
            ['sender' => 1, 'receiver' => 2],
            ['extra1' => 1, 'extra2' => 2],
            $this->time
        );
        $this->serializedRichEvent = '{
          "source": "default",
          "name": "message.send",
          "group":"group1",
          "mode":"async",
          "context": {"sender":1, "receiver":2},
          "extra":{"extra1":1, "extra2":2},
          "field1":1,
          "field2":2,
          "time":'.$this->time.'
        }';
        $this->serializedWrappedRichEvent = '{
          "source":"default",
          "name":"message.send",
          "time":'.$this->time.',
          "context":{"sender":1, "receiver":2},
          "extra":{"extra1":1, "extra2":2},
          "field1":1,
          "field2":2,
          "_extra_":{"group":"group1","mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Test\\\RichEvent"}
        }';

        $this->serializer = new Serializer\JsonSerializer();
    }

    public function testSerializeEvent()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->serializedEvent,
            $this->serializer->serialize($this->event)
        );
    }

    public function testUnserializeEvent()
    {
        $event = $this->serializer->unserialize(
            '\Tnc\Service\EventDispatcher\Event',
            $this->serializedEvent
        );
        $this->assertEquals($this->event, $event);
    }

    public function testSerializeRichEvent()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->serializedRichEvent,
            $this->serializer->serialize($this->richEvent)
        );
    }

    public function testUnserializeRichEvent()
    {
        $event = $this->serializer->unserialize(
            '\Tnc\Service\EventDispatcher\Test\RichEvent',
            $this->serializedRichEvent
        );
        $this->assertEquals($this->richEvent, $event);
    }

    public function testSerializeWrappedEvent()
    {
        $eventWrapper = new EventWrapper($this->event);

        $this->assertJsonStringEqualsJsonString(
            '{
              "source":null,
              "name":null,
              "time":null,
              "context":{"sender":1, "receiver":2},
              "_extra_":{"group":null,"mode":null,"class":"Tnc\\\Service\\\EventDispatcher\\\Event"}
            }',
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testSerializeWrappedRichEvent()
    {
        $eventWrapper = new EventWrapper($this->richEvent);

        $this->assertJsonStringEqualsJsonString(
            $this->serializedWrappedRichEvent,
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testUnserializeWrappedRichEvent()
    {
        $eventWrapper = new EventWrapper($this->richEvent);

        $newEventWrapper = $this->serializer->unserialize(
            '\Tnc\Service\EventDispatcher\EventWrapper',
            $this->serializedWrappedRichEvent
        );

        $this->assertEquals($eventWrapper, $newEventWrapper);
        $this->assertEquals($this->richEvent, $newEventWrapper->getEvent());
    }
}


class RichEvent extends Event
{
    protected $extra = array();
    protected $field1 = 1;
    protected $field2 = 2;

    public function __construct(
        $source,
        $name,
        $mode,
        $group,
        array $context,
        array $extra,
        $time
    )
    {
        $this->appendExtraInfo($source, $name, $mode, $time);
        $this->setGroup($group);
        $this->setContext($context);
        $this->extra = $extra;
    }
}