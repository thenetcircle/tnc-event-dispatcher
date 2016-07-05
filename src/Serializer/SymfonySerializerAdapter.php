<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Event\DefaultEvent;
use Tnc\Service\EventDispatcher\Serializer;

class SymfonySerializerAdapter implements Serializer
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private $serializer;


    /**
     * SymfonySerializerProxy constructor.
     *
     * @param array            $normalizers
     * @param JsonEncoder|null $encoder
     */
    public function __construct(array $normalizers = array(), JsonEncoder $encoder = null)
    {
        $encoder = $encoder ?: new JsonEncoder();

        $this->serializer = new \Symfony\Component\Serializer\Serializer(
            $normalizers,
            array($encoder)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(Event $event)
    {
        return $this->serializer->serialize($event, 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type = DefaultEvent::class)
    {
        return $this->serializer->deserialize($data, $type, 'json');
    }
}
