<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\Service\EventDispatcher\Backend;

use TNC\Service\EventDispatcher\Interfaces\Backend;
use TNC\Service\EventDispatcher\Event\Internal\InternalEventProducer;

abstract class AbstractBackend extends InternalEventProducer implements Backend
{
}
