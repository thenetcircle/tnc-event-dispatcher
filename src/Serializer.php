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

namespace TNC\EventDispatcher;

use TNC\EventDispatcher\Exceptions\DenormalizeException;
use TNC\EventDispatcher\Exceptions\FormatException;
use TNC\EventDispatcher\Exceptions\NoAvailableNormalizerException;
use TNC\EventDispatcher\Exceptions\NormalizeException;
use TNC\EventDispatcher\Exceptions\UnformatException;
use TNC\EventDispatcher\Serialization\Normalizer;

/**
 * AbstractSerializer
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
class Serializer
{
    protected $supportedNormalizers = array();
    protected $formatter            = null;

    /**
     * AbstractSerializer constructor.
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer[]    $supportedNormalizers
     * @param \TNC\EventDispatcher\Serialization\Formatter       $formatter
     */
    public function __construct(array $supportedNormalizers, $formatter)
    {
        foreach ($supportedNormalizers as $normalizer) {
            $this->supportedNormalizers[] = $normalizer->withSerializer($this);
        }

        $this->formatter            = $formatter;
    }

    /**
     * @param object $object
     *
     * @return string
     *
     * @throws FormatException
     * @throws NoAvailableNormalizerException
     * @throws NormalizeException
     */
    public function serialize($object)
    {
        return $this->format(
          $this->normalize($object)
        );
    }

    /**
     * @param string $data
     * @param string $className
     *
     * @return object
     *
     * @throws UnformatException
     * @throws NoAvailableNormalizerException
     * @throws DenormalizeException
     */
    public function unserialize($data, $className)
    {
        return $this->denormalize(
          $this->unformat($data),
          $className
        );
    }

    /**
     * @param $object
     *
     * @return array
     *
     * @throws NoAvailableNormalizerException
     * @throws NormalizeException
     */
    public function normalize($object)
    {
        if (null === ($normalizer = $this->getNormalizer($object))) {
            throw new NoAvailableNormalizerException(
                sprintf('Could not normalize object of class %s, No normalizer found!', get_class($object))
            );
        }

        return $normalizer->normalize($object);
    }

    /**
     * @param $data
     * @param $className
     *
     * @return object
     *
     * @throws NoAvailableNormalizerException
     * @throws DenormalizeException
     */
    public function denormalize($data, $className)
    {
        if (null === ($normalizer = $this->getDenormalizer($data, $className))) {
            throw new NoAvailableNormalizerException(
                sprintf('Could not denormalize object of class %s, No normalizer found!', $className)
            );
        }

        return $normalizer->denormalize($data, $className);
    }

    /**
     * Formats a semi-result
     *
     * @param array $data
     *
     * @return string formatted data
     *
     * @throws FormatException
     */
    public function format($data) {
        return $this->formatter->format($data);
    }

    /**
     * Unformats a result to be a semi-result
     *
     * @param string $formattedData
     *
     * @return array data
     *
     * @throws UnformatException
     */
    public function unformat($formattedData) {
        return $this->formatter->unformat($formattedData);
    }

    /**
     * Prepends a new supported Normalizer
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer $normalizer
     *
     * @return $this
     */
    public function prependNormalizer(Normalizer $normalizer) {
        array_unshift($this->supportedNormalizers, $normalizer->withSerializer($this));
        return $this;
    }

    /**
     * Appends a new supported Normalizer
     *
     * @param \TNC\EventDispatcher\Serialization\Normalizer $normalizer
     *
     * @return $this
     */
    public function appendNormalizer(Normalizer $normalizer) {
        array_push($this->supportedNormalizers, $normalizer->withSerializer($this));
        return $this;
    }


    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizer|null
     */
    public function getNormalizer($object)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
            if (
                $normalizer instanceof Normalizer
                && $normalizer->supportsNormalization($object)
            ) {
                return $normalizer;
            }
        }

        return null;
    }

    /**
     * @return \TNC\EventDispatcher\Serialization\Normalizer|null
     */
    public function getDenormalizer($data, $className)
    {
        foreach ($this->supportedNormalizers as $normalizer) {
            if (
                $normalizer instanceof Normalizer
                && $normalizer->supportsDenormalization($data, $className)
            ) {
                return $normalizer;
            }
        }

        return null;
    }
}
