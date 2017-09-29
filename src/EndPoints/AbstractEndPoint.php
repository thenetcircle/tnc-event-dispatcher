<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\EndPoints;

use TNC\EventDispatcher\Interfaces\EndPoint;
use TNC\EventDispatcher\Event\Internal\InternalEventProducer;

abstract class AbstractEndPoint extends InternalEventProducer implements EndPoint
{
}
