<?php


/**
 * TncEventDispatcher_Test_Unit_Case_Basic
 *
 * @author The NetCircle
 */
class TncEventDispatcher_Test_Unit_Case_Basic
    extends PHPUnit_Framework_TestCase
{
    public function testNewInstance()
    {
        TncEventDispatcher::manager('chat')->process(22277, 123, "this is a test message");
    }
}