<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal;

use Tnc\Service\EventDispatcher\Dispatcher;
use Tnc\Service\EventDispatcher\Exception;

interface InternalEventProducer
{
    /**
     * Sets the dispatcher which will trigger internal events
     *
     * @param Dispatcher $dispatcher
     */
    public function setEventDispatcher(Dispatcher $dispatcher);
}