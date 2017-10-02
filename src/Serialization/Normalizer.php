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

namespace TNC\EventDispatcher\Serialization;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Serializer;

interface Normalizer
{
    /**
     * Normalizes the Object to be a semi-result, Then can be using for Formatter
     *
     * @param object $object
     *
     * @return array
     *
     * @throws NormalizeException
     */
    public function normalize($object);

    /**
     * Denormalizes a semi-result to be a Object according to the $className
     *
     * @param array  $data
     * @param string $className
     *
     * @return object
     *
     * @throws DenormalizeException
    */
    public function denormalize($data, $className);

    /**
     * Checks if this Normalizer supports normalization
     *
     * @param object $object
     *
     * @return bool
     */
    public function supportsNormalization($object);

    /**
     * Checks if this Normalizer supports denormalization
     *
     * @param array  $data
     * @param string $className
     *
     * @return bool
     */
    public function supportsDenormalization($data, $className);

    /**
     * Sets the serializer
     *
     * @param \TNC\EventDispatcher\Serializer $serializer
     */
    public function setSerializer(Serializer $serializer);
}
