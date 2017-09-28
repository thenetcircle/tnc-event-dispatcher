<?php

namespace TNC\EventDispatcher\Serializer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Interfaces\Serializer;
use TNC\EventDispatcher\Serializer\Encoder\Encoder;
use \TNC\EventDispatcher\Serializer\Normalizer\Normalizer;
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
     * @var \TNC\EventDispatcher\Serializer\Normalizer\Normalizer[]
     */
    protected $normalizers = array();

    /**
     * @var \TNC\EventDispatcher\Serializer\Encoder\Encoder
     */
    protected $encoder = null;

    /**
     * AbstractSerializer constructor.
     *
     * @param Normalizer[] $normalizers
     * @param Encoder      $encoder
     */
    public function __construct(array $normalizers, $encoder)
    {
        array_unshift($normalizers, new EventWrapperNormalizer());

        foreach ($normalizers as $normalizer) {
            $normalizer->setSerializer($this);
        }

        $this->normalizers = $normalizers;
        $this->encoder = $encoder;
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
    public function unserialize($data, $class)
    {
        $data = $this->decode($data);

        return $this->denormalize($data, $class);
    }

    /**
     * @param object $object
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function normalize($object)
    {
        if (null === ($normalizer = $this->getNormalizer($object))) {
            throw new InvalidArgumentException(
                sprintf('Could not normalize object of class %s, No normalizer found!', get_class($object))
            );
        }

        return $normalizer->normalize($object);
    }

    /**
     * @param array  $data
     * @param string $class
     *
     * @return object
     *
     * @throws InvalidArgumentException
     */
    protected function denormalize($data, $class)
    {
        if (null === ($normalizer = $this->getDenormalizer($data, $class))) {
            throw new InvalidArgumentException(
                sprintf('Could not denormalize object of class %s, No normalizer found!', $class)
            );
        }

        return $normalizer->denormalize($data, $class);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function encode($data) {
        return $this->encoder->encode($data);
    }

    /**
     * @param string $content
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function decode($content) {
        return $this->encoder->decode($content);
    }


    /**
     * @return \TNC\EventDispatcher\Serializer\Normalizer\Normalizer|null
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
     * @return \TNC\EventDispatcher\Serializer\Normalizer\Normalizer|null
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
