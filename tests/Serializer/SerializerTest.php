<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;
use Tnc\Service\EventDispatcher\Serializer\Annotation\Accessor;
use Tnc\Service\EventDispatcher\Serializer\Annotation\Normalizer;
use Tnc\Service\EventDispatcher\Serializer\AnnotationSerializer;
use Tnc\Service\EventDispatcher\Serializer\Annotation\ActivityStreams\Actor;


class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeEvent()
    {
        $event = new MockActivityEvent();
        $serializer = new AnnotationSerializer();
        $serializer->serialize($event);
    }
}

/**
 * Class MockActivityEvent
 *
 * @package Tnc\Service\EventDispatcher\Test
 *
 * @author  The Netcircle
 */
class MockActivityEvent extends AbstractEvent
{
    /**
     * @Actor("user")
     */
    private $sender;

    private $receiver;

    private $message;

    public function __construct()
    {
        $this->sender = new User(100);
        $this->receiver = new User(1231010);
        $this->message = new Message(22294949);
        $this->setName('abc');
    }
}

class User
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}

class Message
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}