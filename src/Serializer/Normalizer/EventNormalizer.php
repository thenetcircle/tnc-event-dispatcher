<?php


namespace Tnc\Service\EventDispatcher\Serializer\Normalizer;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EventNormalizer extends ObjectNormalizer
{
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(new AnnotationReader())
        );

        parent::__construct($classMetadataFactory);
    }
}