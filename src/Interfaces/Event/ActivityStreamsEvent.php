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

namespace TNC\EventDispatcher\Interfaces\Event;

interface ActivityStreamsEvent extends TransportableEvent
{
    /**
     * Normalizes a Event to be a Activity
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\ActivityBuilder $builder
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\Activity
     *
     * @throws \TNC\EventDispatcher\Exception\NormalizeException
     */
    public function normalize($builder);

    /**
     * Denormalizes a Acitivty to be a Event
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizers\ActivityStreams\Impl\Activity $activity
     *
     * @throws \TNC\EventDispatcher\Exception\DenormalizeException
     */
    public function denormalize($activity);
}