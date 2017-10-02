<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Interfaces;

use TNC\EventDispatcher\Dispatcher;

interface Receiver
{
    /**
     * @param \TNC\EventDispatcher\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher);
}
