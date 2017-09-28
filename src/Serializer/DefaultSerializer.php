<?php

namespace TNC\EventDispatcher\Serializer;

use TNC\EventDispatcher\Exception\InvalidArgumentException;
use TNC\EventDispatcher\Normalizer\ActivityStreamsNormalizer;
use TNC\EventDispatcher\Normalizer\CustomNormalizer;

/**
 * DefaultSerializer
 *
 * @package    TNC\EventDispatcher
 *
 * @author     The NetCircle
 */
class DefaultSerializer extends AbstractSerializer
{
    /**
     * AbstractSerializer constructor.
     *
     * @param \TNC\EventDispatcher\Interfaces\Normalizer[] $normalizers
     */
    public function __construct(array $normalizers = null)
    {
        if ($normalizers === null) {
            $normalizers = [new ActivityStreamsNormalizer(), new CustomNormalizer()];
        }

        parent::__construct($normalizers);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function decode($content)
    {
        if (null === ($data = json_decode($content, true))) {
            throw new InvalidArgumentException(sprintf('String %s can not be decoded.', $content));
        }

        return $data;
    }
}
