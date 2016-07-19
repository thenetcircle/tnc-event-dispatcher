<?php

namespace Tnc\Service\EventDispatcher\Test\Backend;

use Tnc\Service\EventDispatcher\Backend\KafkaBackend;
use Tnc\Service\EventDispatcher\ChannelDetective\SimpleChannelDetective;
use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Event\ActivityStreamsEvent;
use Tnc\Service\EventDispatcher\ExternalDispatcher\NullExternalDispatcher;
use Tnc\Service\EventDispatcher\Pipeline;
use Tnc\Service\EventDispatcher\Serializer\JsonSerializer;

class KafkaBackendTest extends \PHPUnit_Framework_TestCase
{
    public function testPush()
    {
        $externalDispatcher = new NullExternalDispatcher();

        $backend          = new KafkaBackend(
        #'10.60.0.129:9092,10.60.0.129:9093,10.60.0.129:9094',
            'maggie-kafka-1.thenetcircle.lab:9092,maggie-kafka-2.thenetcircle.lab:9092,maggie-kafka-3.thenetcircle.lab:9092',
            [],
            false
        );
        $serializer       = new JsonSerializer();
        $channelDetective = new SimpleChannelDetective();

        $pipeline   = new Pipeline($externalDispatcher, $backend, $serializer, $channelDetective);
        $dispatcher = new Dispatcher($externalDispatcher, $pipeline);

        for ($i = 0; $i < 100; $i++) {
            $event = new ActivityStreamsEvent();
            $event->setProvider(ActivityStreamsEvent::createObj('test', 'KafkaBackend'))
                  ->setActor( ActivityStreamsEvent::createObj('user', mt_rand(100000, 999999)))
                  ->setObject( ActivityStreamsEvent::createObj('message', 15))
                  ->setTarget( ActivityStreamsEvent::createObj('user', 171))
                  ->setContext( ['ip' => '192.168.1.1', 'user-agent' => 'Apache']);

            $dispatcher->dispatch('message.test-'.mt_rand(10000, 99999).'.send', $event, Dispatcher::MODE_ASYNC);
        }
    }
}