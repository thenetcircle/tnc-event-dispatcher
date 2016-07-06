<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Normalizer;

/**
 * AbstractSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
abstract class AbstractSerializer implements Serializer
{
    /**
     * @var Normalizer[]
     */
    protected $normalizers = array();

    /**
     * AbstractSerializer constructor.
     *
     * @param Normalizer[] $normalizers
     */
    public function __construct(array $normalizers = null)
    {
        $this->normalizers = $normalizers ?: array(new Normalizer\CustomNormalizer());
        foreach ($this->normalizers as $normalizer) {
            $normalizer->setSerializer($this);
        }
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
     * @return Normalizer|null
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
     * @return Normalizer|null
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
