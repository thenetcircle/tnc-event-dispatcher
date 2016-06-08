<?php


/**
 * TncEventDispatcher_Test_Unit_Case_Basic
 *
 * @author The NetCircle
 */
class TncEventDispatcher_Test_Unit_Case_Basic extends PHPUnit_Framework_TestCase
{
    public function testListeners()
    {
        $syncDispatcher = new \Tnc\Componment\EventDispatcher\Dispatcher\SyncDispatcher();
        var_dump($syncDispatcher);
    }
}