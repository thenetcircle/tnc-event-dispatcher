<?php

namespace Tnc\Service\EventDispatcher;

use Normalt\Normalizer\AggregateNormalizer;
use Tnc\Service\EventDispatcher\Normalizer\EventNormalizer;
use Tnc\Service\EventDispatcher\Normalizer\WrappedEventNormalizer;

class Serializer
{
    protected $aggregate;

    /**
     * @param AggregateNormalizer|null $aggregate
     */
    public function __construct(AggregateNormalizer $aggregate = null)
    {
        $this->aggregate = $aggregate ?: $this->createAggregateNormalizer();
    }

    /**
     * @param WrappedEvent $wrappedEvent
     *
     * @return string
     */
    public function serialize(WrappedEvent $wrappedEvent)
    {
        return json_encode($this->aggregate->normalize($wrappedEvent));
    }

    /**
     * @param string $contents
     *
     * @return WrappedEvent
     */
    public function unserialize($contents)
    {
        $data = json_decode($contents, true);

        return $this->aggregate->denormalize($data, 'Tnc\Service\EventDispatcher\WrappedEvent');
    }

    /**
     * @return AggregateNormalizer
     */
    private function createAggregateNormalizer()
    {
        return new AggregateNormalizer([new WrappedEventNormalizer(), new EventNormalizer()]);
    }
}
