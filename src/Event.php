<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Tnc\Service\EventDispatcher\Serializer\JsonSerializable;
use Tnc\Service\EventDispatcher\Serializer\Serializable;

class Event extends BaseEvent implements Serializable
{
    use JsonSerializable;
}