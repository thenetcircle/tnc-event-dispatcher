<?php

namespace Tnc\Service\EventDispatcher\Tests\Serializer;

use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\ActivityBuilder;
use Tnc\Service\EventDispatcher\Tests\Mock\MockActivityEvent;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Interfaces\Serializer;
use Tnc\Service\EventDispatcher\Event\EventWrapper;

class DefaultSerializerTest extends \PHPUnit_Framework_TestCase
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

    private $unknownEvent;
    private $serializedUnknownEvent;

    /**
     * @var Serializer\DefaultSerializer
     */
    private $serializer;
    private $datetime;
    private $mode;


    public function setUp()
    {
        $this->datetime   = (new \DateTime())->format(\DateTime::RFC3339);
        $this->mode       = Dispatcher::MODE_SYNC_PLUS;
        $this->serializer = new Serializer\DefaultSerializer();


        $this->event = new DefaultEvent(['sender' => 'user1', 'receiver' => 'user2']);
        $this->event->setName('testEvent');
        $this->event->setMode($this->mode);
        $this->event->setGroup('group1');
        $this->serializedEvent
                                      = '{
          "sender":"user1",
          "receiver":"user2",
          "extra":{
            "name":"testEvent",
            "mode":"' . $this->mode . '",
            "group":"group1",
            "propagationStopped":false
          }}';
        $this->serializedWrappedEvent = preg_replace(
            '/}}$/',
            ',"class":"Tnc\\\\\Service\\\\\EventDispatcher\\\\\Event\\\\\DefaultEvent"}}',
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
        $this->activityEvent = new MockActivityEvent($activity);
        $this->activityEvent->setName('message.send');
        $this->activityEvent->setMode($this->mode);
        $this->activityEvent->setGroup('group1');
        $activity->setExtra([
            'name'               => $this->activityEvent->getName(),
            'group'              => $this->activityEvent->getGroup(),
            'mode'               => $this->activityEvent->getMode(),
            'propagationStopped' => false,
        ]);
        $this->serializedActivityEvent
                                              = '{
          "verb": "send",
          "provider":{"id":"DD", "objectType":"community"},
          "actor": {"id":"171", "objectType":"user"},
          "object": {"id":"2221", "objectType":"message"},
          "target": {"id":"2281", "objectType":"user"},
          "published":"' . $this->datetime . '",
          "id":"TestId",
          "version":"1.0",
          "extra":{
            "name":"message.send",
            "mode":"' . $this->mode . '",
            "group":"group1",
            "propagationStopped":false
          }}';
        $this->serializedWrappedActivityEvent = preg_replace(
            '/}}$/',
            ',"class":"Tnc\\\\\Service\\\\\EventDispatcher\\\\\Tests\\\\\Mock\\\\\MockActivityEvent"}}',
            $this->serializedActivityEvent
        );

        // unknown event will be transform to DefaultEvent
        $unknownEvent = clone $this->event;
        $unknownEvent->setName('message.send');
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
            '/}}$/',
            ',"class":"Tnc\\\\\Service\\\\\EventDispatcher\\\\\Test\\\\\UnknownEvent"}}',
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

    public function testUnserializeWrappedActivityEvent()
    {
        $eventWrapper = new EventWrapper($this->activityEvent, $this->mode);

        $newEventWrapper = $this->serializer->unserialize(
            $this->serializedWrappedActivityEvent,
            get_class($eventWrapper)
        );

        $eventWrapper->getEvent()->getActivity()->addExtra('class', MockActivityEvent::class);

        $this->assertEquals($eventWrapper, $newEventWrapper);
        $this->assertEquals($this->activityEvent, $newEventWrapper->getEvent());
    }

    public function testUnserializeUnknownEvent()
    {
        $unknownEvent             = new EventWrapper($this->unknownEvent);
        $unserializedUnknownEvent = $this->serializer->unserialize(
            $this->serializedUnknownEvent,
            EventWrapper::class
        );

        $this->assertEquals($unknownEvent, $unserializedUnknownEvent);
    }
}