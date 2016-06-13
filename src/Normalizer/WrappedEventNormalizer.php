<?php

namespace Tnc\Service\EventDispatcher\Normalizer;

use Assert\Assertion;
use Normalt\Normalizer\AggregateNormalizer;
use Normalt\Normalizer\AggregateNormalizerAware;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tnc\Service\EventDispatcher\Util;
use Tnc\Service\EventDispatcher\WrappedEvent;

class WrappedEventNormalizer implements
    AggregateNormalizerAware,
    NormalizerInterface,
    DenormalizerInterface
{
    /**
     * @var AggregateNormalizer
     */
    protected $aggregate;

    /**
     * {@inheritdoc}
     */
    public function setAggregateNormalizer(AggregateNormalizer $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof WrappedEvent;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === 'Tnc\Service\EventDispatcher\WrappedEvent';
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'event' => $this->aggregate->normalize($object->getEvent()),
            'name'  => $object->getName(),
            'group' => $object->getGroup(),
            'mode'  => $object->getMode(),
            'class' => $object->getClass(),
            'time'  => $object->getTime(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        Assertion::choicesNotEmpty($data, ['event', 'name', 'class']);
        Assertion::classExists($data['class']);

        $event        = $this->aggregate->denormalize($data['event'], $data['class']);
        $wrappedEvent = new WrappedEvent($data['name'], $event, $data['group'], $data['mode']);
        Util::setInvisiblePropertyValue($wrappedEvent, 'class', $data['class']);
        Util::setInvisiblePropertyValue($wrappedEvent, 'time', $data['time']);

        return $wrappedEvent;
    }
}
