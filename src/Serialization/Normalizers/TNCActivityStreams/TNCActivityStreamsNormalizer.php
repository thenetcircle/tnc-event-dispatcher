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
use TNC\EventDispatcher\Interfaces\Event\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Serialization\Normalizers\AbstractNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\ActivityObject;

class TNCActivityStreamsNormalizer extends AbstractNormalizer
{
    /**
     * @var null|\TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\ActivityBuilderInterface
     */
    protected $activityBuilder = null;

    public function __construct(ActivityBuilderInterface $activityBuilder = null)
    {
        $this->activityBuilder = null === $activityBuilder ? new DefaultActivityBuilder() : $activityBuilder;
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
        /** @var Activity $activity */
        $activity = $this->denormalizeActivity($data);

        /** @var TNCActivityStreamsEvent $object */
        $reflectionClass = new \ReflectionClass($className);
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

    /**
     * @param \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity $activity
     *
     * @return array
     */
    protected function normalizeActivity(Activity $activity)
    {
        return $this->getNonEmptyProperties($activity->getAll());
    }

    /**
     * @param array $properties
     *
     * @return array
     */
    protected function getNonEmptyProperties($properties)
    {
        $data = [];

        foreach ($properties as $key => $value) {
            if (
              (is_string($value) && $value == '') ||
              (is_array($value)  && count($value) === 0) ||
              (is_object($value) && !($value instanceof ActivityObject)) ||
              is_null($value)
            ) {
                continue;
            }

            switch (true) {

                case is_array($value):
                    if ($key == 'attachments') {
                        $data[$key] = $this->getNonEmptyProperties($value);
                    }
                    else {
                        $data[$key] = $value;
                    }
                    break;

                case is_object($value):
                    $_data = $this->getNonEmptyProperties($value->getAll());
                    if (count($_data) > 0) {
                        $data[$key] = $_data;
                    }
                    break;

                default:
                    $data[$key] = (string)$value;

            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity
     */
    protected function denormalizeActivity(array $data)
    {
        $builder = new DefaultActivityBuilder();
        $builder->setFromArray($data);
        return $builder->getActivity();
    }
}
