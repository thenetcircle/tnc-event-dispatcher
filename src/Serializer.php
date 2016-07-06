<?php

namespace Tnc\Service\EventDispatcher;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

interface Serializer
{
    /**
     * @param object $object
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function serialize($object);

    /**
     * @param string $content
     * @param string $class
     *
     * @return object
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($content, $class);


    /**
     * @param object $object
     *
     * @return array
     *
     * @throws InvalidArgumentException
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
     * @param array $data
     *
     * @return string
     */
    public function encode($data);

    /**
     * @param string $content
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function decode($content);
}
