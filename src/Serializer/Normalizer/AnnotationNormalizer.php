<?php

namespace Tnc\Service\EventDispatcher\Serializer\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Tnc\Service\EventDispatcher\Event;
use Tnc\Service\EventDispatcher\Exception\InvalidArgumentException;
use Tnc\Service\EventDispatcher\Serializer\Annotation;
use Tnc\Service\EventDispatcher\Serializer\Normalizer;
use Tnc\Service\EventDispatcher\Serializer\Normalizer\ActivityStreamsNormalizer;

class AnnotationNormalizer implements Normalizer
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader = null)
    {
        $this->reader = $reader ?: new AnnotationReader();
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    public function normalize(Event $event)
    {
        $reflectionEvent = new \ReflectionClass($event);

        if(null === ($annot = $this->reader->getClassAnnotation($reflectionEvent, Annotation\Normalizer::class))) {
            $annot = new Annotation\Normalizer();
        }

        $data = array();
        switch($annot->type)
        {
            case Annotation\Normalizer::ACTIVITY_STREAMS:
                $normalizer = new ActivityStreamsNormalizer($this->reader);
                $data = $normalizer->normalize($reflectionEvent);
                break;

            case Annotation\Normalizer::NORMAL:
                break;

            default:
                throw new InvalidArgumentException('Invalid Normalizer Type.');
        }

        $data['extra'] = [
            'class' => $reflectionEvent->getName()
        ];

        return $this->encoder->encode($data);
    }

    /**
     * @param array $data
     *
     * @return Event
     *
     * @throws InvalidArgumentException
     */
    public function denormalize($data)
    {
        $data = $this->encoder->decode($content);

        if(!isset($data['extra']['class']) || !class_exists($data['extra']['class'])) {
            throw new InvalidArgumentException('Can not do unserialize, Because class is missed.');
        }

        $reflectionEvent = new \ReflectionClass($data['extra']['class']);

        if(null === ($annot = $this->reader->getClassAnnotation($reflectionEvent, Annotation\Normalizer::class))) {
            $annot = new Annotation\Normalizer();
        }
    }
}