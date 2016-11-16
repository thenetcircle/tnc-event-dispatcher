<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Backend;

use Tnc\Service\EventDispatcher\Interfaces\Backend;
use Tnc\Service\EventDispatcher\Event\Internal\InternalEventProducer;

abstract class AbstractBackend extends InternalEventProducer implements Backend
{
}
