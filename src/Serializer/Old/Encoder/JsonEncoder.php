<?php

namespace Tnc\Service\EventDispatcher\Serializer\Encoder;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer\Encoder;

/**
 * JsonSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
class JsonEncoder implements Encoder
{
    /**
     * {@inheritdoc}
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($content)
    {
        if (null === ($data = json_decode($content, true))) {
            throw new InvalidArgumentException(sprintf('String %s can not be decoded.', $content));
        }

        return $data;
    }
}
