<?php

namespace Tnc\Service\EventDispatcher\Test;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;
use Tnc\Service\EventDispatcher\Serializer\Annotation\ActivityStreams\Actor;
use Tnc\Service\EventDispatcher\Serializer\DefaultSerializer;
use Tnc\Service\EventDispatcher\Serializer\Encoder;
use Tnc\Service\EventDispatcher\Serializer\Encoder\JsonEncoder;
use Tnc\Service\EventDispatcher\Serializer\Normalizer;
use Tnc\Service\EventDispatcher\Serializer\Normalizer\AnnotationNormalizer;


class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @var Encoder
     */
    private $encoder;

    /**
     * @var DefaultSerializer
     */
    private $serializer;

    public function setUp()
    {
        $this->normalizer = new AnnotationNormalizer();
        $this->encoder = new JsonEncoder();

        $this->serializer = new DefaultSerializer(
            $this->normalizer,
            $this->encoder
        );
    }

    public function testNormalizeEvent()
    {
        $event = new MockActivityEvent();

        $this->normalizer->normalize($event);
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