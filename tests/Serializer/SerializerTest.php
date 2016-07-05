<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Serializer\Annotation\ActivityStreams\Actor;


class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $normalizers = array(
            new Serializer\Normalizer\EventNormalizer()
        );
        $encoder = new Serializer\Encoder\ActivityStreamsEncoder();

        $this->serializer = new Serializer\SymfonySerializerAdapter($normalizers, $encoder);
    }

    public function testNormalizeEvent()
    {
        $event = new MockActivityEvent();

        var_dump($this->serializer->serialize($event));
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
     * Id
     */
    private $id;

    /**
     * @Actor("user")
     */
    private $sender;

    /**
     * Actor("user")
     */
    private $receiver;

    /**
     * Actor("message")
     */
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