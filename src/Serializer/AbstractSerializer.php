<?php

namespace TNC\EventDispatcher\Serializer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Interfaces\Serializer;
use TNC\EventDispatcher\Interfaces\Normalizer;
use TNC\EventDispatcher\Normalizer\EventWrapperNormalizer;

/**
 * AbstractSerializer
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
abstract class AbstractSerializer implements Serializer
{
    /**
     * @var \TNC\EventDispatcher\Interfaces\Normalizer[]
     */
    protected $normalizers = array();

    /**
     * AbstractSerializer constructor.
     *
     * @param Normalizer[] $normalizers
     */
    public function __construct(array $normalizers)
    {
        array_unshift($normalizers, new EventWrapperNormalizer());

        foreach ($normalizers as $normalizer) {
            $normalizer->setSerializer($this);
        }

        $this->normalizers = $normalizers;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($object)
    {
        $data = $this->normalize($object);

        return $this->encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($content, $class)
    {
        $data = $this->decode($content);

        return $this->denormalize($data, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object)
    {
        if (null === ($normalizer = $this->getNormalizer($object))) {
            throw new InvalidArgumentException(
                sprintf('Could not normalize object of class %s, No normalizer found!', get_class($object))
            );
        }

        return $normalizer->normalize($object);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class)
    {
        if (null === ($normalizer = $this->getDenormalizer($data, $class))) {
            throw new InvalidArgumentException(
                sprintf('Could not denormalize object of class %s, No normalizer found!', $class)
            );
        }

        return $normalizer->denormalize($data, $class);
    }


    /**
     * @return \TNC\EventDispatcher\Interfaces\Normalizer|null
     */
    protected function getNormalizer($object)
    {
        foreach ($this->normalizers as $normalizer) {
            if (
                $normalizer instanceof Normalizer
                && $normalizer->supportsNormalization($object)
            ) {
                return $normalizer;
            }
        }

        return null;
    }

    /**
     * @return \TNC\EventDispatcher\Interfaces\Normalizer|null
     */
    protected function getDenormalizer($data, $class)
    {
        foreach ($this->normalizers as $normalizer) {
            if (
                $normalizer instanceof Normalizer
                && $normalizer->supportsDenormalization($data, $class)
            ) {
                return $normalizer;
            }
        }

        return null;
    }
}
