<?php

namespace TNC\EventDispatcher\Serialization\Encoder;

use TNC\EventDispatcher\Exception\UnformatException;

class JsonFormatter implements Formatter
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
            throw new UnformatException(sprintf('String %s can not be decoded.', $formattedData));
        }

        return $data;
    }
}