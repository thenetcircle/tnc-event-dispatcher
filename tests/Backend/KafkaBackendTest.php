<?php

namespace Tnc\Service\EventDispatcher\Tests\Backend;

use Tnc\Service\EventDispatcher\Backend\KafkaBackend;
use Tnc\Service\EventDispatcher\ChannelDetective\SimpleChannelDetective;
use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Normalizer\ActivityStreams\ActivityBuilder;
use Tnc\Service\EventDispatcher\Tests\Mock\MockActivityEvent;
use Tnc\Service\EventDispatcher\Tests\Mock\MockExternalDispatcher;
use Tnc\Service\EventDispatcher\Pipeline;
use Tnc\Service\EventDispatcher\Serializer\DefaultSerializer;

class KafkaBackendTest extends \PHPUnit_Framework_TestCase
{
    public function testPush()
    {
        $externalDispatcher = new MockExternalDispatcher();

        $backend          = new KafkaBackend(
            #'10.60.0.129:9092,10.60.0.129:9093,10.60.0.129:9094',
            'maggie-kafka-1.thenetcircle.lab:9092,maggie-kafka-2.thenetcircle.lab:9092,maggie-kafka-3.thenetcircle.lab:9092',
            [],
            false
        );
        $serializer       = new DefaultSerializer();
        $channelDetective = new SimpleChannelDetective();

        $pipeline   = new Pipeline($backend, $serializer, $channelDetective);
        $dispatcher = new Dispatcher($externalDispatcher, $pipeline);

        for ($i = 0; $i < 100; $i++) {
            $event = new MockActivityEvent(
                ActivityBuilder::createActivity()->setProviderByParams('test', 'KafkaBackend')
                                                 ->setActorByParams('user', mt_rand(100000, 999999))
                                                 ->setObjectByParams('message', 15)
                                                 ->setTargetByParams('user', 171)
                                                 ->setContext(['ip' => '192.168.1.1', 'user-agent' => 'Apache'])
                                                 ->getActivity()
            );

            $dispatcher->dispatch('message.test-'.mt_rand(10000, 99999).'.send', $event, Dispatcher::MODE_ASYNC);
        }
    }
}