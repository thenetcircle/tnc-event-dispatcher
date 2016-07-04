<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer;

class DefaultSerializer implements Serializer
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
     * DefaultSerializer constructor.
     *
     * @param \Tnc\Service\EventDispatcher\Serializer\Normalizer $normalizer
     * @param \Tnc\Service\EventDispatcher\Serializer\Encoder    $encoder
     */
    public function __construct(Normalizer $normalizer, Encoder $encoder)
    {
        $this->normalizer = $normalizer;
        $this->encoder    = $encoder;
    }

    /**
     * @param Event $event
     *
     * @return string
     */
    public function serialize(Event $event)
    {
        return $this->encoder->encode(
            $this->normalizer->normalize($event)
        );
    }

    /**
     * @param string $content
     *
     * @return Event
     */
    public function unserialize($content)
    {
        $this->normalizer->denormalize(
            $this->encoder->decode($content)
        );
    }
}
