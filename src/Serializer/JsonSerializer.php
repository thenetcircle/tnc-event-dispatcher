<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer;

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
