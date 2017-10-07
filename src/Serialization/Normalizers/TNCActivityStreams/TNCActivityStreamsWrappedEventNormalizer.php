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

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams;

use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\WrappedEvent;

class TNCActivityStreamsWrappedEventNormalizer extends AbstractNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function normalize($wrappedEvent)
    {
        $data         = $wrappedEvent->getNormalizedEvent();
        $data['verb'] = $wrappedEvent->getEventName();

        $metadata = [
            'mode' => $wrappedEvent->getTransportMode(),
            'class' => $wrappedEvent->getClassName()
        ];

        if (isset($data['context']) && is_array($data['context'])) {
            $data['context']['metadata'] = $metadata;
        }
        else {
            $data['context'] = ['metadata' => $metadata];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        $eventName       = $data['verb'];

        $transportMode   = isset($data['context']['metadata']['mode']) ?
          $data['context']['metadata']['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;

        $className  = isset($data['context']['metadata']['class']) ? $data['context']['metadata']['class'] : '';

        if (isset($data['context']['metadata'])) unset($data['context']['metadata']);

        $normalizedEvent = $data;

        return new WrappedEvent($transportMode, $eventName, $normalizedEvent, $className);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof WrappedEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        return $className == WrappedEvent::class;
    }
}
