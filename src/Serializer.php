<?php

namespace TNC\EventDispatcher\Serialization;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use \TNC\EventDispatcher\Serialization\Normalizer\NormalizerInterface;

/**
 * AbstractSerializer
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
class Serializer
{
    protected $supportedNormalizers = array();
    protected $formatter            = null;

    /**
     * AbstractSerializer constructor.
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer\NormalizerInterface[] $supportedNormalizers
     * @param \TNC\EventDispatcher\Serialization\Encoder\FormatterInterface       $formatter
     */
    public function __construct(array $supportedNormalizers, $formatter)
    {
        array_unshift($supportedNormalizers, new EventWrapperNormalizer());

        $this->supportedNormalizers = $supportedNormalizers;
        $this->formatter            = $formatter;
    }

    /**
     * @param object $object
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function serialize($object)
    {
        $data = $this->normalize($object);

        return $this->format($data);
    }

    /**
     * @param string $data
     * @param string $class
     *
     * @return object
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($data, $class)
    {
        $data = $this->unformat($data);

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
    protected function format($data) {
        return $this->formatter->format($data);
    }

    /**
     * @param string $content
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function unformat($content) {
        return $this->formatter->unformat($content);
    }


    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizer\NormalizerInterface|null
     */
    protected function getNormalizer($object)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
            if (
                $normalizer instanceof NormalizerInterface
                && $normalizer->supportsNormalization($object)
            ) {
                return $normalizer;
            }
        }

        return null;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizer\NormalizerInterface|null
     */
    protected function getDenormalizer($data, $class)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
            if (
                $normalizer instanceof NormalizerInterface
                && $normalizer->supportsDenormalization($data, $class)
            ) {
                return $normalizer;
            }
        }

        return null;
    }
}
