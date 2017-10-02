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

namespace TNC\EventDispatcher\Tests\Serializer;

use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Interfaces\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder;

class TNCActivityStreamsNormalizerTest extends \PHPUnit_Framework_TestCase
{

}

class TestEvent1 implements TNCActivityStreamsEvent {
    /**
     * {@inheritdoc}
     */
    public function normalize(TNCActivityBuilder $builder)
    {
        // TODO: Implement normalize() method.
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(Activity $activity)
    {
        // TODO: Implement denormalize() method.
    }

    public function getTransportMode()
    {
        return TransportableEvent::TRANSPORT_MODE_ASYNC;
    }
}