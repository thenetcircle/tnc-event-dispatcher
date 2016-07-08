<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace Tnc\Service\EventDispatcher\Backend;

use Tnc\Service\EventDispatcher\Backend;
use Tnc\Service\EventDispatcher\Exception;
use Tnc\Service\EventDispatcher\ExternalDispatcher;

abstract class AbstractBackend implements Backend
{
    /**
     * @var ExternalDispatcher
     */
    protected $eventDispatcher;

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(ExternalDispatcher $externalDispatcher)
    {
        $this->eventDispatcher = $externalDispatcher;
    }
}
