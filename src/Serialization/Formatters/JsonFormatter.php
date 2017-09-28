<?php

namespace TNC\EventDispatcher\Serialization\Encoder;

use TNC\EventDispatcher\Exception\UnformattableException;

class JsonFormatter implements FormatterInterface
{
    /**
     * {@inheritdoc}
     */
    public function format($data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unformat($formattedData)
    {
        if (null === ($data = json_decode($formattedData, true))) {
            throw new UnformattableException(sprintf('String %s can not be decoded.', $formattedData));
        }

        return $data;
    }
}