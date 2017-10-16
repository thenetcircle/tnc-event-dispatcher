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

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Interfaces\Event\TransportableEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\WrappedEvent;

class TNCActivityStreamsWrappedEventNormalizer extends AbstractNormalizer
{
    const CONTAINER_FIELD = 'generator';

    /**
     * {@inheritdoc}
     */
    public function normalize($wrappedEvent)
    {
        $data         = $wrappedEvent->getNormalizedEvent();

        $eventName = $wrappedEvent->getEventName();
        $data['title'] = $eventName;
        if (strpos($eventName, '.') !== false) {
            $data['verb'] = substr(strrchr($eventName, '.'), 1);
        }

        $data['generator'] = [
          'id'      => 'tnc-event-dispatcher',
          'objectType' => 'library',
          'content' => [
            'mode'  => $wrappedEvent->getTransportMode(),
            'class' => $wrappedEvent->getClassName()
          ]
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        if (!isset($data['title'])) {
            throw new DenormalizeException('The field "title" is required.');
        }

        $eventName = $data['title'];
        $metadata  = [];

        if (
          isset($data['generator']) &&
          is_array($data['generator']) &&
          $data['generator']['id'] == 'tnc-event-dispatcher' &&
          isset($data['generator']['content'])
        ) {
            $metadata = $data['generator']['content'];
        }

        $transportMode = isset($metadata['mode']) ? $metadata['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;
        $className     = isset($metadata['class']) ? $metadata['class'] : '';

        return new WrappedEvent($transportMode, $eventName, $data, $className);
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
