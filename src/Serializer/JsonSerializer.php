<?php

namespace Tnc\Service\EventDispatcher\Serializer;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;

/**
 * JsonSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
class JsonSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($string)
    {
        if (null === ($data = json_decode($string, true))) {
            throw new InvalidArgumentException(sprintf('String %s can not be decoded.', $string));
        }

        return $data;
    }
}
