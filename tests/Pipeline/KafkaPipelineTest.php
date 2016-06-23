<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Pipeline;
use Tnc\Service\EventDispatcher\Pipeline\PersistentPipeline;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;

class KafkaPipelineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var int
     */
    private $timeout = 2000;

    /**
     * @var EventWrapper
     */
    private $eventWrapper;
    private $serializedEventWrapper;


    public function setUp()
    {
        $this->driver   = $this->createMock(Driver::class);
        $serializer     = new Serializer\JsonSerializer();
        $this->pipeline = new PersistentPipeline($this->driver, $serializer, 200);

        $event = new Event\DefaultEvent(['sender'=>'user1', 'receiver'=>'user2']);
        $event->setName('message.send');
        $this->eventWrapper = new EventWrapper($event, 'async');
        $this->serializedEventWrapper = '{"sender":"user1","receiver":"user2","name":"message.send",' .
            '"_extra_":{"class":"Tnc\\\Service\\\EventDispatcher\\\Event\\\DefaultEvent","mode":"async"}}';
    }

    public function testPush()
    {
        $this->driver->expects($this->once())
                     ->method('push')
                     ->with(
                         $this->equalTo('event-message'),
                         $this->equalTo($this->serializedEventWrapper),
                         $this->equalTo($this->timeout),
                         $this->equalTo(null)
                     );

        $this->pipeline->push($this->eventWrapper, $this->timeout);
    }

    public function testPopAndAck()
    {
        $channel = 'event-message';
        $data    = $this->serializedEventWrapper;
        $receipt = 'receipt';

        // Pop
        $this->driver->expects($this->once())
                     ->method('pop')
                     ->with($this->equalTo($channel), $this->equalTo($this->timeout))
                     ->will($this->returnValue(array($data, $receipt)));

        $result = $this->pipeline->pop($channel, $this->timeout);
        $this->assertEquals($this->eventWrapper, $result);


        // Ack
        $this->driver->expects($this->once())
                     ->method('ack')
                     ->with($this->equalTo($receipt));

        $this->pipeline->ack($result);
    }
}