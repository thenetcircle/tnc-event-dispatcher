<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Event\Internal;

use Symfony\Component\EventDispatcher\Event;

class ErrorSerializableEvent extends Event
{
    const ERROR = 'service.event-dispatcher.error';

    /**
     * @var string
     */
    protected $errCode;

    /**
     * @var string
     */
    protected $errString;

    /**
     * @var string
     */
    protected $source;

    /**
     * DefaultEvent constructor.
     *
     * @param int    $errCode
     * @param string $errString
     * @param string $source
     */
    public function __construct($errCode, $errString, $source = '')
    {
        $this->errCode   = $errCode;
        $this->errString = $errString;
        $this->source    = $source;
    }

    /**
     * @return string
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * @return string
     */
    public function getErrString()
    {
        return $this->errString;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransportToken()
    {
        return '';
    }
}