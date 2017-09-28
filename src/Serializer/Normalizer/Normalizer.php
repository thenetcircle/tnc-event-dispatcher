<?php

namespace TNC\EventDispatcher\Serializer\Normalizer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Interfaces\Serializer;

interface Normalizer
{
    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer);

    /**
     * @param object $object
     *
     * @return array
     */
    public function normalize($object);

    /**
     * @param array  $data
     * @param string $class
     *
     * @return object
     *
     * @throws InvalidArgumentException
    */
    public function denormalize($data, $class);

    /**
     * @param object $object
     *
     * @return bool
     */
    public function supportsNormalization($object);

    /**
     * @param array  $data
     * @param string $class
     *
     * @return bool
     */
    public function supportsDenormalization($data, $class);
}
