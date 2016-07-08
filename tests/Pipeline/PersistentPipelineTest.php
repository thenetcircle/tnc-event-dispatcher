<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Backend\PersistentPipeline;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\EventWrapper;

class PersistentPipelineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $backend;

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
        $this->backend  = $this->createMock(Backend::class);
        $serializer     = new Serializer\JsonSerializer();
        $this->pipeline = new PersistentPipeline($this->backend, $serializer, 200);

        $event = new Event\DefaultEvent(['sender'=>'user1', 'receiver'=>'user2']);
        $event->setName('message.send');
        $this->eventWrapper = new EventWrapper($event, 'async');
        $this->serializedEventWrapper = '{"sender":"user1","receiver":"user2","name":"message.send",' .
            '"_php_":{"class":"Tnc\\\Service\\\EventDispatcher\\\Event\\\DefaultEvent","mode":"async"}}';
    }

    public function testPush()
    {
        $this->backend->expects($this->once())
                      ->method('push')
                      ->with(
                         $this->equalTo('event-message'),
                         $this->equalTo($this->serializedEventWrapper),
                         $this->equalTo(null)
                     );

        $this->pipeline->push($this->eventWrapper);
    }

    public function testPopAndAck()
    {
        $channel = 'event-message';
        $data    = $this->serializedEventWrapper;
        $receipt = 'receipt';

        // Pop
        $this->backend->expects($this->once())
                      ->method('pop')
                      ->with($this->equalTo($channel), $this->equalTo($this->timeout))
                      ->will($this->returnValue(array($data, $receipt)));

        $result = $this->pipeline->pop($channel, $this->timeout);
        $this->assertEquals($this->eventWrapper, $result);


        // Ack
        $this->backend->expects($this->once())
                      ->method('ack')
                      ->with($this->equalTo($receipt));

        $this->pipeline->ack($result);
    }
}