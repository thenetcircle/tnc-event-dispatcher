<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

interface Serializer
{
    /**
     * @param Normalizable $object
     *
     * @return string
     */
    public function serialize(Normalizable $object);

    /**
     * @param string $class
     * @param string $string
     *
     * @return Normalizable
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($class, $string);


    //---


    /**
     * @param Normalizable $object
     *
     * @return array
     */
    public function normalize(Normalizable $object);

    /**
     * @param string $class
     * @param array  $data
     *
     * @return Normalizable
     */
    public function denormalize($class, $data);


    //---


    /**
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data);

    /**
     * @param string $string
     *
     * @return array
     */
    public function decode($string);
}
