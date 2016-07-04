<?php

namespace Tnc\Service\EventDispatcher\Serializer;

interface Encoder
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function encode($data);

    /**
     * @param string  $content
     *
     * @return array
     */
    public function decode($content);
}
