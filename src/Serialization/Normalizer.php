<?php

namespace TNC\EventDispatcher\Serialization\Normalizer;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Serializer;

interface Normalizer
{
    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param object $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($object);

    /**
     * Denormalizes a semi-result to be a Object according to the $className
     *
     * @param array  $data
     * @param string $className
     *
     * @return object
     *
     * @throws DenormalizeException
    */
    public function denormalize($data, $className);

    /**
     * Checks if this Normalizer supports normalization
     *
     * @param object $object
     *
     * @return bool
     */
    public function supportsNormalization($object);

    /**
     * Checks if this Normalizer supports denormalization
     *
     * @param array  $data
     * @param string $className
     *
     * @return bool
     */
    public function supportsDenormalization($data, $className);

    /**
     * Sets the serializer
     *
     * @param \TNC\EventDispatcher\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer);
}
