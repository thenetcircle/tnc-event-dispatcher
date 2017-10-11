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
        $data['verb'] = $wrappedEvent->getEventName();

        $metadata = [
          'id'      => 'event-dispatcher-metadata',
          'content' => [
            'mode'  => $wrappedEvent->getTransportMode(),
            'class' => $wrappedEvent->getClassName()
          ]
        ];

        if (isset($data[self::CONTAINER_FIELD])) {

            if (isset($data[self::CONTAINER_FIELD]['attachments'])) {
                $data[self::CONTAINER_FIELD]['attachments'][] = $metadata;
            } else {
                $data[self::CONTAINER_FIELD]['attachments'] = [$metadata];
            }

        } else {
            $data[self::CONTAINER_FIELD] = [
              'attachments' => [$metadata]
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $className)
    {
        if (!isset($data['verb'])) {
            throw new DenormalizeException('The field "verb" is required.');
        }

        $eventName = $data['verb'];
        $metadata  = [];

        if (
          isset($data[self::CONTAINER_FIELD]['attachments']) &&
          is_array($attachments = $data[self::CONTAINER_FIELD]['attachments']) &&
          count($attachments) > 0
        ) {
            foreach ($attachments as $attachment) {
                if (is_array($attachment) && isset($attachment['id']) && $attachment['id'] == 'event-dispatcher-metadata') {
                    $metadata = $attachment;
                    break;
                }
            }
        }

        $transportMode = isset($metadata['content']['mode']) ? $metadata['content']['mode'] : TransportableEvent::TRANSPORT_MODE_ASYNC;
        $className     = isset($metadata['content']['class']) ? $metadata['content']['class'] : '';

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
