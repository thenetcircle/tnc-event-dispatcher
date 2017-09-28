<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Backend;

use TNC\EventDispatcher\Interfaces\Backend;
use TNC\EventDispatcher\Event\Internal\InternalEventProducer;

abstract class AbstractBackend extends InternalEventProducer implements Backend
{
}
