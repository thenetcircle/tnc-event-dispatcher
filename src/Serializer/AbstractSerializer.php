<?php

namespace Tnc\Service\EventDispatcher\Serializer;

use Tnc\Service\EventDispatcher\Normalizer;
use Tnc\Service\EventDispatcher\Serializer;

/**
 * AbstractSerializer
 *
 * @package    Tnc\Service\EventDispatcher
 *
 * @author     The NetCircle
 */
abstract class AbstractSerializer implements Serializer
{
    /**
     * @var Normalizer
     */
    protected $normalizer;

    /**
     * AbstractSerializer constructor.
     *
     * @param Normalizer $normalizer
     */
    public function __construct(Normalizer $normalizer = null)
    {
        $this->normalizer = $normalizer ?: new DefaultNormalizer();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(Normalizable $event)
    {
        $data = $this->normalizer->normalize($event);

        return $this->encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($content, $class)
    {
        $data = $this->decode($content);

        return $this->normalizer->denormalize($data, $class);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    abstract public function encode($data);

    /**
     * @param string $content
     *
     * @return array
     */
    abstract public function decode($content);
}
