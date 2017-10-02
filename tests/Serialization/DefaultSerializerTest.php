<?php

namespace TNC\EventDispatcher\Tests\Serializer;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Normalizer\ActivityStreams\ActivityBuilder;
use TNC\EventDispatcher\Tests\Mock\MockActivityTNCActivityStreamsEvent;
use TNC\EventDispatcher\Event\DefaultEvent;
use TNC\EventDispatcher\Interfaces\Serializer;
use TNC\EventDispatcher\Event\EventWrapper;
use TNC\EventDispatcher\Serialization\DefaultSerializer;

class DefaultSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultEvent
     */
    private $eventName;
    private $event;
    private $serializedEvent;
    private $serializedWrappedEvent;

    /**
     * @var MockActivityTNCActivityStreamsEvent
     */
    private $activityEvent;
    private $serializedActivityEvent;
    private $serializedWrappedActivityEvent;

    private $unknownEvent;
    private $serializedUnknownEvent;

    /**
     * @var DefaultSerializer
     */
    private $serializer;
    private $datetime;
    private $mode;


    public function setUp()
    {
        $this->datetime   = (new \DateTime())->format(\DateTime::RFC3339);
        $this->mode       = Dispatcher::MODE_SYNC_PLUS;
        $this->serializer = new DefaultSerializer();


        $this->eventName = 'testEvent';
        $this->event = new DefaultEvent(['sender' => 'user1', 'receiver' => 'user2']);
        $this->serializedEvent = '{"sender":"user1","receiver":"user2"}';
        $this->serializedWrappedEvent = preg_replace(
            '/}$/',
            ',"extra":{"name":"'.$this->eventName.'","mode":"' . $this->mode .
            '","class":"TNC\\\\\Service\\\\\EventDispatcher\\\\\Event\\\\\DefaultEvent"}}',
            $this->serializedEvent
        );


        $activity = ActivityBuilder::createActivity()
                                   ->setVerb('send')
                                   ->setProviderByParams('community', 'DD')
                                   ->setActorByParams('user', '171')
                                   ->setObjectByParams('message', '2221')
                                   ->setTargetByParams('user', '2281')
                                   ->setPublished($this->datetime)
                                   ->setId('TestId')
                                   ->getActivity();
        $this->activityEvent = new MockActivityTNCActivityStreamsEvent($activity);
        $this->serializedActivityEvent = '{
          "verb": "send",
          "provider":{"id":"DD", "objectType":"community"},
          "actor": {"id":"171", "objectType":"user"},
          "object": {"id":"2221", "objectType":"message"},
          "target": {"id":"2281", "objectType":"user"},
          "published":"' . $this->datetime . '",
          "id":"TestId",
          "version":"1.0"
        }';
        $this->serializedWrappedActivityEvent = preg_replace(
            '/}$/',
            ',"extra":{"name":"'.$this->eventName.'","mode":"' . $this->mode .
            '","class":"TNC\\\\\Service\\\\\EventDispatcher\\\\\Tests\\\\\Mock\\\\\MockActivityEvent"}}',
            $this->serializedActivityEvent
        );

        // unknown event will be transform to DefaultEvent
        $unknownEvent = clone $this->event;
        unset($unknownEvent['sender'], $unknownEvent['receiver']);

        $unknownEvent['verb']      = 'send';
        $unknownEvent['provider']  = ['id' => 'DD', 'objectType' => 'community'];
        $unknownEvent['actor']     = ['id' => '171', 'objectType' => 'user'];
        $unknownEvent['object']    = ['id' => '2221', 'objectType' => 'message'];
        $unknownEvent['target']    = ['id' => '2281', 'objectType' => 'user'];
        $unknownEvent['published'] = $this->datetime;
        $unknownEvent['id']        = 'TestId';
        $unknownEvent['version']   = '1.0';

        $this->unknownEvent           = $unknownEvent;
        $this->serializedUnknownEvent = preg_replace(
            '/}$/',
            ',"extra":{"name":"'.$this->eventName.'","mode":"' . $this->mode .
            '","class":"TNC\\\\\Service\\\\\EventDispatcher\\\\\Test\\\\\UnknownEvent"}}',
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
        $eventWrapper = new EventWrapper($this->eventName, $this->event, $this->mode);

        $this->assertJsonStringEqualsJsonString(
            $this->serializedWrappedEvent,
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testUnserializeWrappedEvent()
    {
        $eventWrapper = new EventWrapper($this->eventName, $this->event, $this->mode);

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
        $eventWrapper = new EventWrapper($this->eventName, $this->activityEvent, $this->mode);

        $this->assertJsonStringEqualsJsonString(
            $this->serializedWrappedActivityEvent,
            $this->serializer->serialize($eventWrapper)
        );
    }

    public function testUnserializeWrappedActivityEvent()
    {
        $eventWrapper = new EventWrapper($this->eventName, $this->activityEvent, $this->mode);

        $newEventWrapper = $this->serializer->unserialize(
            $this->serializedWrappedActivityEvent,
            get_class($eventWrapper)
        );

        $this->assertEquals($eventWrapper, $newEventWrapper);
        $this->assertEquals($this->activityEvent, $newEventWrapper->getEvent());
    }

    public function testUnserializeUnknownEvent()
    {
        $unknownEvent             = new EventWrapper($this->eventName, $this->unknownEvent, $this->mode);
        $unserializedUnknownEvent = $this->serializer->unserialize(
            $this->serializedUnknownEvent,
            EventWrapper::class
        );

        $this->assertEquals($unknownEvent, $unserializedUnknownEvent);
    }
}