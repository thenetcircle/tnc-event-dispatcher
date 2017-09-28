<?php

namespace TNC\EventDispatcher\Serializer\Encoder;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Interfaces\Serializer;

interface Encoder
{
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
