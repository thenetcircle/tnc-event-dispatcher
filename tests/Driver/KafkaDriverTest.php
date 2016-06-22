<?php

namespace Tnc\Service\EventDispatcher\Test\Driver;

use Tnc\Service\EventDispatcher\Driver;

class KafkaDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Driver
     */
    private $pipeline;

    protected function setup()
    {
        $this->pipeline = new Driver\KafkaDriver('172.17.0.3:9092');
    }

    public function testPush()
    {
        $this->pipeline->push('testTopic', 'testMessage', 200, '1');
    }
}