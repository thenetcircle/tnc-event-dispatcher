<?php

namespace TNC\Service\EventDispatcher\Tests;

use TNC\Service\EventDispatcher\Dispatcher;
use TNC\Service\EventDispatcher\Event\DefaultEvent;
use TNC\Service\EventDispatcher\Interfaces\Backend;
use TNC\Service\EventDispatcher\Interfaces\ChannelDetective;
use TNC\Service\EventDispatcher\ChannelDetective\SimpleChannelDetective;
use TNC\Service\EventDispatcher\Interfaces\Event;
use TNC\Service\EventDispatcher\Pipeline;
use TNC\Service\EventDispatcher\Interfaces\Serializer;
use TNC\Service\EventDispatcher\Event\EventWrapper;
use TNC\Service\EventDispatcher\Serializer\DefaultSerializer;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $backend;

    /**
     * @var ChannelDetective
     */
    private $channelDetective;

    /**
     * @var Pipeline
     */
    private $pipeline;

    /**
     * @var int
     */
    private $timeout = 2000;

    /**
     * @var \TNC\Service\EventDispatcher\Event\EventWrapper
     */
    private $eventWrapper;
    private $serializedEventWrapper;


    public function setUp()
    {
        $this->backend = $this->createMock(Backend::class);

        $serializer         = new DefaultSerializer();

        $this->channelDetective   = new SimpleChannelDetective();
        $this->pipeline = new Pipeline($this->backend, $serializer, $this->channelDetective);

        $eventName = 'message.send';
        $event = new DefaultEvent(['sender' => 'user1', 'receiver' => 'user2']);
        $this->eventWrapper           = new EventWrapper($eventName, $event, Dispatcher::MODE_ASYNC);
        $this->serializedEventWrapper =
            '{"sender":"user1","receiver":"user2"' .
            ',"extra":{"name":"message.send","mode":"async","class":"TNC\\\Service\\\EventDispatcher\\\Event\\\DefaultEvent"}}';
    }

    public function testPush()
    {
        $this->backend->expects($this->once())
                      ->method('push')
                      ->with(
                          $this->channelDetective->getPushingChannels($this->eventWrapper),
                          $this->equalTo($this->serializedEventWrapper),
                          $this->anything()
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

        list($resultEventWrapper, $resultReceipt) = $this->pipeline->pop($this->timeout, $channel);
        $this->assertEquals($this->eventWrapper, $resultEventWrapper);
        $this->assertEquals($receipt, $resultReceipt);


        // Ack
        $this->backend->expects($this->once())
                      ->method('ack')
                      ->with($this->equalTo($receipt));

        $this->pipeline->ack($resultReceipt);
    }
}