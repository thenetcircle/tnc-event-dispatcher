<?php

namespace TNC\EventDispatcher\Serialization\Encoder;

use TNC\EventDispatcher\Exception\UnformattableException;

interface FormatterInterface
{
    /**
     * @param array $data
     *
     * @return string formatted data
     *
     * @throws UnformattableException
     */
    public function format($data);

    /**
     * @param string $formattedData
     *
     * @return array data
     *
     * @throws UnformattableException
     */
    public function unformat($formattedData);
}
