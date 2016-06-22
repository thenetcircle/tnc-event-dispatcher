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


    public function setUp()
    {
        $this->driver   = $this->createMock(Driver::class);
        $serializer     = new Serializer\JsonSerializer();
        $this->pipeline = new PersistentPipeline($this->driver, $serializer, 200);

        $event = new Event\DefaultEvent(['sender'=>'user1', 'receiver'=>'user2']);
        $event->setName('message.send');
        $this->eventWrapper = new EventWrapper($event, 'async');
    }

    public function testPush()
    {
        $channel = 'event-message';
        $data    = '{"name":"message.send","data":{"sender":"user1","receiver":"user2"},' .
            '"_extra_":{"class":"Tnc\\\Service\\\EventDispatcher\\\Event\\\DefaultEvent","mode":"async"}}';

        $this->driver->expects($this->once())
                     ->method('push')
                     ->with(
                         $this->equalTo('event-message'),
                         $this->equalTo($data),
                         $this->equalTo($this->timeout),
                         $this->equalTo(null)
                     );

        $this->pipeline->push($this->eventWrapper, $this->timeout);

        return array(
            'channel' => $channel,
            'data'    => $data
        );
    }


    /**
     * @depends testPush
     */
    public function testPop(array $args)
    {
        $args['receipt'] = 'receipt';

        $this->driver->expects($this->once())
                     ->method('pop')
                     ->with($this->equalTo($args['channel']), $this->equalTo($this->timeout))
                     ->will($this->returnValue(array($args['data'], $args['receipt'])));

        $this->assertEquals($this->eventWrapper, $this->pipeline->pop($args['channel'], $this->timeout));

        return $args;
    }

    /**
     * @depends testPop
     */
    public function testAck(array $args)
    {
        $this->driver->expects($this->once())
                     ->method('ack')
                     ->with($this->equalTo($args['receipt']));

        $this->pipeline->ack($this->eventWrapper);
    }
}