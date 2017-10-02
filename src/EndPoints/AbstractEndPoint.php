<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\EndPoints;

use TNC\EventDispatcher\Dispatcher;
use TNC\EventDispatcher\Interfaces\EndPoint;

abstract class AbstractEndPoint implements EndPoint
{
    /**
     * @var \TNC\EventDispatcher\Dispatcher
     */
    protected $dispatcher;

    /**
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
