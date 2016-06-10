<?php


/**
 * TncEventDispatcher_Test_Unit_Case_Basic
 *
 * @author The NetCircle
 */
class TncEventDispatcher_Test_Unit_Case_Basic extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tnc\Componment\EventDispatcher\Backend\Kafka
     */
    protected $kafka;

    public function setup() {
        $this->kafka = new Tnc\Componment\EventDispatcher\Backend\Kafka(
            'maggie-kafka-1.thenetcircle.lab:9092,maggie-kafka-2.thenetcircle.lab:9092,maggie-kafka-3.thenetcircle.lab:9092',
            ['producer' => []],
            ['producer' => []],
            false
        );
    }

    public function testProducer()
    {
        $this->kafka->produce(['Test-EventDispatcher'], 'Test Message', 'Test Key');
    }
}