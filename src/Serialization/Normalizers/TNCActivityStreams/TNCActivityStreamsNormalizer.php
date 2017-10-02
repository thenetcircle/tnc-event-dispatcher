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
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;

class TNCActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * @var null|\TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\TNCActivityBuilder
     */
    protected $activityBuilder = null;

    public function __construct()
    {
        $this->activityBuilder = new TNCActivityBuilder();
    }

    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param TNCActivityStreamsEvent $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($object)
    {
        $activity = $object->normalize($this->activityBuilder);

        return $this->normalizeActivity($activity);
    }

    /**
     * Denormalizes a semi-result to be a Object according to the $className
     *
     * @param array  $data
     * @param string $className
     *
     * @return TNCActivityStreamsEvent
     *
     * @throws DenormalizeException
     */
    public function denormalize($data, $className)
    {
        $reflectionClass = new \ReflectionClass($className);

        /** @var Activity $activity */
        $activity = $this->denormalizeActivity($data);

        /** @var TNCActivityStreamsEvent $object */
        $object = $reflectionClass->newInstanceWithoutConstructor();
        $object->denormalize($activity);
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($object)
    {
        return ($object instanceof TNCActivityStreamsEvent);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, TNCActivityStreamsEvent::class);
    }

    protected function normalizeActivity(Activity $activity) {
        $data = [];

        $properties = get_object_vars($activity);
        foreach ($properties as $key => $value) {
            if (!empty($value)) {
                switch(true) {
                    case is_object($value):
                        if ($key == 'actor') {

                        }
                        elseif ($key == 'object') {

                        }
                        break;

                    case is_array($value):
                        $data[$key] = $value;
                        break;
                    default:
                        $data[$key] = (string)$value;
                }
            }
        }

        return $data;
    }

    protected function denormalizeActivity(array $data) {
        $activity = new Activity();


        foreach ($data as $key => $value) {
            switch (true) {
                case $key == 'actor':
                    break;
                case $key == 'object':
                    break;
                case $key == 'context':
                    break;
                default:
                    $activity->{$key} = $value;
            }
        }

        return $activity;
    }
}
