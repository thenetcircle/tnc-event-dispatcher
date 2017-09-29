<?php

/*
 * This file is part of the Tnc package.
 *
 * (c) Service Team
 *
 * file that was distributed with this source code.
 */

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl;

/**
 * ActivityStreams
 *
 * an activity consists of an actor, a verb, an an object, and a target.
 * It tells the story of a person performing an action on or with an object --
 * "Geraldine posted a photo to her album" or "John shared a video".
 * In most cases these components will be explicit, but they may also be implied.
 *
 * @see     http://activitystrea.ms/specs/json/1.0/
 */
class Activity
{
    /**
     * @var string
     */
    public $version = '1.0';

    /**
     * @var string
     */
    public $id = "";

    /**
     * @var string
     */
    public $verb = "";

    /**
     * @var Actor
     */
    public $actor = null;

    /**
     * @var Obj
     */
    public $object = null;

    /**
     * @var string
     */
    public $provider = "";

    /**
     * @var string
     */
    public $published = "";

    /**
     * @var array
     */
    public $context = [];
}