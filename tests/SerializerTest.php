<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Normalizer\EventNormalizer;
use Tnc\Service\EventDispatcher\Serializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeEvent()
    {
        $event = new Event();
        $event->setUserId(123);

        $normalizer = new EventNormalizer();
        $serializer = new Serializer([$normalizer]);

        echo $serializer->serialize($event, 'json');

        echo 'def';
    }
}