<?php

namespace TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams;

use TNC\EventDispatcher\Exception\DenormalizeException;
use TNC\EventDispatcher\Exception\NormalizeException;
use TNC\EventDispatcher\Interfaces\TNCActivityStreamsEvent;
use TNC\EventDispatcher\Serialization\Normalizer\AbstractNormalizer;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\AbstractObject;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Activity;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Attachment;
use TNC\EventDispatcher\Serialization\Normalizers\TNCActivityStreams\Impl\Author;

class TNCActivityStreamsNormalizer extends AbstractNormalizer
{
    protected $activityBuilder = null;

    public function __construct()
    {
        $this->activityBuilder = TNCActivityBuilder::createActivity();
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
        $vars = get_object_vars($this);
        $data = [];

        foreach ($vars as $_key => $_value) {
            if (!empty($_value)) {
                if (is_object($_value)) {
                    $data[$_key] = $this->serializer->normalize($_value);
                }
                elseif (is_array($_value)) {
                    $data[$_key] = $_value;
                }
                else {
                    $data[$_key] = (string)$_value;
                }
            }
        }

        return $data;
    }

    protected function denormalizeActivity(array $data) {
        $classMapping = [
          'actor'     => Actor::class,
          'generator' => Generator::class,
          'object'    => Obj::class,
          'provider'  => Provider::class,
          'target'    => Target::class,
        ];

        foreach ($data as $_key => $_value) {
            if (array_key_exists($_key, $classMapping)) {
                $this->{$_key} = $this->serializer->denormalize($_value, $classMapping[$_key]);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }

    protected function normalizeActivityObject(AbstractObject $activityObject) {
        $vars = get_object_vars($this);
        $data = [];

        foreach ($vars as $_key => $_value) {
            if (!empty($_value)) {
                if ($_key === 'attachments') {
                    $attachments = [];
                    foreach ($_value as $_attachment) {
                        array_push($attachments, $this->serializer->normalize($_attachment));
                    }
                    $data[$_key] = $attachments;
                }
                elseif (is_object($_value)) {
                    $data[$_key] = $this->serializer->normalize($_value);
                }
                elseif (is_array($_value)) {
                    $data[$_key] = $_value;
                }
                else {
                    $data[$_key] = (string)$_value;
                }
            }
        }

        return $data;
    }

    protected function denormalizeActivityObject(array $data) {
        foreach ($data as $_key => $_value) {
            if ($_key === 'attachments') {
                $attachments = [];
                foreach ($_value as $_attachment) {
                    array_push($attachments, $this->serializer->denormalize($_attachment, Attachment::class));
                }
                $this->attachments = $attachments;
            }
            elseif ($_key === 'author') {
                $this->author = $this->serializer->denormalize($_value, Author::class);
            }
            else {
                $this->{$_key} = $_value;
            }
        }
    }
}
