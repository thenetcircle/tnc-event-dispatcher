<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer;

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
