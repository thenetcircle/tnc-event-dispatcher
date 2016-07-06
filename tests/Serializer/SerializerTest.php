<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Event\ActivityStreamsEvent;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultEvent
     */
    private $event;
    private $serializedEvent;
    private $serializedWrappedEvent;

    /**
     * @var MockActivityEvent
     */
    private $activityEvent;
    private $serializedActivityEvent;
    private $serializedWrappedActivityEvent;

    /**
     * @var Serializer\JsonSerializer
     */
    private $serializer;
    private $datetime;
    private $mode;


    public function setUp()
    {
        $this->datetime   = (new \DateTime())->format(\DateTime::RFC3339);
        $this->mode       = Dispatcher::MODE_SYNC_PLUS;
        $this->serializer = new Serializer\JsonSerializer();


        $this->event = new DefaultEvent(['sender' => 'user1', 'receiver' => 'user2']);
        $this->event->setName('testEvent');
        $this->serializedEvent = '{"name":"testEvent", "sender":"user1", "receiver":"user2"}';
        $this->serializedWrappedEvent = preg_replace(
            '/}$/',
            ',"_php_":{"class":"Tnc\\\\\Service\\\\\EventDispatcher\\\\\Event\\\\\DefaultEvent","mode":"' .
            $this->mode .
            '"}}',
            $this->serializedEvent
        );


        $this->activityEvent = (new MockActivityEvent())->setName('message.send')
                                                                  ->setProvider(MockActivityEvent::createObj('community', 'DD'))
                                                                  ->setActor(MockActivityEvent::createObj('user', '171'))
                                                                  ->setObject(MockActivityEvent::createObj('message', '2221'))
                                                                  ->setTarget(MockActivityEvent::createObj('user', '2281'))
                                                                  ->setPublished($this->datetime)
                                                                  ->setId('TestId');
        $this->serializedActivityEvent = '{
          "verb": "message.send",
          "provider":{"id":"DD", "objectType":"community"},
          "actor": {"id":"171", "objectType":"user"},
          "object": {"id":"2221", "objectType":"message"},
          "target": {"id":"2281", "objectType":"user"},
          "published":"' . $this->datetime . '",
          "id":"TestId",
          "version":"1.0"}';
        $this->serializedWrappedActivityEvent = preg_replace(
            '/}$/',
            ',
            "_php_":{"class":"Tnc\\\\\Service\\\\\EventDispatcher\\\\\Test\\\\\MockActivityEvent","mode":"' . $this->mode . '"}
            }',
            $this->serializedActivityEvent
        );
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
            $this->serializedEvent,
            get_class($this->event)
        );
        $this->assertEquals($this->event, $event);
    }

    public function testSerializeWrappedEvent()
    {
        $eventWrapper = new EventWrapper($this->event, $this->mode);

        $this->assertJsonStringEqualsJsonString(
            $this->serializedWrappedEvent,
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testUnserializeWrappedEvent()
    {
        $eventWrapper = new EventWrapper($this->event, $this->mode);

        $newEventWrapper = $this->serializer->unserialize(
            $this->serializedWrappedEvent,
            get_class($eventWrapper)
        );

        $this->assertEquals($eventWrapper, $newEventWrapper);
        $this->assertEquals($this->event, $newEventWrapper->getEvent());
    }




    public function testSerializeActivityEvent()
    {
        $this->assertJsonStringEqualsJsonString(
            $this->serializedActivityEvent,
            $this->serializer->serialize($this->activityEvent)
        );
    }

    public function testUnserializeActivityEvent()
    {
        $event = $this->serializer->unserialize(
            $this->serializedActivityEvent,
            get_class($this->activityEvent)
        );
        $this->assertEquals($this->activityEvent, $event);
    }

    public function testSerializeWrappedActivityEvent()
    {
        $eventWrapper = new EventWrapper($this->activityEvent, $this->mode);

        $this->assertJsonStringEqualsJsonString(
            $this->serializedWrappedActivityEvent,
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testUnserializeWrappedRichEvent()
    {
        $eventWrapper = new EventWrapper($this->activityEvent, $this->mode);

        $newEventWrapper = $this->serializer->unserialize(
            $this->serializedWrappedActivityEvent,
            get_class($eventWrapper)
        );

        $this->assertEquals($eventWrapper, $newEventWrapper);
        $this->assertEquals($this->activityEvent, $newEventWrapper->getEvent());
    }
}


class MockActivityEvent extends ActivityStreamsEvent
{
}