<?php

namespace TNC\EventDispatcher;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\FormatException;
use TNC\EventDispatcher\Exception\NoAvailableNormalizerException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Exception\UnformatException;
use TNC\EventDispatcher\Serialization\Normalizer\EventDispatcherNormalizer;
use TNC\EventDispatcher\Serialization\Normalizer\Normalizer;

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
     * @param \TNC\EventDispatcher\Serialization\Normalizer\Normalizer[] $supportedNormalizers
     * @param \TNC\EventDispatcher\Serialization\Encoder\Formatter       $formatter
     */
    public function __construct(array $supportedNormalizers, $formatter)
    {
        $this->supportedNormalizers = $supportedNormalizers;
        $this->formatter            = $formatter;
    }

    /**
     * @param object $object
     *
     * @return string
     *
     * @throws FormatException
     * @throws NoAvailableNormalizerException
     * @throws NormalizeException
     */
    public function serialize($object)
    {
        return $this->format(
          $this->normalize($object)
        );
    }

    /**
     * @param string $data
     * @param string $className
     *
     * @return object
     *
     * @throws UnformatException
     * @throws NoAvailableNormalizerException
     * @throws DenormalizeException
     */
    public function unserialize($data, $className)
    {
        return $this->denormalize(
          $this->unformat($data),
          $className
        );
    }

    /**
     * @param $object
     *
     * @return array
     *
     * @throws NoAvailableNormalizerException
     * @throws NormalizeException
     */
    public function normalize($object)
    {
        if (null === ($normalizer = $this->getNormalizer($object))) {
            throw new NoAvailableNormalizerException(
                sprintf('Could not normalize object of class %s, No normalizer found!', get_class($object))
            );
        }

        return $normalizer->normalize($object);
    }

    /**
     * @param $data
     * @param $className
     *
     * @return object
     *
     * @throws NoAvailableNormalizerException
     * @throws DenormalizeException
     */
    public function denormalize($data, $className)
    {
        if (null === ($normalizer = $this->getDenormalizer($data, $className))) {
            throw new NoAvailableNormalizerException(
                sprintf('Could not denormalize object of class %s, No normalizer found!', $className)
            );
        }

        return $normalizer->denormalize($data, $className);
    }

    /**
     * Formats a semi-result
     *
     * @param array $data
     *
     * @return string formatted data
     *
     * @throws FormatException
     */
    public function format($data) {
        return $this->formatter->format($data);
    }

    /**
     * Unformats a result to be a semi-result
     *
     * @param string $formattedData
     *
     * @return array data
     *
     * @throws UnformatException
     */
    public function unformat($formattedData) {
        return $this->formatter->unformat($formattedData);
    }

    /**
     * Prepends a new supported Normalizer
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer\Normalizer $normalizer
     *
     * @return $this
     */
    public function prependNormalizer(Normalizer $normalizer) {
        array_unshift($this->supportedNormalizers, $normalizer);
        return $this;
    }

    /**
     * Appends a new supported Normalizer
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer\Normalizer $normalizer
     *
     * @return $this
     */
    public function appendNormalizer(Normalizer $normalizer) {
        array_push($this->supportedNormalizers, $normalizer);
        return $this;
    }


    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizer\Normalizer|null
     */
    protected function getNormalizer($object)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
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
     * @return \TNC\EventDispatcher\Serialization\Normalizer\Normalizer|null
     */
    protected function getDenormalizer($data, $className)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
            if (
                $normalizer instanceof Normalizer
                && $normalizer->supportsDenormalization($data, $className)
            ) {
                return $normalizer;
            }
        }

        return null;
    }
}
