<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal\Event;

use Tnc\Service\EventDispatcher\Event\AbstractEvent;

class ErrorEvent extends AbstractEvent
{
    const NAME = 'service.event-dispatcher.error';

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
}