<?php

namespace Tnc\Service\EventDispatcher\Serializer\Normalizer;

use Doctrine\Common\Annotations\Reader;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Serializer\Annotation\ActivityStreams\Actor;

class ActivityStreamsNormalizer
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array
     */
    public function normalize(\ReflectionClass $class)
    {
        $name = $class->getName();

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $name) {
                continue;
            }

            foreach($this->reader->getPropertyAnnotations($property) as $annot)
            {
                if ($annot instanceof Actor) {
                    $propertyMetadata->sinceVersion = $annot->version;
                }
            }
        }
    }

    /**
     * @param array $data
     *
     * @return Event
    */
    public function denormalize($data)
    {

    }
}
