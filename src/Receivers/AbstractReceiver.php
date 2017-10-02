<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Receivers;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Interfaces\Receiver;

abstract class AbstractReceiver implements Receiver
{
    /**
     * @var \TNC\EventDispatcher\Dispatcher
     */
    protected $dispatcher = null;

    /**
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
