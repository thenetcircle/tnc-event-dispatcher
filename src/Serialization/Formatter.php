<?php

namespace TNC\EventDispatcher\Serialization;

use TNC\EventDispatcher\Exception\FormatException;
use TNC\EventDispatcher\Exception\UnformatException;

interface Formatter
{
    /**
     * Formats a semi-result
     *
     * @param array $data
     *
     * @return string formatted data
     *
     * @throws FormatException
     */
    public function format($data);

    /**
     * Unformats a result to be a semi-result
     *
     * @param string $formattedData
     *
     * @return array data
     *
     * @throws UnformatException
     */
    public function unformat($formattedData);
}
