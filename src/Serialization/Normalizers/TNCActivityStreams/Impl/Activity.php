<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Contributors:
 *     Beineng Ma <baineng.ma@gmail.com>
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