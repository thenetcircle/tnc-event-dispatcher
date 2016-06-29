<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Internal;

use Tnc\Service\EventDispatcher\Exception;
use Tnc\Service\EventDispatcher\LocalDispatcher;

interface InternalEventProducer
{
    /**
     * Sets the dispatcher which will trigger internal events
     *
     * @param LocalDispatcher $dispatcher
     */
    public function setInternalEventDispatcher(LocalDispatcher $dispatcher);
}