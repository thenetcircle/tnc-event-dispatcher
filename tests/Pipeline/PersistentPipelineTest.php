<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Driver;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Pipeline;
use Tnc\Service\EventDispatcher\Pipeline\PersistentPipeline;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Util;
use Tnc\Service\EventDispatcher\WrappedEvent;

class PersistentPipelineTest extends \PHPUnit_Framework_TestCase
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
     * @var int
     */
    private $time;

    public function setUp()
    {
        $this->driver   = $this->createMock(Driver::class);
        $serializer     = new Serializer\JsonSerializer();
        $this->pipeline = new PersistentPipeline($this->driver, $serializer);
        $this->time     = time();
    }

    public function testPush()
    {
        $wrappedEvent = $this->getWrappedEvent('DD', 'message.send', 'group', 'async');

        $this->driver->expects($this->once())
                     ->method('push')
                     ->with(
                         $this->equalTo('event-message'),
                         $this->equalTo(
                             '{"domainId":"DD","name":"message.send","time":' . $this->time . ',' .
                             '"data":[],' .
                             '"extra":{"mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Event","group":"group"}}'
                         ),
                         $this->equalTo($this->timeout),
                         $this->equalTo('group')
                     );

        $this->pipeline->push($wrappedEvent, $this->timeout);
    }

    public function testPushRichEvent()
    {
        $event        = new RichEvent('rich', ['key1' => 'value1', 'key2' => 'value2'], []);
        $wrappedEvent = $this->getWrappedEvent('DD', 'message.send', 'group', 'async', $event);
        $data         = '{"domainId":"DD","name":"message.send","time":' . $this->time . ',' .
            '"data":{"name":"rich","context":{"key1":"value1","key2":"value2"}},' .
            '"extra":{"mode":"async","class":"Tnc\\\Service\\\EventDispatcher\\\Test\\\RichEvent","group":"group"}}';
        $channel      = 'event-message';

        $this->driver->expects($this->once())
                     ->method('push')
                     ->with(
                         $this->equalTo($channel),
                         $this->equalTo($data),
                         $this->equalTo($this->timeout),
                         $this->equalTo('group')
                     );

        $this->pipeline->push($wrappedEvent, $this->timeout);

        return array(
            'channel' => $channel,
            'event'   => $wrappedEvent,
            'data'    => $data
        );
    }

    /**
     * @depends testPushRichEvent
     */
    public function testPop(array $args)
    {
        $args['receipt'] = 'receipt';

        $this->driver->expects($this->once())
                     ->method('pop')
                     ->with($this->equalTo($args['channel']), $this->equalTo($this->timeout))
                     ->will($this->returnValue(array($args['data'], $args['receipt'])));

        $this->assertEquals($args['event'], $this->pipeline->pop($this->timeout));
    }

    /**
     * @depends testPop
     */
    public function testAck(array $args)
    {
        $this->driver->expects($this->once())
                     ->method('ack')
                     ->with($this->equalTo($args['receipt']));

        $this->pipeline->ack($args['event']);
    }

    private function getWrappedEvent($domainId, $eventName, $group, $mode, $event = null)
    {
        $event        = $event ?: new Event();
        $wrappedEvent = new WrappedEvent($domainId, $eventName, $event, $group, $mode);
        Util::setInvisiblePropertyValue($wrappedEvent, 'time', $this->time);
        return $wrappedEvent;
    }
}


class RichEvent extends Event
{
    public    $name;
    protected $context = array();
    private   $extra   = array(); // for private property, you need implement your own serialize method

    public function __construct($name, array $context, array $extra)
    {
        $this->name    = $name;
        $this->context = $context;
        $this->extra   = $extra;
    }
}