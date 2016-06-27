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
    protected $errStr;

    /**
     * DefaultEvent constructor.
     *
     * @param int $errCode
     * @param string $errStr
     */
    public function __construct($errCode, $errStr)
    {
        $this->errCode = $errCode;
        $this->errStr = $errStr;
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
    public function getErrStr()
    {
        return $this->errStr;
    }
}